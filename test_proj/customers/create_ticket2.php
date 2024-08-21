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
    }

    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $sub = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
    $desc = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);

    $cust_id = (int)$_SESSION['user_id'];

    if (empty($sub) || empty($desc)) {
        $err = "Please fill in all fields.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $desc)) {
        $err = "Description can only contain letters, numbers, spaces, and basic punctuation.";
    } elseif (!preg_match('/^[a-zA-Z\s.,!?]+$/', $sub)) {
        $err = "Subject can only contain letters, spaces, and basic punctuation.";
    } else {


        $sql = "INSERT INTO tickets (customer_id, subject, description, created_at) VALUES (?, ?, ?, NOW())";
        $prestmt = $conn->prepare($sql);

        if ($prestmt) {
            $prestmt->bind_param('iss', $cust_id, $sub, $desc);

            if ($prestmt->execute()) {
                $success = 'Your ticket has been created successfully!';
                logUserActivity($cust_id, $_SESSION['role'], 'Create Ticket'); 
                header("Location:manage_tickets2.php");

            } else {
                error_log("Error occurred while creating ticket: " . $prestmt->error);
            }

            $prestmt->close();
        } else {
            error_log("The statement could not be prepared due to the following error: " . $conn->error);
        }
    }
}
?>

<?php include('header2.php'); ?>
<!DOCTYPE html>
<html>
<head>
   
   
</head>
<body>
    <div class="cust_container">


<?php if (!empty($err)): ?>
    <div class="error-message"><?php echo $err; ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="success-message"><?php echo $success; ?></div>
<?php endif; ?>

<form action="create_ticket2.php" method="POST">
    Subject:
    <input type="text" name="subject" id="subject" required><br>

    Description:
    <textarea name="description" id="description" required></textarea><br>

    <button type="submit">Submit Ticket</button>
</form>
</form>
    </div>
</body>
</html>


<style>
   body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;

    }

    .cust_container {
        max-width: 900px; 
        margin: 40px auto; 
        padding: 40px;
        text-align: center;
        background-color: #cc5e61;
        border:4px solid black;

        border-radius: 12px; 
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); 
    }

    form {
        border: 4px solid black; 
        padding: 30px;
        border-radius: 12px;
        background-color: white;
    }

    input[type="text"], input[type="email"] {
        width: calc(100% - 48px); 
        padding: 15px; 
        margin-bottom: 20px;
        border: 2px solid black; 
        border-radius: 6px;
    }

    textarea {
        width: calc(100% - 48px); 
        padding: 15px; 
        margin-bottom: 20px;
        border: 2px solid black; 
        border-radius: 6px;
        height: 150px; 
    }

    button {
        background-color: #cc5e61;
        color: black;
        padding: 15px; 
        border: 2px solid black;
        border-radius: 6px;
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

    .success {
        color: green;
        margin-top: 20px;
    }
</style>
