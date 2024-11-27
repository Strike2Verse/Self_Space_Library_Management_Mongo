<?php
session_start();


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Expires: 0");
header("Pragma: no-cache");


if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
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
                opacity: 0; /* Start hidden */
                animation: floatIn 0.5s forwards; /* Float-in animation */
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
                opacity: 0; /* Start hidden */
                animation: floatIn 0.5s forwards; /* Float-in animation */
                animation-delay: 0.5s; /* Delay the button's animation */
            }
            .login-button:hover {
                background-color: #000000; /* Darker shade on hover */
            }

            /* Floating animation */
            @keyframes floatIn {
                0% {
                    opacity: 0; /* Start hidden */
                    transform: translateY(30px); /* Start below */
                }
                100% {
                    opacity: 1; /* Fully visible */
                    transform: translateY(0); /* End at normal position */
                }
            }
          </style>";

    
    echo "<div class='not-logged-in'>You are not logged in.</div>";
    echo "<a href='login.html' class='login-button'>Go to Login</a>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIBRARY</title>
    <link rel="stylesheet" href="admin.css" />
</head>

<body>
    <nav>
        <label class="logo">LIBRARY</label>
        <ul>
            <li><a href="Home.html">Home</a></li>
            <li><a href="bookCollection.php">Books</a></li>
            <li><a href="logout.php">Sign Out</a></li> 
        </ul>
    </nav>

    <div class="container">
        <div class="textpara">
            <p>Add New Book:</p>
        </div>
        <form action="addbook.php" method="post" enctype="multipart/form-data">
            <input type="text" class="formco" placeholder="Book Number" id="bnum" name="bnum" required>
            <input type="text" class="formco" placeholder="Book Name" id="bname" name="bname" required>
            <input type="text" class="formco" placeholder="Author Name" id="aname" name="aname" required>
            <input type="text" class="formco" placeholder="Publisher Name" id="pname" name="pname" required>
            <input type="file" class="pdf-upload" name="pdf" id="pdf" required>
            <button class="addbutton" name="addbook" type="submit">Add Book</button>
        </form>

        <div class="textpara">
            <p>Delete a Book:</p>
        </div>
        <form action="delbook.php" method="post">
            <input type="text" class="delbook" placeholder="Book Number" id="bnum" name="bnum" required>
            <button class="delbutton" name="delbook" type="submit">Delete Book</button>
        </form>

        <div class="textpara">
            <p>See Book Collection:</p>
        </div>
        <form action="bookCollection.php" method="post">
            <button class="seebutton" name="book_c" type="submit">See Book Collection</button>
        </form>
    </div>

    
    <div class="floating-book left"></div>
    <div class="floating-book right"></div>
</body>
</html>
