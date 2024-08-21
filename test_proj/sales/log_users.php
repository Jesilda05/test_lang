<?php
function log_user_action($conn, $user_id, $action) {
    // Prepare SQL statement
    $sql = "INSERT INTO user_logs (user_id, action) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Failed to prepare statement: " . $conn->error);
        return false;
    }

    // Bind parameters and execute statement
    $stmt->bind_param('is', $user_id, $action);
    
    if ($stmt->execute()) {
        return true;
    } else {
        error_log("Error executing statement: " . $stmt->error);
        return false;
    }
}

// Example usage
include('mainconn/db_connect.php'); // Include your database connection file

session_start();
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Log an action
if (log_user_action($conn, $user_id, 'Logged in')) {
    echo "Action logged successfully.";
} else {
    echo "Failed to log action.";
}

$conn->close();
?>
