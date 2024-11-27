<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.html");
    exit();
}

require '../vendor/autoload.php';

$client = new MongoDB\Client("URL_TO_YOUR_MONGODB_SERVER");
$database = $client->library_db;
$books_collection = $database->book_collection;
$log_collection = $database->deleted_books_log;

$message = "";

if (isset($_POST["delbook"])) {
    $bnum = (int)$_POST['bnum'];
    $deleted_by = $_SESSION['admin_id'];

    try {
        $book = $books_collection->findOne(['bnum' => $bnum]);

        if ($book) {
            $book_name = $book['bname'] ?? 'Unknown';
            $author = $book['author']['name'] ?? 'Unknown';
            $publisher = $book['pname'] ?? 'Unknown';

            $deleteResult = $books_collection->deleteOne(['bnum' => $bnum]);

            if ($deleteResult->getDeletedCount() > 0) {
                $message = "Record deleted successfully";

                $logDocument = [
                    'book_number' => $bnum,
                    'book_name' => $book_name,
                    'author' => $author,
                    'publisher' => $publisher,
                    'deleted_by' => $deleted_by,
                    'deleted_date' => new MongoDB\BSON\UTCDateTime()
                ];
                $log_collection->insertOne($logDocument);
            } else {
                $message = "Error deleting record.";
            }
        } else {
            $message = "Error fetching book details: Book not found.";
        }
    } catch (Exception $e) {
        $message = "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIBRARY</title>
    <link rel="stylesheet" href="delbook.css" />
</head>

<body>
    <nav>
        <label class="logo">LIBRARY</label>
        <ul>
            <li><a href="Home.html">Home</a></li>
            <li><a href="bookCollection.php">Books</a></li>
        </ul>
    </nav>

    <img src="db.png" alt="Dustbin Icon" class="icon">

    <div class="msg">
        <h3><?php echo htmlspecialchars($message); ?><br></h3>
    </div>

    <div class="blurrct"></div>

    <button class="back" type="button" onClick="formto()">Back</button>

</body>

<script>
    function formto() {
        window.location.href = "admin.php";
    }
</script>

</html>
