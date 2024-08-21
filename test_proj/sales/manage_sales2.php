<?php
session_start();
include('../mainconn/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SalesManager') {
    header('Location: ../login.php');
    exit();
}

$sales_manager_id = (int)$_SESSION['user_id'];

$sql = "SELECT * FROM sales WHERE sales_manager_id = ? ORDER BY created_at DESC";
$prestmt = $conn->prepare($sql);
$prestmt->bind_param('i', $sales_manager_id);

$prestmt->execute();
$res = $prestmt->get_result();

if ($conn->error) {
    echo "SORRY! We couldn't retrieve your data due to the following error.";
    error_log($conn->error);
}

?>

<?php include('header2.php'); ?>
<div class="sales_container">
<h3>Manage Sales</h3>

<table border="1">
    <thead>
        <tr>
            <th><strong>Amount</strong></th>
            <th><strong>Created At</strong></th>
            <th><strong>Actions</strong></th>
        </tr>
    </thead>
    <tbody>
        <?php if ($res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <a href="edit_sales2.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="delete_sales2.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this sale?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">NO SALES RECORDS FOUND IN THE TABLE.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<?php
$res->close();
$prestmt->close();
$conn->close();
?>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family:  Tahoma, Geneva, sans-serif;

    }

    .sales_container {
        max-width: 800px; 
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
        color: white; 
    }

    table {
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 20px; 
    }

    th, td {
        border: 4px solid black; 
        padding: 10px; 
        text-align: left; 
        background-color: white; 
    }

    th {
        background-color: #e63c3c; 
        color: white; 
    }

    tr:nth-child(even) {
        background-color: #f16f6f; 
    }

    a.edit-link, a.delete-link {
        color: grey; 
        text-decoration: none; 
    }

    a.edit-link:hover, a.delete-link:hover {
        text-decoration: underline; 
    }

    a.delete-link {
        color: #d9534f; 
    }
</style>
