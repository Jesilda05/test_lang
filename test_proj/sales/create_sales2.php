<?php
session_start(); 
include('../mainconn/db_connect.php'); 
include('../mainconn/authentication.php'); 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SalesManager') {
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
    
    $quotation_id = filter_var(trim($_POST['quotation_id']), FILTER_SANITIZE_NUMBER_INT);
    $amount = filter_var(trim($_POST['amount']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    $sales_manager_id = (int)$_SESSION['user_id'];

    // Validation
    if (empty($quotation_id) || empty($amount)) {
        $err = "Please fill in all fields.";
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $err = "Amount must be a positive number.";
    } else {
        $sql = "INSERT INTO sales (quotation_id, amount, sales_manager_id, created_at) VALUES (?, ?, ?, NOW())";
        $prestmt = $conn->prepare($sql);

        if ($prestmt) {
            $prestmt->bind_param('idi', $quotation_id, $amount, $sales_manager_id);

            if ($prestmt->execute()) {
                $success = 'Sales record has been created successfully!';
                logUserActivity($sales_manager_id, $_SESSION['role'], 'Create Sales');
                header("Location:manage_sales2.php");


            } else {
                error_log("Error occurred while creating sales record: " . $prestmt->error);
            }

            $prestmt->close();
        } else {
            error_log("The statement could not be prepared due to the following error: " . $conn->error);
        }
    }
}
?>

<?php include('header2.php'); ?>

<div class="sales_container">
    <h3 class="sales_form-heading">Create Sales</h3>

    <?php if (!empty($err)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="create_sales2.php" method="POST" class="form-sales">
        <div class="form-group">
            <label for="quotation_id">Quotation ID:</label>
            <input type="number" name="quotation_id" id="quotation_id" required class="form-control">
        </div>

        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" step="0.01" name="amount" id="amount" required class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Submit Sales Record</button>
    </form>
</div>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family:  Tahoma, Geneva, sans-serif;

         
    }

    .sales_container {
        max-width: 700px;
        margin: 20px auto; 
        padding: 30px;
        text-align: center;
        background-color: #cc5e61;
        border-radius: 8px;
        border:4px solid black;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h3 {
        margin-bottom: 20px;
        font-size: 30px;
        color: black;
    }

    form {
        border: 4px solid black;
        padding: 30px;
        border-radius: 8px;
        background-color: white;
    }

    input[type="text"], input[type="email"] {
        width: calc(100% - 24px);
        padding: 30px;
        margin-bottom: 15px;
        border: 2px solid black;
        border-radius: 4px;
    }

    select {
        width: calc(100% - 24px);
        padding: 20px;
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
        color: red;
        margin-bottom: 20px;
    }

    .success {
        color: green;
        margin-top: 20px;
    }
</style>