<?php 
session_start();
include("../mainconn/db_connect.php");
include("../mainconn/authentication.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || $_SESSION['role'] !== 'Customer') {
    header("Location: ../login.php");
    exit();
}

function logUserActivity($userId, $role, $action) {
    global $conn;

    error_log("Inside logUserActivity for user_id: $userId, role: $role, action: $action");

    $sql = "INSERT INTO user_logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Error preparing statement for logging user activity: " . $conn->error);
        return;
    }

    $stmt->bind_param('iss', $userId, $role, $action);
    $stmt->execute();

    if ($stmt->error) {
        error_log("Error executing statement for logging user activity: " . $stmt->error);
    } else {
        error_log("Log entry created successfully for action: $action");
    }

    $stmt->close();
}

if (isset($_GET['id'])) {
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $id = (int)$_GET['id'];
        $cust_id = (int)$_SESSION['user_id'];

        $sql = "DELETE FROM feedback WHERE id = ? AND customer_id = ?";
        $prestmt = $conn->prepare($sql);

        if ($prestmt) {
            $prestmt->bind_param('ii', $id, $cust_id);

            if ($prestmt->execute()) {
                echo 'Your feedback has been deleted successfully!';
                error_log("feedback deleted successfully");
                logUserActivity($cust_id, $_SESSION['role'], 'Delete feedback');
            } else {
                error_log("Error deleting feedback: " . $prestmt->error);
                echo "Error deleting ticket. Please try again.";
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

sleep(1);

header("Location: manage_feedback2.php");
exit();
?>