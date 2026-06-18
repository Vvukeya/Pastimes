<?php
// Admin Chat Monitor - View and reply to all user chats
session_start();
require_once '../config/database.php';
/** @var mysqli $conn */
require_once '../includes/auth.php';
requireAdmin();

$success = '';
$error = '';

// Get all conversations
$conversations = getAllConversations($conn);

// Get selected conversation
$selected_user1 = isset($_GET['user1']) ? intval($_GET['user1']) : 0;
$selected_user2 = isset($_GET['user2']) ? intval($_GET['user2']) : 0;

$messages = [];
$current_conversation = null;

if ($selected_user1 > 0 && $selected_user2 > 0) {
    $messages = getConversationMessages($conn, $selected_user1, $selected_user2);
    
    // Get user info for the conversation
    $user_sql = "SELECT * FROM tblUser WHERE user_id IN (?, ?)";
    $user_stmt = mysqli_prepare($conn, $user_sql);
    mysqli_stmt_bind_param($user_stmt, "ii", $selected_user1, $selected_user2);
    mysqli_stmt_execute($user_stmt);
    $users_result = mysqli_stmt_get_result($user_stmt);
    while ($user = mysqli_fetch_assoc($users_result)) {
        if ($user['user_id'] == $selected_user1) {
            $current_conversation['user1'] = $user;
        } else {
            $current_conversation['user2'] = $user;
        }
    }
}

// Handle sending admin reply with file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $message_id = intval($_POST['message_id']);
    $reply_text = sanitizeInput($_POST['reply_text']);
    $user1_id = intval($_POST['user1_id']);
    $user2_id = intval($_POST['user2_id']);
    
    $file_name = null;
    $file_path = null;
    $file_type = null;
    $file_size = null;
    
    // Handle file upload
    if (isset($_FILES['reply_file']) && $_FILES['reply_file']['error'] == UPLOAD_ERR_OK) {
        $upload_result = uploadChatFile($_FILES['reply_file']);
        if ($upload_result['success']) {
            $file_name = $upload_result['file_name'];
            $file_path = $upload_result['file_path'];
            $file_type = $upload_result['file_type'];
            $file_size = $upload_result['file_size'];
        } else {
            $error = $upload_result['error'];
        }
    }
    
    if (empty($reply_text) && !$file_name) {
        $error = 'Please enter a reply or attach a file';
    } elseif (empty($error)) {
        // This will create both admin_reply record AND a message in tblMessages
        if (sendAdminReply($conn, $message_id, $_SESSION['user_id'], $reply_text, $file_name, $file_path, $file_type, $file_size)) {
            $success = 'Reply sent successfully! The user will see this message in their dashboard.';
            // Mark original message as read
            markMessageAsRead($conn, $message_id);
            // Refresh messages
            $messages = getConversationMessages($conn, $user1_id, $user2_id);
        } else {
            $error = 'Failed to send reply';
        }
    }
}

