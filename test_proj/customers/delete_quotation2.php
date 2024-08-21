<?php
session_start();
include("../mainconn/db_connect.php");
include("../mainconn/authentication.php");

if (!isset($_SESSION["user_id"]) || $_SESSION['role'] !== 'Customer') {
    header("Location: ../login.php");
    exit();
}

function logUserActivity($userId, $role, $action) {
    global $conn;
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
    }

    $stmt->close();
}

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = (int)$_GET['id'];
    $cust_id = (int)$_SESSION['user_id'];

    $sql = "DELETE FROM quotations WHERE id = ? AND customer_id = ?";
    $prestmt = $conn->prepare($sql);

    if ($prestmt) {
        $prestmt->bind_param('ii', $id, $cust_id);

        if ($prestmt->execute()) {
            echo 'Your quotation has been deleted successfully!';
            logUserActivity($cust_id, $_SESSION['role'], 'Delete Quotation'); // Log the action
        } else {
            error_log("Error deleting quotation: " . $prestmt->error);
            echo "Error deleting quotation. Please try again.";
        }

        $prestmt->close();
    } else {
        error_log("Error preparing statement: " . $conn->error);
        echo "Error preparing statement. Please try again.";
    }

} else {
    echo "Invalid ID or ID not set.";
}

header("Location: manage_quotations2.php");
exit();
?>
