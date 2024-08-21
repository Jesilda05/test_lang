<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../login.php');
    exit();
}

$cust_id = (int)$_SESSION['user_id'];
$error = $success = '';

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

    $query = "SELECT * FROM tickets WHERE id = ? AND customer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $id, $cust_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $ticket = $result->fetch_assoc();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $sub = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
                $desc = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);

                if (empty($sub) || empty($desc)) {
                    $error = "All fields are required.";
                } elseif (!preg_match('/^[a-zA-Z\s.,!?]+$/', $sub)) {
                    $error = "Subject can only contain letters, spaces, and basic punctuation.";
                } elseif (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $desc)) {
                    $error = "Description can only contain letters, numbers, spaces, and basic punctuation.";
                } else {
                    $upd_sql = "UPDATE tickets SET subject = ?, description = ? WHERE id = ? AND customer_id = ?";
                    $stmt = $conn->prepare($upd_sql);
                    $stmt->bind_param('ssii', $sub, $desc, $id, $cust_id);

                    if ($stmt->execute()) {
                        $success = 'Ticket updated successfully.';
                        logUserActivity($cust_id, $_SESSION['role'], 'Updated Ticket');
                        header("Location:manage_tickets2.php");
                    } else {
                        $error = 'Error updating ticket: ' . $stmt->error;
                    }
                }
            }
        } else {
            $error = 'Ticket not found.';
        }
        $stmt->close();
    } else {
        error_log("Error executing query: " . $stmt->error);
    }
} else {
    $error = "Invalid ID.";
}
?>

<?php include('header2.php'); ?>

<div class="cust_container">
    <h3>Update Ticket</h3>

    <?php if (!empty($error)) : ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)) : ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (isset($ticket)) : ?>
        <form action="edit_ticket2.php?id=<?php echo htmlspecialchars($id); ?>" method="POST">
            <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($cust_id); ?>">
            
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars($ticket['subject']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($ticket['description']); ?></textarea>
            </div>
            
            <button type="submit">Update</button>
        </form>
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
    padding: 30px; 
    text-align: center;
    background-color: #cc5e61;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border:4px solid black;

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