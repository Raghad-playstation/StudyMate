
<?php
require_once "db_connect.php";

// Only process registration forms
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.html");
    exit;
}

// Get the form information
$name = trim($_POST["fullname"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";

// Check required fields
if ($name === "" || $email === "" ||
    $password === "" || $confirm_password === "") {
    die("Please fill in all fields.");
}

// Check full name
if (mb_strlen($name) < 3) {
    die("Full name must be at least 3 characters.");
}

// Check email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

// Check password length
if (strlen($password) < 8) {
    die("Password must be at least 8 characters.");
}

// Check for at least one number
if (!preg_match("/[0-9]/", $password)) {
    die("Password must contain at least one number.");
}

// Check password confirmation
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

// Hash the password before saving
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {

    // Save the new student
    $sql = "INSERT INTO users (name, email, password_hash)
            VALUES (:name, :email, :password_hash)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":email" => $email,
        ":password_hash" => $hashed_password
    ]);

    // Send the student to Login
    header("Location: login.html?registered=1");
    exit;

} catch (PDOException $e) {

    if ($e->getCode() == "23000") {
        die("This email is already registered.");
    }

    error_log($e->getMessage());
    die("Something went wrong. Please try again.");
}
?>
