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
$srep_id = (int)$_SESSION['user_id'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $l_id = (int)$_GET['id'];
} else {
    $err = "Invalid lead ID.";
}

if (!empty($l_id)) {
    $stmt = $conn->prepare("SELECT name, email, phone, status FROM leads WHERE id=? ");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $l_id);
    $stmt->execute();
    $stmt->bind_result($lead_name, $lead_email, $lead_phone, $lead_status);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($err)) {
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $status = isset($_POST['status']) ? filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING) : '';

    if (empty($name) || empty($email) || empty($phone) || empty($status)) {
        $err = "Please fill in all fields.";
    } elseif (!preg_match('/^[a-zA-Z\s.,!?]+$/', $name)) {
        $err = "Name can only contain letters, spaces, and basic punctuation.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "Invalid email format.";
    } elseif (!preg_match("/^\d{10}$/", $phone)) {
        $err = "Phone number must be exactly 10 digits.";
    } elseif (!in_array($status, ['new', 'in_progress', 'closed'])) {
        $err = "Invalid status.";
    }

    if (empty($err)) {
        $sql = "UPDATE leads SET name=?, email=?, phone=?, status=? WHERE id=? ";
        $prestmt = $conn->prepare($sql);

        if ($prestmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $prestmt->bind_param('ssssi', $name, $email, $phone, $status, $l_id);

        if ($prestmt->execute()) {
            if ($prestmt->affected_rows > 0) {
                $success = 'Your lead has been updated successfully!';
                header("Location: manage_leads2.php"); 
                exit();
            } else {
                $err = "No changes were made or the lead does not exist.";
            }
        } else {
            $err = "Error occurred while updating lead: " . $prestmt->error;
        }

        $prestmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lead</title>
</head>
<body>
    <?php include 'header2.php'; ?>

    <div class="lead_container">
        <h1>Edit Lead </h1>

        <?php if (!empty($err)): ?>
            <div style='color: black;'><?php echo $err; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style='color: green;'><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="edit_lead2.php?id=<?php echo $l_id; ?>" method="POST">
            Name: <input type="text" name="name" value="<?php echo htmlspecialchars($lead_name); ?>" required><br>
            Email: <input type="email" name="email" value="<?php echo htmlspecialchars($lead_email); ?>" required><br>
            Phone: <input type="text" name="phone" value="<?php echo htmlspecialchars($lead_phone); ?>" required><br>
            Status: 
            <select name="status" required>
                <option value="new" <?php echo $lead_status == 'new' ? 'selected' : ''; ?>>NEW</option>
                <option value="in_progress" <?php echo $lead_status == 'in_progress' ? 'selected' : ''; ?>>IN_PROGRESS</option>
                <option value="closed" <?php echo $lead_status == 'closed' ? 'selected' : ''; ?>>CLOSED</option>
            </select><br>
            <button type="submit">Update Lead</button>
        </form>
    </div>

    
</body>
</html>

<style>
     body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: white;

    }

    .lead_container {
        max-width: 600px;
        margin: 20px auto; 
        padding: 20px;
        text-align: center;
        border:4px solid black;

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
        background-color:#cc5e61 ;
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
