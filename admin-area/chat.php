<?php
ob_start();
include('../includes/connect.php');

// Ensure the admin is logged in
if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit();
}

// Get the user ID
if (isset($_GET['chat'])) {
    $user_id = $_GET['chat'];

    // Fetch user details
    $user_query = "SELECT * FROM user_table WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $user_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $user_result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($user_result);

    if (!$user) {
        echo "<script>window.location.href = 'dashboard.php';</script>";
        exit();
    }

    // Fetch messages
    $messages_query = "
        SELECT 
            'chat_messages' AS source_table,
            message_id AS id,
            message AS message_content,
            message_date,
            admin_read AS is_read,
            sent_by_user
        FROM chat_messages
        WHERE user_id = ?

        UNION ALL

        SELECT 
            'contact_support' AS source_table,
            support_id AS id,
            message AS message_content,
            message_date,
            support_read AS is_read,
            sent_by_user
        FROM contact_support
        WHERE user_id = ?

        ORDER BY message_date ASC
    ";
    $stmt = mysqli_prepare($con, $messages_query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
    mysqli_stmt_execute($stmt);
    $messages_result = mysqli_stmt_get_result($stmt);

    // Mark all unread messages as read
    // Update unread messages in chat_messages
    $update_query1 = "UPDATE chat_messages SET admin_read = 1 WHERE user_id = ? AND admin_read = 0";
    $stmt1 = mysqli_prepare($con, $update_query1);
    mysqli_stmt_bind_param($stmt1, "i", $user_id);
    mysqli_stmt_execute($stmt1);

    // Update unread messages in contact_support
    $update_query2 = "UPDATE contact_support SET support_read = 1 WHERE user_id = ? AND support_read = 0";
    $stmt2 = mysqli_prepare($con, $update_query2);
    mysqli_stmt_bind_param($stmt2, "i", $user_id);
    mysqli_stmt_execute($stmt2);

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($user['username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .chat-message {
            margin-bottom: 15px;
        }
        .chat-message.admin {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
        }
        .chat-message.user {
            background-color: #e2ffe2;
            padding: 10px;
            border-radius: 5px;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-success">Chat with <?php echo htmlspecialchars($user['username']); ?></h3>
        <a href="dashboard.php?chat_channels" class="btn btn-secondary">Back to Chat Channels</a>
    </div>
    <!-- Chat Messages -->
    <div class="card mb-4">
        <div class="card-body" id="chat-container" style="height: 300px; overflow-y: auto;">
            <?php while ($message = mysqli_fetch_assoc($messages_result)): ?>
                <div class="chat-message <?php echo $message['sent_by_user'] == 1 ? 'user' : 'admin'; ?>" 
                     data-id="<?php echo $message['id']; ?>" 
                     <?php if ($message['is_read'] == 0 && $message['sent_by_user'] == 0) echo 'id="first-unread"'; ?>>
                    <strong><?php echo $message['sent_by_user'] == 1 ? htmlspecialchars($user['username']) : 'Admin'; ?>:</strong>
                    <p><?php echo nl2br(htmlspecialchars($message['message_content'])); ?></p>
                    <small><?php echo $message['message_date']; ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Send New Message Form -->
    <form id="chat-form">
        <div class="mb-3">
            <textarea name="message" id="message" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

<script>
$(document).ready(function () {
    // Scroll to the first unread message or the bottom of the chat container
    const firstUnread = $('#first-unread');
    if (firstUnread.length) {
        firstUnread[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);
    }

    // Handle form submission via AJAX
    $('#chat-form').submit(function (event) {
        event.preventDefault(); // Prevent form from submitting normally

        const message = $('#message').val();
        const user_id = <?php echo $user_id; ?>;

        $.ajax({
            url: 'send_message.php', // PHP file to handle message sending
            type: 'POST',
            data: { message: message, user_id: user_id },
            success: function (response) {
                const data = JSON.parse(response);

                if (data.status === 'success') {
                    // Clear the input field
                    $('#message').val('');

                    // Update the chat container with the new messages
                    let newMessages = '';
                    data.messages.forEach(function (message) {
                        const isUser = message.sent_by_user == 1;
                        newMessages += `
                            <div class="chat-message ${isUser ? 'user' : 'admin'}">
                                <strong>${isUser ? '<?php echo $user["username"]; ?>' : 'Admin'}:</strong>
                                <p>${message.message}</p>
                                <small>${message.message_date}</small>
                            </div>
                        `;
                    });

                    $('#chat-container').html(newMessages);

                    // Scroll to the bottom of the chat container
                    $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);
                } else {
                    alert('Error: ' + data.message);
                }
            },
            error: function (xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    });
});

</script>

</body>
</html>
<?php ob_end_flush(); ?>
