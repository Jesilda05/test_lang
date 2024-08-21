<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

// Check if the user is authenticated and is a SalesManager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SalesManager') {
    header('Location: ../login.php');
    exit();
}

// Get Sales Manager ID and cast it to int
$sales_manager_id = (int)$_SESSION['user_id'];

// Prepare SQL query to retrieve sales data for the Sales Manager
$sales_sql = "SELECT COUNT(*) as total_sales, SUM(amount) as total_amount FROM sales WHERE sales_manager_id = ?";
$prestmt = $conn->prepare($sales_sql);
$prestmt->bind_param('i', $sales_manager_id);
$prestmt->execute();
$sales_result = $prestmt->get_result()->fetch_assoc();

// Handle errors
if ($conn->error) {
    echo "SORRY! We couldn't retrieve your data due to the following error.";
    error_log($conn->error);
}

?>

<?php include('header2.php'); ?>

<div class="dashboard">
    <div class="sidebar">
        <h3>SalesManager Dashboard</h3>
        <ul>
            <li><a href="sales_dashboard.php">Dashboard</a></li>
            <li><a href="create_sales2.php">Create Sale</a></li>
            <li><a href="manage_sales2.php">Manage Sales</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h2>Sales Overview</h2>
        <div class="summary">
            <div class="card">
                <h3>Total Sales</h3>
                <p><?php echo htmlspecialchars($sales_result['total_sales']); ?></p>
            </div>
            <div class="card">
                <h3>Total Amount</h3>
                <p>$<?php echo htmlspecialchars(number_format($sales_result['total_amount'], 2)); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<?php
$prestmt->close();
$conn->close();
?>
