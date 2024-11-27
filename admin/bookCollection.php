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
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .search-sort form {
            display: inline-block;
            margin-right: 20px;
        }
        .search-sort label {
            margin-right: 15px;
        }
        .search-sort input[type="text"],
        .search-sort select {
            padding: 5px;
            font-size: 14px;
        }
        .search-sort input[type="submit"] {
            padding: 5px 10px;
            background-color: #007bff;
            border: none;
            color: #fff;
            cursor: pointer;
        }
        .search-sort input[type="submit"]:hover {
            background-color: orange;
        }

        .button-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px; 
        }

        .go-back-btn {
            padding: 8px 16px;
            background-color: #ff7500;
            border: none;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }

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
    <?php
    session_start();
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

    
    $aggregationPipeline = [
        ['$group' => ['_id' => '$author.name', 'count' => ['$sum' => 1]]],
        ['$sort' => ['count' => -1]]
    ];
    $authorStatsCursor = $collection->aggregate($aggregationPipeline);
    $authorStats = iterator_to_array($authorStatsCursor);
    ?>

    <nav>
        <label class="logo">LIBRARY</label>
        <ul>
            <li><a href="Home.html">Home</a></li>
            <li class="home"><a href="Home.php">Books</a></li>
            <?php
           
            if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
                $profilePage = isset($_SESSION['admin_id']) ? 'admin.php' : 'user.php';
                echo '<li><a href="' . $profilePage . '">Profile</a></li>';
                echo '<li><a href="logout.php">Logout</a></li>';
            } else {
                echo '<li><a href="login.html">Login</a></li>';
            }
            ?>
        </ul>
    </nav>    

    <div class="cen">
        <div class="search-sort">
            <form action="bookCollection.php" method="GET">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <input type="submit" value="Search">
            </form>
            <form action="bookCollection.php" method="GET">
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
        
        <div class="button-container">
        <?php if (!empty($search)): ?>
            <a href="bookCollection.php" class="go-back-btn">Go Back</a>
        <?php endif; ?>
        </div>

       
        <div class="table-container">
            <h2>BOOK COLLECTION</h2>
            <?php
            if (empty($books)) {
                echo "<p>No results found.</p>";
            } else {
                echo '<table>
                        <tr>
                            <th>BOOK NUMBER</th>
                            <th>BOOK NAME</th>
                            <th>AUTHOR</th>
                            <th>PUBLISHER</th>
                        </tr>';
                foreach ($books as $book) {
                    echo "<tr>
                            <td>".(isset($book['bnum']) ? htmlspecialchars($book['bnum']) : '')."</td>
                            <td>".(isset($book['bname']) ? htmlspecialchars($book['bname']) : '')."</td>
                            <td>".(isset($book['author']['name']) ? htmlspecialchars($book['author']['name']) : '')."</td>
                            <td>".(isset($book['pname']) ? htmlspecialchars($book['pname']) : '')."</td>
                        </tr>";
                }
                echo "</table>";
            }
            ?>
        </div>
            
       
            <div class="table-container">
                <h2>AUTHOR STATISTICS</h2>
                <?php
                if (empty($authorStats)) {
                    echo "<p>No author statistics available.</p>";
                } else {
                    echo '<table>
                            <tr>
                                <th>AUTHOR</th>
                                <th>NUMBER OF BOOKS</th>
                            </tr>';
                    foreach ($authorStats as $stat) {
                        echo "<tr>
                                <td>".(isset($stat['_id']) ? htmlspecialchars($stat['_id']) : '')."</td>
                                <td>".(isset($stat['count']) ? htmlspecialchars($stat['count']) : '')."</td>
                            </tr>";
                    }
                    echo "</table>";
                }
                ?>
            </div>
    </div>
</body>
</html>
