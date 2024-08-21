<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0px;
            display: flex;
            min-height: 100vh;
            font-family: Tahoma, Geneva, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            width: 100%;
        }

        .cust_sidebar {
            width: 250px;
            background-color: #000000;
            color: #ecf0f1;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }

        .cust_sidebar h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: white;
        }

        .cust_sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .cust_sidebar nav ul li {
            margin-bottom: 10px;
        }

        .cust_sidebar nav ul li a {
            color: #ecf0f1;
            text-decoration: none;
            background-color: #7c6a6a;
            font-size: 18px;
            padding: 10px;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .cust_sidebar nav ul li a:hover {
            background-color: #cc5e61;
        }

        .main {
            margin-left: 260px; 
            width: calc(100% - 280px); 
            padding: 20px;
            background-color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="cust_sidebar">
            <h1>Customer Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="cust_dashboard2.php">Home</a></li>
                    <li><a href="create_quotation2.php">Create Quotation</a></li>
                    <li><a href="manage_quotations2.php">Manage Quotations</a></li>
                    <li><a href="create_ticket2.php">Create Ticket</a></li>
                    <li><a href="manage_tickets2.php">Manage Tickets</a></li>
                    <li><a href="create_feedback2.php">Provide Feedback</a></li>
                    <li><a href="manage_feedback2.php">Manage Feedback</a></li>
                    <li><a href="change_password.php">Change Password</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>
