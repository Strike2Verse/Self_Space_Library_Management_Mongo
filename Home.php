<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIBRARY</title>
    <link rel="stylesheet" href="bookCollection.css" />
    <style>
        body {
            transition: background-color 0.5s ease;
        }
        .logo {
            font-size: 25px;
            color: white;
            transition: transform 0.3s ease, color 0.3s ease;
        }
        .logo:hover {
            transform: scale(1.1);
            color: #fff;
        }
        .search-sort {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }
        .search-sort:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .table-container {
            margin-top: 40px;
            overflow-x: auto;
            max-height: 600px;
            animation: fadeIn 0.5s ease-in;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
            transition: opacity 0.5s ease-in;
            opacity: 0;
        }
        .table-container table.show {
            opacity: 1;
        }
        .table-container th, .table-container td {
            border: 1px solid #05566A;
            padding: 10px;
            text-align: center;
            transition: background-color 0.3s;
        }
        .table-container h2 {
            color: #ffffff;
            background-color: #eb6f2c;
            padding: 15px;
            margin: 0;
            text-align: center;
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 100;
            font-size: 1.5em;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }
        .table-scroll {
            overflow-y: auto;
            max-height: 500px;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <?php
    // MongoDB connection
    require '../vendor/autoload.php';
    $client = new MongoDB\Client("URL_TO_YOUR_MONGODB_SERVER");
    $collection = $client->library_db->book_collection;

    $filter = [];
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $_GET['search'];
        $filter = [
            '$or' => [
                ['bname' => new MongoDB\BSON\Regex($search, 'i')],
                ['author.name' => new MongoDB\BSON\Regex($search, 'i')],
                ['pname' => new MongoDB\BSON\Regex($search, 'i')]
            ]
        ];
    }

    $sort = [];
    if (isset($_GET['sort'])) {
        $sort_option = $_GET['sort'];
        switch ($sort_option) {
            case 'bname_asc':
                $sort = ['bname' => 1];
                break;
            case 'bname_desc':
                $sort = ['bname' => -1];
                break;
            case 'bnum_asc':
                $sort = ['bnum' => 1];
                break;
            case 'bnum_desc':
                $sort = ['bnum' => -1];
                break;
        }
    }

    $booksCursor = $collection->find($filter, ['sort' => $sort]);
    $books = iterator_to_array($booksCursor);
    ?>

    <nav>
        <label class="logo">LIBRARY</label>
        <ul>
            <li><a href="Home.html">Home</a></li>
            <li class="home"><a href="Home.php">Books</a></li>
            <li class="registration"><a href="registration.html">Register</a></li>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<li><a href="user.php">Profile</a></li>';
                echo '<li><a href="logout.php">Logout</a></li>';
            } else {
                echo '<li><a href="login.html">Login</a></li>';
            }
            ?>
        </ul>
    </nav>    

    <div class="cen">
        <div class="search-sort">
            <form action="Home.php" method="GET">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <input type="submit" value="Search">
            </form>
            <form action="Home.php" method="GET">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort">
                    <option value="">-- Select --</option>
                    <option value="bname_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'bname_asc') ? 'selected' : ''; ?>>Book Name (A-Z)</option>
                    <option value="bname_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'bname_desc') ? 'selected' : ''; ?>>Book Name (Z-A)</option>
                    <option value="bnum_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'bnum_asc') ? 'selected' : ''; ?>>Book Number (Ascending)</option>
                    <option value="bnum_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'bnum_desc') ? 'selected' : ''; ?>>Book Number (Descending)</option>
                </select>
                <input type="submit" value="Sort">
            </form>
        </div>

        <div class="table-container">
            <h2>Book Collection</h2>
            <table>
                <tr>
                    <th>Book Number</th>
                    <th>Book Name</th>
                    <th>Author</th>
                    <th>Publisher</th>
                </tr>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['bnum']); ?></td>
                        <td><?php echo htmlspecialchars($book['bname']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']['name']); ?></td>
                        <td><?php echo htmlspecialchars($book['pname']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const table = document.querySelector('.table-container table');
            if (table) {
                table.classList.add('show');
            }
        });
    </script>
</body>
</html>
