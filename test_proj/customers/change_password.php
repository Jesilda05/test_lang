<?php
session_start();
include('../mainconn/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sales_manager_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        $sql = "SELECT password FROM customers WHERE id = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('i', $sales_manager_id);

            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if (password_verify($current_password, $row['password'])) {
                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $sql_update = "UPDATE customers SET password = ? WHERE id = ?";
                    if ($stmt_update = $conn->prepare($sql_update)) {
                        $stmt_update->bind_param('si', $hashed_new_password, $sales_manager_id);

                        if ($stmt_update->execute()) {
                            $success = "Password changed successfully!";
                        } else {
                            $error = "Error updating password. Please try again.";
                        }

                        $stmt_update->close();
                    } else {
                        $error = "Failed to prepare update query: " . $conn->error;
                    }
                } else {
                    $error = "Current password is incorrect.";
                }
            } else {
                $error = "User not found.";
            }

            $stmt->close();
        } else {
            $error = "Failed to prepare query: " . $conn->error;
        }
    }

    $conn->close();
}
?>

<?php include('header2.php'); ?>

<div class="cust_container">
    <h3>Change Password</h3>

    <?php if (isset($error)): ?>
        <div style="color: red;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div style="color: green;"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div>
            <label for="current_password">Current Password</label>
            <input type="password" name="current_password" id="current_password" required>
        </div>
        <div>
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" required>
        </div>
        <div>
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>
        <div>
            <button type="submit">Change Password</button>
        </div>
    </form>
</div>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;

    }
    .cust_container {
        max-width: 400px;
        margin: 40px auto;
        padding: 20px;
        text-align: center;
        border: 5px solid black;
        border-radius: 8px;
        background-color: #cc5e61;
    }

    h3 {
        margin-bottom: 20px;
        font-size: 24px;
        color: black;
    }

    form div {
        margin-bottom: 15px;
        text-align: left;
    }

    label {
        font-weight: bold;
    }

    input[type="password"] {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
    }

    button {
        padding: 10px 15px;
        border: 3px solid black;
        background-color: #cc5e61;
        color: black;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #e63c3c;
    }
</style>
