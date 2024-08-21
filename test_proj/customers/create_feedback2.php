<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../login.php');
    exit();
}

$err = "";
$success = "";

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
    } else {
        error_log("User activity logged successfully");
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feed = filter_var(trim($_POST['feedback']), FILTER_SANITIZE_STRING);
    $cust_id = (int)$_SESSION['user_id'];

    if (empty($feed)) {
        $err = 'PLEASE FILL IN ALL FIELDS';
    } elseif (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $feed)) {
        $err = "Feedback can only contain letters,numbers, spaces, and basic punctuation.";
    } else {
        $sql = "INSERT INTO feedback (customer_id, feedback, created_at) VALUES (?, ?, NOW())";
        $prestmt = $conn->prepare($sql);
        $prestmt->bind_param('is', $cust_id, $feed);

        if ($prestmt->execute()) {
            $success = 'Your Feedback has been submitted successfully!';
            error_log("Feedback submitted, attempting to log user activity...");
            logUserActivity($cust_id, $_SESSION['role'], 'Create Feedback');
            header("Location: manage_feedback2.php");
            exit(); 
        } else {
            error_log("Error submitting feedback: " . $prestmt->error);
        }

        $prestmt->close();
    }
}
?>

<?php include('header2.php'); ?>
<h3>Create Feedback</h3>
<div class="cust_container">
    <?php if (!empty($err)): ?>
        <div class="error"><?php echo $err; ?></div>
    <?php endif; ?>

    <form action="create_feedback2.php" method="POST">
        <div class="form-group">
            <label for="feedback"><strong>Feedback:</strong></label>
            <textarea name="feedback" id="feedback" required></textarea>
        </div>
        <button type="submit">Submit Feedback</button>
    </form>
    
    <?php if (!empty($success)): ?>
        <div class="success-message"><?php echo $success; ?></div>
    <?php endif; ?>
</div>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;

    }

    .cust_container {
        max-width: 800px; 
        margin: 40px auto; 
        border:4px solid black;

        padding: 30px; 
        text-align: center;
        background-color: #cc5e61;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h3 {
        margin-bottom: 30px;
        font-size: 26px;
        color: black;
    }

    form {
        border: 4px solid black;
        padding: 30px; 
        border-radius: 10px;
        background-color: #fff;
    }

    textarea {
        width: 90%; 
        height: 200px; 
        padding: 15px;
        margin-bottom: 20px;
        border: 2px solid black;
        border-radius: 6px;
        font-size: 16px; 
    }

    button {
        background-color: #cc5e61;
        color: black;
        padding: 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 18px; 
        width: 100%;
    }

    button:hover {
        background-color: #e63c3c;
    }

    .error {
        color: black;
        margin-bottom: 20px;
    }

    .success-message {
        color: green;
        margin-top: 20px;
    }
</style>
