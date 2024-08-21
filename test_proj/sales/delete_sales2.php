<?php

session_start();
include("../mainconn/db_connect.php");

if (!isset($_SESSION["user_id"]) || $_SESSION['role'] !== 'SalesManager') {
    header("Location: ../login.php");
    exit();
}
function logUserActivity($userId, $role, $action) {
    global $conn;
    $sql = "INSERT INTO user_logs (user_id, role, action, timestamp) VALUES (?,  ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Error preparing statement for logging user activity: " . $conn->error);
        return;
    }

    $stmt->bind_param('iss', $userId, $role, $action);
    $stmt->execute();

    if ($stmt->error) {
        error_log("Error executing statement for logging user activity: " . $stmt->error);
    }

    $stmt->close();
}
if (isset($_GET['id'])) {
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $id = (int)$_GET['id'];
        $sales_manager_id = (int)$_SESSION['user_id'];
       
        $sql = "DELETE FROM sales WHERE id = ? AND sales_manager_id = ?";
        $prestmt = $conn->prepare($sql);

        if ($prestmt) {
            $prestmt->bind_param('ii', $id, $sales_manager_id);

            if ($prestmt->execute()) {
                logUserActivity($sales_manager_id,$_SESSION['role'], 'Delete sales');

                echo 'The sales record has been deleted successfully!';
            } else {
                error_log("Error deleting sales record: " . $prestmt->error);
                echo "Error deleting sales record. Please try again.";
            }

            $prestmt->close();
        } else {
            error_log("Error preparing statement: " . $conn->error);
            echo "Error preparing statement. Please try again.";
        }
    } else {
        echo "Invalid ID.";
    }
} else {
    echo "ID not set.";
}

header("Location: manage_sales2.php");
exit();
?>
