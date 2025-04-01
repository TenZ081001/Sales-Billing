<?php
require 'db/dbconn.php'; // Include database connection

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    try {
        // First, check if the user exists
        $checkStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $checkStmt->execute([$user_id]);
        
        if ($checkStmt->rowCount() === 0) {
            echo "<script>
                alert('Error: User not found');
                window.location.href = 'admin.php';
            </script>";
            exit();
        }

        // Delete the user
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        echo "<script>
            alert('User deleted successfully!');
            window.location.href = 'admin.php';
        </script>";
        exit();
        
    } catch (PDOException $e) {
        echo "<script>
            alert('Error deleting user: " . addslashes($e->getMessage()) . "');
            window.location.href = 'admin.php';
        </script>";
        exit();
    }
} else {
    header("Location: admin.php");
    exit();
}
?>