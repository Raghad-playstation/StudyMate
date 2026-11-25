

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once "db_connect.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email == "" || $password == "") {
        die("Please fill in all fields.");
    }

    try {
        // Find the student using her email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([":email" => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check the password
        if ($user && password_verify($password, $user["password_hash"])) {

            session_regenerate_id(true);

            // Remember the logged-in student
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["user_name"] = $user["name"];

            // Go to the dashboard
            header("Location: dashboard.html");
            exit;

        } else {
            echo "Invalid email or password.";
        }

    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo "Login failed. Please try again.";
    }
}
?>
