<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'LeadManager') {
    header('Location: ../login.php');
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

    $stmt->bind_param('iss', $userId, $_SESSION['role'], $action);
    $stmt->execute();

    if ($stmt->error) {
        error_log("Error executing statement for logging user activity: " . $stmt->error);
    }

    $stmt->close();
}

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = (int)$_GET['id'];
    $lead_manager_id = (int)$_SESSION['user_id'];

    $sql = "DELETE FROM leads WHERE id = ? AND lead_manager_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $id, $lead_manager_id);

    if ($stmt->execute()) {
        logUserActivity($lead_manager_id, $role, 'Delete Lead');

        header('Location: manage_leads2.php?success=Lead deleted successfully.');
    } else {
        header('Location: manage_leads2.php?error=Error deleting lead.');
    }

   
    $stmt->close();
} else {
    header('Location: manage_leads2.php?error=Invalid lead ID.');
    exit();
}

$conn->close();
?>
