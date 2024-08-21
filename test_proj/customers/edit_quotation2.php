<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../login.php');
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

$cust_id = (int)$_SESSION['user_id'];
$error = $success = '';

if (isset($_GET['id'])) {
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $id = (int)$_GET['id'];
        $query = "SELECT * FROM quotations WHERE id = ? AND customer_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $id, $cust_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $quotation = $result->fetch_assoc();

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $details = filter_var(trim($_POST['details']), FILTER_SANITIZE_STRING);
                    $product = filter_var(trim($_POST['product']), FILTER_SANITIZE_STRING);

                    if (empty($details) || empty($product)) {
                        $error = "Both details and product are required.";
                    } elseif (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $details)) {
                        $error = "Details can only contain letters, numbers, spaces, and basic punctuation.";
                    } elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', $product)) {
                        $error = "Product can only contain letters, numbers, and spaces.";
                    } else {
                        $upd_sql = "UPDATE quotations SET details = ?, product = ? WHERE id = ? AND customer_id = ?";
                        $stmt = $conn->prepare($upd_sql);
                        $stmt->bind_param('ssii', $details, $product, $id, $cust_id);

                        if ($stmt->execute()) {
                            $success = 'Quotation updated successfully.';
                            logUserActivity($cust_id, $_SESSION['role'], 'Updated Quotation');
                            header("Location:manage_quotations2.php");
                        } else {
                            $error = 'Error updating quotation: ' . $stmt->error;
                        }
                    }
                }
            } else {
                $error = 'Quotation not found.';
            }
            $stmt->close();
        } else {
            error_log("Error executing query: " . $stmt->error);
        }
    } else {
        $error = "Invalid ID.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['id'])) {
    $details = filter_var(trim($_POST['details']), FILTER_SANITIZE_STRING);
    $product = filter_var(trim($_POST['product']), FILTER_SANITIZE_STRING);

    if (empty($details) || empty($product)) {
        $error = "Both details and product are required.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $details)) {
        $error = "Details can only contain letters, numbers, spaces, and basic punctuation.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', $product)) {
        $error = "Product can only contain letters, numbers, and spaces.";
    } else {
        $sql = "INSERT INTO quotations (customer_id, details, product, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iss', $cust_id, $details, $product);

        if ($stmt->execute()) {
            $success = 'Quotation created successfully.';
        } else {
            $error = 'Error creating quotation: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<?php include('header2.php'); ?>
<div class="cust_container">
    <h3><?php echo isset($id) ? 'Update Quotation' : 'Create Quotation'; ?></h3>

    <?php if (!empty($error)) : ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)) : ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="<?php echo isset($id) ? $_SERVER['PHP_SELF'] . '?id=' . $id : $_SERVER['PHP_SELF']; ?>" method="POST">
        <?php if (isset($id)) : ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <?php endif; ?>
        <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($cust_id); ?>">

        <div class="form-group">
            <label for="details">Details:</label>
            <textarea name="details" id="details" required><?php echo isset($quotation['details']) ? htmlspecialchars($quotation['details']) : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label for="product">Product:</label>
            <input type="text" name="product" id="product" required value="<?php echo isset($quotation['product']) ? htmlspecialchars($quotation['product']) : ''; ?>">
        </div>

        <button type="submit"><?php echo isset($id) ? 'Update' : 'Submit'; ?></button>
    </form>
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
        border:4px solid black;

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
        margin-top:20px;
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
