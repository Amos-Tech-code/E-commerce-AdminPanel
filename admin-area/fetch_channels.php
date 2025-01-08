<?php
include('../includes/connect.php');

// Initialize search filter
$search_user_id = isset($_GET['search_user_id']) ? mysqli_real_escape_string($con, $_GET['search_user_id']) : '';

// Query to fetch users with messages in chat_messages or contact_support
$query = "
    SELECT 
        u.user_id, 
        u.username,
        
        -- Count unread chat messages where admin_read = 0, considering distinct chat_message IDs to avoid duplication
        COALESCE((
            SELECT COUNT(*) 
            FROM chat_messages cm 
            WHERE cm.user_id = u.user_id AND cm.admin_read = 0
        ), 0) AS unread_chat_messages,
        
        -- Count unread contact support messages where support_read = 0, considering distinct support_message IDs to avoid duplication
        COALESCE((
            SELECT COUNT(*) 
            FROM contact_support cs 
            WHERE cs.user_id = u.user_id AND cs.support_read = 0
        ), 0) AS unread_support_messages
        
    FROM user_table u
";

// Apply search filter if provided (it will include users with or without messages)
if ($search_user_id) {
    $query .= " WHERE u.user_id LIKE '%$search_user_id%'";
} else {
    // By default, only users with messages will be included (in chat_messages or contact_support)
    $query .= " WHERE EXISTS (
        SELECT 1 FROM chat_messages cm WHERE cm.user_id = u.user_id
    ) OR EXISTS (
        SELECT 1 FROM contact_support cs WHERE cs.user_id = u.user_id
    )";
}

$query .= " GROUP BY u.user_id";

// Execute the query
$result = mysqli_query($con, $query);

// Return the list of channels
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate total unread messages (chat + support)
        $unread_count = $row['unread_chat_messages'] + $row['unread_support_messages'];
        
        // Display user as a card with gradient background
        echo '<div class="channel-card">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . htmlspecialchars($row['username']) . ' (ID: ' . $row['user_id'] . ')</h5>';
        
        if ($unread_count > 0) {
            echo '<span class="badge badge-unread"><i class="bi bi-envelope"></i> ' . $unread_count . ' unread</span>';
        }
        
        echo '<a href="dashboard.php?chat=' . $row['user_id'] . '" class="btn-start-chat">Start Chat</a>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<p class="text-center text-danger">No users found.</p>';
}
?>