// Mark all messages in selected conversation as read
if ($selected_user1 > 0 && $selected_user2 > 0 && isset($_GET['mark_read'])) {
    $sql = "UPDATE tblMessages SET is_read = 1 WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $selected_user1, $selected_user2, $selected_user2, $selected_user1);
    mysqli_stmt_execute($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Monitor - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .admin-wrapper { display: flex; }
        .admin-sidebar { width: 280px; background: #1a1a2e; color: white; min-height: 100vh; position: fixed; }
        .admin-sidebar .logo { padding: 24px; font-size: 24px; font-weight: 800; text-align: center; }
        .admin-sidebar .logo span:first-child { color: var(--gold); }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: #aaa; text-decoration: none; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: var(--pastime-green); color: white; }
        .admin-main { flex: 1; margin-left: 280px; }
        .admin-header { background: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; }
        .admin-content { padding: 24px; }
        
        /* Chat Layout */
        .chat-monitor-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            height: calc(100vh - 150px);
        }
        
        /* Conversations Sidebar */
        .conversations-list {
            background: var(--warm-beige);
            border-right: 1px solid var(--light-grey);
            overflow-y: auto;
        }
        
        .conversations-list-header {
            padding: 15px;
            background: var(--warm-beige);
            border-bottom: 1px solid var(--light-grey);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .conversation-item {
            padding: 16px;
            border-bottom: 1px solid var(--light-grey);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }
        
        .conversation-item:hover {
            background: rgba(46, 125, 50, 0.1);
        }
        
        .conversation-item.active {
            background: rgba(46, 125, 50, 0.2);
            border-left: 3px solid var(--pastime-green);
        }
        
        .conversation-avatar {
            width: 48px;
            height: 48px;
            background: var(--pastime-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .conversation-info {
            flex: 1;
            min-width: 0;
        }
        
        .conversation-name {
            font-weight: 600;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .conversation-last {
            font-size: 12px;
            color: var(--grey);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }
        
        .conversation-time {
            font-size: 11px;
            color: var(--grey);
            flex-shrink: 0;
        }
        
        .unread-badge {
            background: var(--pastime-green);
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
        }
        
        /* Chat Messages Area */
        .chat-area {
            display: flex;
            flex-direction: column;
            background: white;
            height: 100%;
        }
        
        .chat-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--light-grey);
            background: white;
            flex-shrink: 0;
        }
        
        .chat-header h3 {
            font-size: 16px;
            margin-bottom: 4px;
        }
        
        .chat-header .chat-subtitle {
            font-size: 12px;
            color: var(--grey);
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #fafafa;
        }
        
        .message {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }
        
        .message-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }
        
        .message-user i {
            font-size: 24px;
            color: var(--pastime-green);
        }
        
        .message-user strong {
            font-size: 14px;
        }
        
        .message-time {
            font-size: 11px;
            color: var(--grey);
            margin-left: auto;
        }
        
        .message-bubble {
            background: white;
            padding: 12px 16px;
            border-radius: 12px;
            max-width: 80%;
            margin-left: 34px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .message-bubble.user-message {
            background: #E3F2FD;
            border-left: 3px solid #2196F3;
        }
        
        .admin-reply {
            background: #E8F5E9;
            border-left: 3px solid var(--pastime-green);
            margin-top: 10px;
            margin-left: 34px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            max-width: 80%;
        }
        
        .admin-reply-label {
            font-size: 11px;
            color: var(--pastime-green);
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        /* File Attachment Styles */
        .file-attachment {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f0f0f0;
            padding: 8px 12px;
            border-radius: 8px;
            margin-top: 8px;
            text-decoration: none;
            color: var(--charcoal);
            transition: all 0.3s ease;
        }
        
        .file-attachment:hover {
            background: #e0e0e0;
        }
        
        .file-attachment i {
            font-size: 20px;
            color: var(--pastime-green);
        }
        
        .file-info {
            flex: 1;
        }
        
        .file-name {
            font-size: 13px;
            font-weight: 500;
        }
        
        .file-size {
            font-size: 10px;
            color: var(--grey);
        }
        
        /* Reply Form */
        .reply-form-container {
            padding: 16px 20px;
            border-top: 1px solid var(--light-grey);
            background: white;
            flex-shrink: 0;
        }
        
        .reply-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .reply-input-group {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }
        
        .reply-textarea {
            flex: 1;
            padding: 12px;
            border: 1px solid var(--light-grey);
            border-radius: var(--radius);
            resize: vertical;
            font-family: inherit;
            font-size: 14px;
            min-height: 60px;
        }
        
        .reply-textarea:focus {
            outline: none;
            border-color: var(--pastime-green);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }
        
        .file-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .file-label {
            background: var(--warm-beige);
            padding: 8px 16px;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        
        .file-label:hover {
            background: var(--light-grey);
        }
        
        .selected-file {
            font-size: 12px;
            color: var(--grey);
        }
        
        .btn-primary, .btn-outline {
            padding: 8px 20px;
            border-radius: var(--radius);
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: var(--pastime-green);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--pastime-green-dark);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--pastime-green);
            border: 1px solid var(--pastime-green);
        }
        
        .btn-outline:hover {
            background: var(--pastime-green);
            color: white;
        }
        
        .success-message, .error-message {
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .success-message {
            background: #e8f5e9;
            color: #4caf50;
            border-left: 4px solid #4caf50;
        }
        
        .error-message {
            background: #ffebee;
            color: #f44336;
            border-left: 4px solid #f44336;
        }
        
        .no-conversation {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--grey);
            text-align: center;
            flex-direction: column;
            gap: 15px;
            padding: 40px;
        }
        
        .no-conversation i {
            font-size: 64px;
            color: #ddd;
        }
        
        .no-conversation h3 {
            font-size: 20px;
        }
        
        .no-conversation p {
            font-size: 14px;
            max-width: 400px;
        }
        
        .refresh-btn {
            background: var(--warm-beige);
            border: none;
            padding: 8px 16px;
            border-radius: var(--radius);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            background: var(--light-grey);
        }
        
        .badge-admin {
            background: var(--pastime-green);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        /* Scrollbar Styling */
        .chat-messages::-webkit-scrollbar,
        .conversations-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .chat-messages::-webkit-scrollbar-track,
        .conversations-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .chat-messages::-webkit-scrollbar-thumb,
        .conversations-list::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        
        .chat-messages::-webkit-scrollbar-thumb:hover,
        .conversations-list::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }
        
        @media (max-width: 1024px) {
            .chat-monitor-container {
                grid-template-columns: 280px 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .chat-monitor-container {
                grid-template-columns: 1fr;
                height: auto;
            }
            .conversations-list {
                max-height: 300px;
                border-right: none;
                border-bottom: 1px solid var(--light-grey);
            }
            .admin-sidebar {
                width: 70px;
            }
            .admin-sidebar .logo span:last-child,
            .admin-sidebar nav a span:last-child {
                display: none;
            }
            .admin-main {
                margin-left: 70px;
            }
            .message-bubble,
            .admin-reply {
                max-width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="logo"><span>PAST</span><span>IMES</span></div>
            <nav>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-tshirt"></i> Products</a>
                <a href="pending-approvals.php"><i class="fas fa-clock"></i> Pending</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="users.php"><i class="fas fa-users"></i> Users</a>
                <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
                <a href="chat-monitor.php" class="active"><i class="fas fa-comments"></i> Chat Monitor</a>
                <hr>
                <a href="../index.php"><i class="fas fa-store"></i> View Store</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-comments"></i> Chat Monitor</h1>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="refresh-btn" onclick="location.reload();">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </span>
                    <span class="badge-admin">
                        <i class="fas fa-users"></i> Monitor All Conversations
                    </span>
                </div>
            </div>
            
            <div class="admin-content">
                <?php if ($success): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <div class="chat-monitor-container">
                    <!-- Conversations Sidebar -->
                    <div class="conversations-list">
                        <div class="conversations-list-header">
                            <h3><i class="fas fa-comments"></i> All Conversations</h3>
                            <p style="font-size: 12px; color: var(--grey); margin-top: 4px;">Click any conversation to view and reply</p>
                        </div>
                        <?php if (mysqli_num_rows($conversations) > 0): ?>
                            <?php while ($conv = mysqli_fetch_assoc($conversations)): 
                                // Check if there are unread messages in this conversation
                                $unread_sql = "SELECT COUNT(*) as unread FROM tblMessages WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) AND is_read = 0 AND receiver_id != " . $_SESSION['user_id'];
                                $unread_stmt = mysqli_prepare($conn, $unread_sql);
                                mysqli_stmt_bind_param($unread_stmt, "iiii", $conv['user1_id'], $conv['user2_id'], $conv['user2_id'], $conv['user1_id']);
                                mysqli_stmt_execute($unread_stmt);
                                $unread_result = mysqli_stmt_get_result($unread_stmt);
                                $unread_data = mysqli_fetch_assoc($unread_result);
                                $unread_count = $unread_data['unread'] ?? 0;
                                
                                $is_active = ($selected_user1 == $conv['user1_id'] && $selected_user2 == $conv['user2_id']);
                            ?>
                                <a href="?user1=<?php echo $conv['user1_id']; ?>&user2=<?php echo $conv['user2_id']; ?>&mark_read=1" 
                                   class="conversation-item <?php echo $is_active ? 'active' : ''; ?>">
                                    <div class="conversation-avatar">
                                        <i class="fas fa-user-friends"></i>
                                    </div>
                                    <div class="conversation-info">
                                        <div class="conversation-name">
                                            <?php echo htmlspecialchars($conv['user1_first'] . ' vs ' . $conv['user2_first']); ?>
                                            <?php if ($unread_count > 0): ?>
                                                <span class="unread-badge"><?php echo $unread_count; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="conversation-last">
                                            <?php 
                                            $last_msg = htmlspecialchars(substr($conv['last_message'] ?? '', 0, 50));
                                            echo $last_msg ?: 'No messages yet';
                                            ?>
                                        </div>
                                    </div>
                                    <div class="conversation-time">
                                        <?php echo date('M d', strtotime($conv['last_message_time'])); ?>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="padding: 40px 20px; text-align: center; color: var(--grey);">
                                <i class="fas fa-comments" style="font-size: 48px; margin-bottom: 15px; color: #ddd;"></i>
                                <p style="font-weight: 500;">No conversations yet</p>
                                <p style="font-size: 13px; margin-top: 8px;">When users message each other, their conversations will appear here.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Chat Area -->
                    <div class="chat-area">
                        <?php if ($selected_user1 > 0 && $selected_user2 > 0 && $current_conversation): ?>
                            <div class="chat-header">
                                <h3>
                                    <i class="fas fa-comment-dots" style="color: var(--pastime-green);"></i> 
                                    Conversation between 
                                    <span style="color: var(--pastime-green); font-weight: 600;">
                                        <?php echo htmlspecialchars($current_conversation['user1']['name'] . ' (@' . $current_conversation['user1']['username'] . ')'); ?>
                                    </span> 
                                    and 
                                    <span style="color: var(--pastime-green); font-weight: 600;">
                                        <?php echo htmlspecialchars($current_conversation['user2']['name'] . ' (@' . $current_conversation['user2']['username'] . ')'); ?>
                                    </span>
                                </h3>
                                <p class="chat-subtitle">
                                    <i class="fas fa-info-circle"></i> As an admin, you can view all messages and reply to this conversation. Your replies will appear to both users.
                                </p>
                            </div>
                            
                            <div class="chat-messages" id="chatMessages">
                                <?php if (mysqli_num_rows($messages) > 0): ?>
                                    <?php while ($msg = mysqli_fetch_assoc($messages)): ?>
                                        <div class="message">
                                            <div class="message-user">
                                                <i class="fas fa-user-circle"></i>
                                                <strong><?php echo htmlspecialchars($msg['sender_name']); ?></strong>
                                                <?php if ($msg['is_admin_reply'] == 1): ?>
                                                    <span style="background: #FF9800; color: white; padding: 1px 10px; border-radius: 12px; font-size: 10px; font-weight: 600;">
                                                        <i class="fas fa-star"></i> Admin
                                                    </span>
                                                <?php endif; ?>
                                                <span class="message-time"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></span>
                                                <?php if ($msg['is_read'] == 1 && $msg['sender_id'] != $_SESSION['user_id']): ?>
                                                    <span style="font-size: 10px; color: var(--grey);"><i class="fas fa-check-double"></i> read</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="message-bubble user-message">
                                                <?php if (!empty($msg['message_text'])): ?>
                                                    <p><?php echo nl2br(htmlspecialchars($msg['message_text'])); ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($msg['file_path'])): ?>
                                                    <a href="../<?php echo $msg['file_path']; ?>" target="_blank" class="file-attachment">
                                                        <i class="fas <?php echo getFileIcon($msg['file_type']); ?>"></i>
                                                        <div class="file-info">
                                                            <div class="file-name"><?php echo htmlspecialchars($msg['file_name']); ?></div>
                                                            <div class="file-size"><?php echo formatFileSize($msg['file_size']); ?></div>
                                                        </div>
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if (!empty($msg['admin_reply_text'])): ?>
                                                <div class="admin-reply">
                                                    <div class="admin-reply-label">
                                                        <i class="fas fa-user-shield"></i> Admin Response:
                                                    </div>
                                                    <p><?php echo nl2br(htmlspecialchars($msg['admin_reply_text'])); ?></p>
                                                    <?php if (!empty($msg['admin_file_path'])): ?>
                                                        <a href="../<?php echo $msg['admin_file_path']; ?>" target="_blank" class="file-attachment" style="background: white;">
                                                            <i class="fas <?php echo getFileIcon($msg['admin_file_type']); ?>"></i>
                                                            <div class="file-info">
                                                                <div class="file-name"><?php echo htmlspecialchars($msg['admin_file_name']); ?></div>
                                                            </div>
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="no-conversation">
                                        <i class="fas fa-comment-dots"></i>
                                        <h3>No messages yet</h3>
                                        <p>This conversation is empty. When users send messages, they will appear here.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Admin Reply Form -->
                            <div class="reply-form-container">
                                <form method="POST" action="" enctype="multipart/form-data" class="reply-form">
                                    <input type="hidden" name="message_id" value="<?php 
                                        $last_msg = null;
                                        if (mysqli_num_rows($messages) > 0) {
                                            mysqli_data_seek($messages, 0);
                                            while ($m = mysqli_fetch_assoc($messages)) {
                                                $last_msg = $m;
                                            }
                                            mysqli_data_seek($messages, 0);
                                        }
                                        echo $last_msg ? $last_msg['message_id'] : 0;
                                    ?>">
                                    <input type="hidden" name="user1_id" value="<?php echo $selected_user1; ?>">
                                    <input type="hidden" name="user2_id" value="<?php echo $selected_user2; ?>">
                                    
                                    <div class="reply-input-group">
                                        <textarea name="reply_text" class="reply-textarea" rows="3" placeholder="Type your admin reply here..."></textarea>
                                    </div>
                                    
                                    <div class="file-input-group">
                                        <label class="file-label">
                                            <i class="fas fa-paperclip"></i> Attach File
                                            <input type="file" name="reply_file" style="display: none;" onchange="updateFileName(this)">
                                        </label>
                                        <span id="selectedFileName" class="selected-file">No file selected (max 5MB)</span>
                                    </div>
                                    
                                    <button type="submit" name="send_reply" class="btn-primary" style="align-self: flex-start;">
                                        <i class="fas fa-paper-plane"></i> Send Admin Reply
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="no-conversation">
                                <i class="fas fa-comments"></i>
                                <h3>Select a conversation</h3>
                                <p>Choose a conversation from the left panel to view messages and reply as an admin.</p>
                                <p style="font-size: 13px; color: #aaa;">As an admin, you can monitor all user-to-user conversations and respond on behalf of the platform.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Auto-scroll to bottom of chat
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function updateFileName(input) {
            const fileNameSpan = document.getElementById('selectedFileName');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileSizeMB = file.size / (1024 * 1024);
                if (fileSizeMB > 5) {
                    alert('File too large! Maximum size is 5MB.');
                    input.value = '';
                    fileNameSpan.textContent = 'No file selected (max 5MB)';
                } else {
                    fileNameSpan.textContent = file.name + ' (' + fileSizeMB.toFixed(2) + ' MB)';
                }
            } else {
                fileNameSpan.textContent = 'No file selected (max 5MB)';
            }
        }
        
        // Auto-refresh every 30 seconds to show new messages
        let refreshInterval = setInterval(function() {
            if (document.querySelector('.chat-area')) {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>