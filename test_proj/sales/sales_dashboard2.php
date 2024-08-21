<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SalesManager') {
    header('Location: ../login.php');
    exit();
}

$sales_manager_id = (int)$_SESSION['user_id'];

$stats = [
    'total_sales' => 0,
    'total_sales_count' => 0
];

$sql = "SELECT COUNT(*) AS total_sales_count, SUM(amount) AS total_sales FROM sales WHERE sales_manager_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $sales_manager_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $stats['total_sales_count'] = $row['total_sales_count'];
    $stats['total_sales'] = $row['total_sales'];
}
$stmt->close();

$recentSales = [];
$recentSalesSql = "SELECT id, amount, created_at FROM sales WHERE sales_manager_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($recentSalesSql);
$stmt->bind_param('i', $sales_manager_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recentSales[] = $row;
}
$stmt->close();

$conn->close();
?>
<?php include('header2.php')?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Manager Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        background-color:white;        }

        main {
            margin-left: 250px;
            padding: 2rem;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(225, 146, 209, 0.1);
            flex: 1;
            overflow-y: auto;
            min-height: 100vh;
            border-left: 2px solid black;
        }

        .dashboard {
            padding: 2rem;
            background-color: #ffffff;
        }

        .dashboard h2 {
            margin-top: 0;
            font-size: 2rem;
            color: black;
            border-bottom: 2px solid #c36262;
            padding-bottom: 0.5rem;
        }

        .dashboard-stats {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .dashboard-stat-card {
            background-color: #cc5e61;
            padding: 1.5rem;
            border-radius: 8px;
            border: 2px solid #101112;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1 1 calc(50% - 1rem);
            min-width: 250px;
        }

        .dashboard-stat-card h3 {
            margin-top: 0;
            font-size: 1.25rem;
            color: black;
        }

        .dashboard-stat-card p {
            margin: 0.5rem 0;
            font-size: 1.1rem;
            color: black;
        }

        .recent-sales {
            margin-top: 2rem;
        }

        .recent-sales h2 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
            color: #f9a4a3;
        }

        .recent-sales table {
            width: 100%;
            border-collapse: collapse;
        }

        .recent-sales th, .recent-sales td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .recent-sales th {
            background-color: #f8c6c4;
            color: #333;
        }

        .recent-sales tr:nth-child(even) {
            background-color: #f9e5e4;
        }
    </style>
</head>
<body>
    <main>
        <div class="dashboard">
            <h2>Sales Manager Dashboard</h2>

            <div class="dashboard-stats">
                <div class="dashboard-stat-card">
                    <h3>Total Sales</h3>
                    <p>$<?php echo number_format($stats['total_sales'], 2); ?></p>
                </div>
                <div class="dashboard-stat-card">
                    <h3>Total Number of Sales</h3>
                    <p><?php echo htmlspecialchars($stats['total_sales_count']); ?></p>
                </div>
            </div>

            <div class="recent-sales">
                <h2>Recent Sales</h2>
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentSales)): ?>
                            <?php foreach ($recentSales as $sale): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sale['id']); ?></td>
                                    <td>$<?php echo number_format($sale['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($sale['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No recent sales found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
