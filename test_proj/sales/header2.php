<?php
include('../mainconn/db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background-color: #ffffff;
        }

        .sidebar {
            width: 250px;
            background-color: black;
            padding: 1rem;
            box-shadow: 2px 0 4px rgba(112, 156, 232, 0.1);
            overflow-y: auto;
            border-right: 2px solid #003366;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
        }

        .sidebar h1 {
            color: white;
            margin-top: 0;
            font-size: 1.5rem;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        nav li {
            margin: 0.5rem 0;
        }

        nav a {
            display: block;
            color: white;
            background-color: rgb(124, 106, 106);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem;
            border-radius: 4px;
            border: 2.5px solid #0a0f14;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        nav a:hover {
            background-color: #cc5e61;
            border-color: #002244;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h1>Sales Dashboard</h1>
        <nav>
            <ul>
                <li><a href="sales_dashboard2.php">Dashboard</a></li>
                <li><a href="create_sales2.php">Create Sales</a></li>
                <li><a href="manage_sales2.php">Manage Sales</a></li>
                <li><a href="change_password.php">Update Password</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
