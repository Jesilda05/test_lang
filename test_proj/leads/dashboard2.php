<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'LeadManager') {
    header('Location: ../login.php');
    exit();
}

$lead_manager_id = (int)$_SESSION['user_id'];

$stats = [
    'total_leads' => 0,
    'new_leads' => 0,
];

$sql = "SELECT COUNT(*) AS total FROM leads WHERE lead_manager_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $lead_manager_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $stats['total_leads'] = $row['total'];
}

$sql = "SELECT COUNT(*) AS new FROM leads WHERE lead_manager_id = ? AND status = 'New'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $lead_manager_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $stats['new_leads'] = $row['new'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Manager Dashboard</title>
    <link rel="stylesheet" href="../assets/css/lead_styles.css"> <!-- Ensure path is correct -->
</head>
<body>
    <?php include('header2.php'); ?>

    <div class="lead_dashboard">
        <h2>Lead Manager Dashboard</h2>

        <div class="lead_dashboard-stats">
            <div class="lead_dashboard-stat-card">
                <h3>Total Leads</h3>
                <p><?php echo htmlspecialchars($stats['total_leads']); ?></p>
            </div>  
            <div class="lead_dashboard-stat-card">
                <h3>New Leads</h3>
                <p><?php echo htmlspecialchars($stats['new_leads']); ?></p>
            </div>
        </div>
    </div>

</body>
</html>
