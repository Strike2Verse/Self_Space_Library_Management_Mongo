<?php
session_start();

require '../vendor/autoload.php';

$client = new MongoDB\Client("URL_TO_YOUR_MONGODB_SERVER");
$database = $client->library_db;
$books_collection = $database->book_collection;
$user_books_collection = $database->user_books;

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_id'])) {
    $book_id = (int) trim($_POST['book_id']);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($user_id === null) {
        $error_message = "User not logged in.";
    } else {
        $book = $books_collection->findOne(['bnum' => $book_id]);

        if ($book) {
            $book_name = isset($book['bname']) ? $book['bname'] : 'Unknown';
            $author = isset($book['author']['name']) ? $book['author']['name'] : 'Unknown';
            $publisher = isset($book['pname']) ? $book['pname'] : 'Unknown';

            try {
                $result = $user_books_collection->insertOne([
                    'user_id' => $user_id,
                    'book_number' => $book_id,
                    'book_name' => $book_name,
                    'author' => $author,
                    'publisher' => $publisher
                ]);

                if ($result->getInsertedCount() > 0) {
                    $success_message = "Book added successfully.";
                } else {
                    $error_message = "Error adding book.";
                }
            } catch (MongoDB\Driver\Exception\Exception $e) {
                $error_message = "Error: " . $e->getMessage();
            }
        } else {
            $error_message = "Book not found.";
        }
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';

$filter = [];
if (!empty($search)) {
    $filter = [
        '$or' => [
            ['bname' => new MongoDB\BSON\Regex($search, 'i')],
            ['aname' => new MongoDB\BSON\Regex($search, 'i')],
            ['pname' => new MongoDB\BSON\Regex($search, 'i')]
        ]
    ];
}

$options = [];
if ($sort) {
    switch ($sort) {
        case 'bname_asc':
            $options['sort'] = ['bname' => 1];
            break;
        case 'bname_desc':
            $options['sort'] = ['bname' => -1];
            break;
        case 'bnum_asc':
            $options['sort'] = ['bnum' => 1];
            break;
        case 'bnum_desc':
            $options['sort'] = ['bnum' => -1];
            break;
    }
}

$books = $books_collection->find($filter, $options);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIBRARY</title>
    <link rel="stylesheet" href="bookCollection.css" />
    <style>
        .search-sort {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-sort form {
            display: inline-block;
            margin-right: 20px;
        }
        .search-sort label {
            margin-right: 15px;
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        .search-sort input[type="text"],
        .search-sort select {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
        }
        .search-sort input[type="text"]:focus,
        .search-sort select:focus {
            border-color: #007bff;
        }
        .search-sort input[type="submit"] {
            padding: 5px 10px;
            background-color: #007bff;
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 4px;
        }
        .search-sort input[type="submit"]:hover {
            background-color: orange;
        }

        .success-message {
            color: green;
            margin-bottom: 10px;
            text-align: center;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }

        .go-to-inventory-btn,
        .go-back-btn {
            padding: 8px 16px;
            background-color: #ff7500;
            border: none;
            color: #fff;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            cursor: pointer;    
        }
        .go-to-inventory-btn:hover,
        .go-back-btn:hover {
            background-color: orange;
        }

        input[type="submit"].add-btn {
            padding: 5px 10px;
            background-color: #28a745;
            border: none;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"].add-btn:hover {
            background-color: orange;
        }

        input[type="submit"],
        .go-to-inventory-btn,
        .go-back-btn {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        input[type="submit"]:hover,
        .go-to-inventory-btn:hover,
        .go-back-btn:hover {
            background-color: orange;
            transform: scale(1.05);
        }

        tr {
            transition: background-color 0.2s ease;
        }

        tr:hover {
            background-color: orange;
        }
    </style>
</head>

<body>
    <nav>
        <label class="logo">LIBRARY</label>
        <ul>
            <li><a href="Home.html">Home</a></li>
            <li class="home"><a href="bookCollection.php">Books</a></li>
            <li class="profile"><a href="user.php">Profile</a></li>
            <li><a href="login.php">Logout</a></li>
        </ul>
    </nav>    

    <div class="cen">
        <div class="search-sort">
            <form action="seebook_user.php" method="GET">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search">
                <input type="submit" value="Search">
            </form>
            <form action="seebook_user.php" method="GET">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort">
                    <option value="">-- Select --</option>
                    <option value="bname_asc">Book Name (A-Z)</option>
                    <option value="bname_desc">Book Name (Z-A)</option>
                    <option value="bnum_asc">Book Number (Ascending)</option>
                    <option value="bnum_desc">Book Number (Descending)</option>
                </select>
                <input type="submit" value="Sort">
            </form>
        </div>

        <?php if ($success_message): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <a href="user.php" class="go-to-inventory-btn">Go to Inventory</a>

        <?php if (!empty($search)): ?>
            <a href="seebook_user.php" class="go-back-btn">Go Back</a>
        <?php endif; ?>

        <div class="table-container">
            <?php
            if ($books->isDead()) {
                echo "<p>No results found.</p>";
            } else {
                echo '<table>
                        <tr>
                            <th>BOOK NUMBER</th>
                            <th>BOOK NAME</th>
                            <th>AUTHOR</th>
                            <th>PUBLISHER</th>
                            <th>Add to Inventory</th>
                        </tr>';
                foreach ($books as $book) {
                    $book_name = isset($book['bname']) ? htmlspecialchars($book['bname']) : 'Unknown';
                    $author = isset($book['author']['name']) ? htmlspecialchars($book['author']['name']) : 'Unknown';  
                    $publisher = isset($book['pname']) ? htmlspecialchars($book['pname']) : 'Unknown';
                    $book_number = htmlspecialchars($book['bnum']);

                    echo "<tr>
                            <td>$book_number</td>
                            <td>$book_name</td>
                            <td>$author</td>
                            <td>$publisher</td>
                            <td>
                                <form action='' method='POST'>
                                    <input type='hidden' name='book_id' value='$book_number'>
                                    <input type='submit' value='Add' class='add-btn'>
                                </form>
                            </td>
                          </tr>";
                }
                echo '</table>';
            }
            ?>
        </div>
    </div>
</body>
</html>
