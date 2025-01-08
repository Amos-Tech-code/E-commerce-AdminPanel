<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Channels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .channel-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .channel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        .channel-card .card-body {
            padding: 20px;
        }

        .channel-card h5 {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .channel-card p {
            font-size: 1rem;
            color: #6c757d;
        }

        .channel-card .badge {
            background-color: #ff5733;
            color: white;
        }

        .channel-card .btn-start-chat {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .channel-card .btn-start-chat:hover {
            background-color: #0056b3;
        }

        .search-bar input {
            border-radius: 30px;
            padding-left: 20px;
            font-size: 1rem;
        }

        .search-bar button {
            border-radius: 30px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .channel-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .channel-card .icon-unread {
            margin-right: 10px;
            color: #ff5733;
        }
    </style>
</head>
<body>

<div class="container mt-5 pt-4">
    <h3 class="text-center text-primary">Chat Channels</h3>
    <p class="text-center text-muted mb-4">Select a user to start chatting or search by User ID</p>

    <!-- Search Form -->
    <form id="search-form" class="mb-4 search-bar">
        <div class="input-group">
            <input 
                type="text" 
                id="search-input" 
                name="search_user_id" 
                class="form-control" 
                placeholder="Search by User ID" 
                aria-label="Search by User ID">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <!-- List of Channels (Users) -->
    <div id="channel-list" class="channel-list">
        <!-- The user list will be populated here -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        // Function to load the chat channels list
        function loadChannels(searchUserId = '') {
            $.ajax({
                url: 'fetch_channels.php', // Server-side script to fetch the channels
                type: 'GET',
                data: { search_user_id: searchUserId },
                success: function(response) {
                    $('#channel-list').html(response); // Update the channel list with the response
                },
                error: function(xhr, status, error) {
                    console.log('Error fetching channels:', error);
                }
            });
        }

        // Initial load
        loadChannels();

        // Set an interval to refresh the chat channels list every 5 seconds
        setInterval(function() {
            loadChannels($('#search-input').val()); // Reload channels, optionally with search filter
        }, 5000); // 5000 milliseconds = 5 seconds

        // Handle search form submission
        $('#search-form').submit(function(event) {
            event.preventDefault(); // Prevent form from submitting normally
            const searchUserId = $('#search-input').val();
            loadChannels(searchUserId); // Reload channels based on the search input
        });
    });
</script>

</body>
</html>
