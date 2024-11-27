<?php
session_start();

require '../vendor/autoload.php';

$client = new MongoDB\Client("URL_TO_YOUR_MONGODB_SERVER"); 
$database = $client->library_db;
$admins_collection = $database->admins;
$users_collection = $database->users;
$logs_collection = $database->user_logs;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $admin_query = $admins_collection->findOne(['username' => $username]);

    if ($admin_query) {
        $user = $admin_query;
        $is_admin = true;
        $role = 'admin';
    } else {
        $user_query = $users_collection->findOne(['username' => $username]);

        if ($user_query) {
            $user = $user_query;
            $is_admin = false;
            $role = 'user';
        } else {
            header("Location: login.html?error=invalid_credentials");
            exit();
        }
    }   

    if (password_verify($password, $user['password'])) {
        if ($is_admin) {
            $_SESSION["admin_id"] = (string) $user['_id'];
            $_SESSION["admin_username"] = $user['username'];
        } else {
            $_SESSION["user_id"] = (string) $user['_id'];
            $_SESSION["user_username"] = $user['username'];
        }

        $_SESSION['role'] = $role;

        $action = "login";
        $logs_collection->insertOne([
            'user_id' => (string) $user['_id'],
            'action' => $action,
            'timestamp' => new MongoDB\BSON\UTCDateTime()
        ]);

        if ($is_admin) {
            header("Location: admin.php");
        } else {
            header("Location: user.php");
        }
        exit();
    } else {
        header("Location: login.html?error=invalid_credentials");
        exit();
    }
} else {
    header("Location: login.html");
    exit();
}
?>
