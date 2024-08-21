<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

// Check if the user is authenticated and is an 'Admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit();
}

$admin_id = (int)$_SESSION['user_id']; // Cast user_id to integer for security

// Query to retrieve all quotations
$sql = "SELECT q.id, q.customer_id, c.name AS customer_name, q.product, q.details, q.created_at, q.status 
        FROM quotations q
        JOIN customers c ON q.customer_id = c.id
        ORDER BY q.created_at DESC";
$result = $conn->query($sql);

// Check for errors
if ($conn->error) {
    echo "Failed to retrieve quotations.";
    error_log($conn->error); // Log error for further debugging
    exit();
}

?>

<?php include('header2.php'); ?>

<h2><b>Manage Quotations</b></h2>

<table border="1">
    <thead>
        <tr>
            <th>Customer Name</th>
            <th>Product</th>
            <th>Details</th>
            <th>Created At</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['product']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['details'])); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <a href="respond_quotation.php?id=<?php echo $row['id']; ?>">Respond</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No quotations found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include('footer.php'); ?>

<?php
// Close database connection
$result->close();
$conn->close();
?>
