<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<style>
             body {
                background-image: url('BG.jpg'); 
                background-size: cover; /* Cover the entire page */
                background-position: center; /* Center the image */
                color: white; /* Text color */
                font-family: Arial, sans-serif; /* Font style */
                display: flex; /* Enable flexbox layout */
                justify-content: center; /* Center content horizontally */
                align-items: center; /* Center content vertically */
                min-height: 100vh; /* Full viewport height */
                margin: 0; /* Remove default margin */
                flex-direction: column; /* Stack elements vertically */
            }
            .not-logged-in {
                background-color: #221c1b;
                color: white; /* White text */
                padding: 15px; /* Padding around the message */
                border-radius: 10px; /* Rounded corners */
                text-align: center; /* Center the text */
                font-size: 18px; /* Increase font size */
                margin: 20px auto; /* Center the message on the page */
                max-width: 400px; /* Maximum width */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for depth */
                transition: opacity 0.5s ease; /* Fade in/out effect */
            }

            .not-logged-in {
                animation: fadeIn 0.5s forwards; /* Add fade-in animation */
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }
                to {
                    opacity: 1;
                }
            }
            .login-button {
                display: inline-block; /* Make it an inline-block for padding */
                padding: 10px 20px; /* Button padding */
                background-color: #ff4400; /* Button color */
                color: white; /* Text color */
                border: none; /* Remove border */
                border-radius: 5px; /* Rounded corners */
                font-size: 16px; /* Font size */
                cursor: pointer; /* Pointer cursor */
                text-decoration: none; /* Remove underline */
                transition: background-color 0.3s; /* Transition effect */
                margin-top: 15px; /* Space above the button */
            }
            .login-button:hover {
                background-color: #000000; /* Darker shade on hover */
            }
          </style>";

    echo "<p class='not-logged-in'>User not logged in.</p>";
    echo "<a href='login.html' class='login-button'>Go to Login</a>";
    exit();
}

$user_id = $_SESSION['user_id'];

require '../vendor/autoload.php';

$client = new MongoDB\Client("URL_TO_YOUR_MONGODB_SERVER");
$collection = $client->library_db->user_books;

$message = "";

// Delete book logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_book'])) {
    $delete_id = $_POST['delete_id'] ?? null;

    if ($delete_id) {
        $result = $collection->deleteOne([
            '_id' => new MongoDB\BSON\ObjectId($delete_id),
            'user_id' => $user_id
        ]);

        if ($result->getDeletedCount() > 0) {
            $message = "<p class='message success'>Book deleted successfully.</p>";
        } else {
            $message = "<p class='message error'>Failed to delete the book.</p>";
        }
    } else {
        $message = "<p class='message error'>Invalid book ID.</p>";
    }
}

$booksCursor = $collection->find(['user_id' => $user_id]);

$books = iterator_to_array($booksCursor);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIBRARY</title>
    <link rel="stylesheet" type="text/css" href="user.css">
    <style>
        .table-container,
        .stats-container {
            margin-top: 70px;
            overflow-x: auto;
        }
        .table-container table,
        .stats-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        }
        .table-container th, 
        .table-container td,
        .stats-container th,
        .stats-container td {
            border: 1px solid #F4A261;
            padding: 10px;
            text-align: center;
        }
        .table-container th,
        .stats-container th {
            background-color: #F4A261;
            color: white;
        }
        .table-container h2,
        .stats-container h2 {
            color: #fff;
            background-color: #ff4400;
            padding: 15px;
            margin: 0;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 0px solid #05566A;
            font-size: 1.5em;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .table-container table tr:hover,
        .stats-container table tr:hover {
            background-color: orange;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <nav>
        <label class="logo">LIBRARY</label>
        <ul>
            <li><a href="Home.html">Home</a></li>
            <li class="home"><a href="seebook_user.php">Books</a></li>
            <li><a href="logout.php">Sign Out</a></li>
        </ul>
    </nav>

    <div class="table-container">
        <?php
        // Display messages
        if (!empty($message)) {
            echo "<div class='auto-hide-message " . (strpos($message, 'error') !== false ? 'error' : 'success') . "'>$message</div>";
        }

        // Check if books exist for the user
        if (empty($books)) {
            echo "<p class='message no-books'>No books found.</p>"; 
        } else {
            echo '<table>
                    <tr>
                        <th>BOOK NUMBER</th>
                        <th>BOOK NAME</th>
                        <th>AUTHOR</th>
                        <th>PUBLISHER</th>
                        <th>PDF</th>
                        <th>Action</th>
                    </tr>';

            foreach ($books as $book) {
                $pdf_path = "pdf/book_" . htmlspecialchars($book['book_number']) . ".pdf";
                echo "<tr>
                        <td>" . htmlspecialchars($book['book_number']) . "</td>
                        <td>" . htmlspecialchars($book['book_name']) . "</td>
                        <td>" . htmlspecialchars($book['author']) . "</td>
                        <td>" . htmlspecialchars($book['publisher']) . "</td>
                        <td>
                            <a href='$pdf_path' target='_blank'>Open PDF</a>
                        </td>
                        <td>
                            <form action='" . $_SERVER['PHP_SELF'] . "' method='post'>
                                <input type='hidden' name='delete_id' value='" . htmlspecialchars($book['_id']) . "'>
                                <button type='submit' name='delete_book'>Delete</button>
                            </form>
                        </td>
                    </tr>";  
            }
            echo "</table>";
        }
        ?>
    </div>

    <div class="container3">
        <form action="seebook_user.php" method="post">
            <button class="seebutton" name="seebook" type="submit">See book collection</button>
        </form>
    </div>

    <script>
        setTimeout(function() {
            const messages = document.querySelectorAll('.auto-hide-message');
            messages.forEach(function(message) {
                message.style.display = 'none';
            });
        }, 5000); 
    </script>
</body>
</html>
