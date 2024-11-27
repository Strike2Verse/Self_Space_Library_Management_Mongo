<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. Only admins can perform this action.";
    exit();
}

require '../vendor/autoload.php';

$client = new MongoDB\Client("URL_TO_YOUR_MONGODB_SERVER");
$database = $client->library_db;
$books_collection = $database->book_collection;
$log_collection = $database->added_books_log;

$message = "";

function getMimeType($filePath) {
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
    ];

    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    return $mimeTypes[$ext] ?? 'application/octet-stream';
}

if (isset($_POST["addbook"])) {
    $bnum = (int)trim($_POST['bnum']);
    $bname = trim($_POST['bname']);
    $aname = trim($_POST['aname']);
    $pname = trim($_POST['pname']);
    $added_by = $_SESSION['admin_id'];

    if (empty($bnum) || empty($bname) || empty($aname) || empty($pname)) {
        $message = "All fields are required.";
    } else {
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['pdf']['tmp_name'];
            $fileName = basename($_FILES['pdf']['name']);
            $fileSize = $_FILES['pdf']['size'];
            $filePath = "pdf/" . $fileName;
            $fileType = getMimeType($filePath);
            $allowedTypes = ['application/pdf'];
            $maxFileSize = 10 * 1024 * 1024;

            if (!in_array($fileType, $allowedTypes)) {
                $message = "Invalid file type. Only PDF files are allowed.";
            } elseif ($fileSize > $maxFileSize) {
                $message = "File size exceeds the 10MB limit.";
            } else {
                if (move_uploaded_file($fileTmpPath, $filePath)) {
                    try {
                        $bookDocument = [
                            'bnum' => $bnum,
                            'bname' => $bname,
                            'author' => ['name' => $aname],
                            'pname' => $pname,
                            'pdf_path' => $filePath
                        ];

                        $logDocument = [
                            'bnum' => $bnum,
                            'bname' => $bname,
                            'author' => ['name' => $aname],
                            'pname' => $pname,
                            'pdf_path' => $filePath,
                            'added_by' => $added_by,
                            'timestamp' => new MongoDB\BSON\UTCDateTime()
                        ];

                        $bookResult = $books_collection->insertOne($bookDocument);
                        $logResult = $log_collection->insertOne($logDocument);

                        if ($bookResult->getInsertedCount() > 0 && $logResult->getInsertedCount() > 0) {
                            $message = "New record added successfully!";
                        } else {
                            $message = "Error adding record.";
                        }
                    } catch (Exception $e) {
                        $message = "Error: " . htmlspecialchars($e->getMessage());
                    }
                } else {
                    $message = "Failed to upload file.";
                }
            }
        } else {
            $message = "No file was uploaded or there was an upload error.";
        }
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
    <link rel="stylesheet" href="addbook.css" />
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
        <p>Please browse your localhost MongoDB Compass<br>
           to view the updated data.
        </p>
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
