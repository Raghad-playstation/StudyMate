
<?php

class TaskManager {

    private $conn; 

    // Store the database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Count tasks for one student
    public function countTasks($user_id) {

        $sql = "SELECT COUNT(*) FROM tasks
                WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ":user_id" => $user_id
        ]);

        return (int) $stmt->fetchColumn();
    }
}
?>
