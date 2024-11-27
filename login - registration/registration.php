<?php include 'session_reset.php'; ?>

<?php
require '../vendor/autoload.php';

$client = new MongoDB\Client("URL_TO_YOUR_MONGODB_SERVER");
$database = $client->library_db;
$users_collection = $database->users;
$admins_collection = $database->admins;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number = trim($_POST['phone_number']);
    $role = $_POST['role'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($phone_number) || empty($role)) {
        echo "All fields are required.";
        exit();
    }

    if ($password != $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    if (!preg_match('/^\d{10}$/', $phone_number)) {
        echo "Invalid phone number. It must be 10 digits.";
        exit();
    }

    $existingUser = $users_collection->findOne([
        '$or' => [
            ['username' => $username],
            ['email' => $email]
        ]
    ]);
    $existingAdmin = $admins_collection->findOne([
        '$or' => [
            ['username' => $username],
            ['email' => $email]
        ]
    ]);

    if ($existingUser || $existingAdmin) {
        echo "Username or email already exists. Please choose a different one.";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $document = [
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password,
        'phone_number' => $phone_number,
        'role' => $role,
    ];

    try {
        if ($role == 'user') {
            $result = $users_collection->insertOne($document);
        } elseif ($role == 'admin') {
            $result = $admins_collection->insertOne($document);
        }

        if (isset($result) && $result->getInsertedCount() > 0) {
            header("Location: login.html");
            exit();
        } else {
            echo "Error: Could not register user.";
        }
    } catch (Exception $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>
