<?php
ob_start(); // Start output buffering

include('../includes/connect.php');
//session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit();
}

// Get the user ID from the URL
if (isset($_GET['chat'])) {
    $user_id = $_GET['chat'];

    // Fetch user details using prepared statements
    $user_query = "SELECT * FROM user_table WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $user_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $user_result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($user_result);

    // If the user does not exist, redirect
    if (!$user) {
        echo "<script>window.location.href = 'dashboard.php';</script>";
        exit();
    }

    // Fetch chat messages between admin and this user using prepared statements
    $messages_query = "SELECT * FROM chat_messages WHERE (sender = 'admin' AND receiver_id = ?) OR (sender = ? AND receiver_id = 'admin') ORDER BY message_time ASC";
    $stmt = mysqli_prepare($con, $messages_query);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $user_id);
    mysqli_stmt_execute($stmt);
    $messages_result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($user['username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
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
    <h3 class="text-center text-success">Chat with <?php echo htmlspecialchars($user['username']); ?></h3>

    <!-- Chat Messages -->
    <div class="card mb-4">
        <div class="card-body" id="chat-container" style="height: 300px; overflow-y: auto;">
            <?php while ($message = mysqli_fetch_assoc($messages_result)): ?>
                <div class="chat-message <?php echo $message['sender'] == 'admin' ? 'admin' : 'user'; ?>">
                    <strong><?php echo $message['sender'] == 'admin' ? 'Admin' : htmlspecialchars($user['username']); ?>:</strong>
                    <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                    <small><?php echo $message['message_time']; ?></small>
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
$(document).ready(function() {
    // Handle form submission via AJAX
    $('#chat-form').submit(function(event) {
        event.preventDefault(); // Prevent form from submitting normally

        var message = $('#message').val();
        var user_id = <?php echo $user_id; ?>;

        // Send the message using AJAX
        $.ajax({
            url: 'send_message.php', // PHP file to handle message sending
            type: 'POST',
            data: { message: message, user_id: user_id },
            success: function(response) {
                var data = JSON.parse(response);

                if (data.status == 'success') {
                    // Clear the input field
                    $('#message').val('');

                    // Append the new message to the chat container
                    var newMessages = '';
                    data.messages.forEach(function(message) {
                        newMessages += '<div class="chat-message ' + (message.sender == 'admin' ? 'admin' : 'user') + '">';
                        newMessages += '<strong>' + (message.sender == 'admin' ? 'Admin' : '<?php echo $user["username"]; ?>') + ':</strong>';
                        newMessages += '<p>' + message.message + '</p>';
                        newMessages += '<small>' + message.message_time + '</small>';
                        newMessages += '</div>';
                    });

                    // Update the chat container with the new messages
                    $('#chat-container').html(newMessages);

                    // Scroll to the bottom of the chat container
                    $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);
                } else {
                    alert('Failed to send message: ' + data.message); // Show the error message
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX error: ' + error); // If AJAX request fails
            }
        });
    });
});

</script>

</body>
</html>

<?php
ob_end_flush(); // End output buffering and flush the output
