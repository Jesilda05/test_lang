<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'LeadManager') {
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
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $status = filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING);
    $lead_manager_id = (int)$_SESSION['user_id'];

    if (empty($name) || empty($email) || empty($phone) || empty($status)) {
        $err = "Please fill in all fields.";
    } elseif (!preg_match('/^[a-zA-Z\s.,!?]+$/', $name)) {
        $err = "Name can only contain letters, spaces, and basic punctuation.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "Invalid email format.";
    } elseif (!preg_match("/^\d{10}$/", $phone)) {
        $err = "Phone number must be 10 digits.";
    } else {
        $sql = "INSERT INTO leads (name, email, phone, status, created_at, lead_manager_id) VALUES (?, ?, ?, ?, NOW(), ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $err = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param('ssssi', $name, $email, $phone, $status, $lead_manager_id);

            if ($stmt->execute()) {
                $success = "Lead created successfully!";
                logUserActivity($lead_manager_id, $_SESSION['role'], 'Create lead'); 
                header("Location:manage_leads2.php");
            } else {
                $err = "Error creating lead: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>

<!-- Include header and form -->
<?php include('header2.php'); ?>
<div class="lead_container">
<h3 class="lead_form-heading">Create Lead</h3>

<?php if (!empty($err)): ?>
    <div class="error"><?php echo htmlspecialchars($err); ?></div>
<?php endif; ?>

<!-- Lead creation form -->
<form action="create_lead2.php" method="POST">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Phone: <input type="text" name="phone" required><br>
    Status:
    <select name="status" id="status" required>
        <option value="">Select a status</option>
        <option value="new">NEW</option>
        <option value="in_progress">IN_PROGRESS</option>
        <option value="closed">CLOSED</option>
    </select><br>
    <button type="submit">Create Lead</button>
</form>
</div>
<?php if (!empty($success)): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<style>
    body {
    margin: 0;
    padding: 0;
    font-family: Tahoma, Geneva, sans-serif;
    background-color: white;
}

.lead_container {
    max-width: 600px;
    margin: 20px auto; 
    padding: 20px;
    border:4px solid black;

    text-align: center;
    background-color: #cc5e61;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h3 {
    margin-bottom: 20px;
    font-size: 30px;
    color: black;
}

form {
    border: 4px solid black;
    padding: 20px;
    border-radius: 8px;
    background-color: white;
}

input[type="text"], input[type="email"] {
    width: calc(100% - 24px);
    padding: 10px;
    margin-bottom: 15px;
    border: 2px solid black;
    border-radius: 4px;
}

select {
    width: calc(100% - 24px);
    padding: 10px;
    margin-bottom: 15px;
    border: 2px solid black;
    border-radius: 4px;
}

button {
    background-color: #cc5e61;
    color: black;
    padding: 10px;
    border: 2px solid black;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
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