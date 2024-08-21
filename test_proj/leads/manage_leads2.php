<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'LeadManager') {
    header('Location: ../login.php');
    exit();
}

$lead_manager_id = (int)$_SESSION['user_id'];
$sql = "SELECT * FROM leads WHERE lead_manager_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $lead_manager_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    echo "Error retrieving leads: " . $stmt->error;
}

?>

<?php include('header2.php'); ?>
<div class="lead_container">
    <h3>Manage Leads</h3>

    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <!-- Debugging the status display -->
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <a href="edit_lead2.php?id=<?php echo $row['id']; ?>">Edit</a> |
                            <a href="delete_lead2.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this lead?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No leads found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$stmt->close();
$conn->close();
?>

<!-- CSS Styling -->
<style>
    .lead_container {
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
