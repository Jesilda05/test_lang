<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            font-family: Tahoma, Geneva, sans-serif;
            background-color: #ffffff;
        }

        .top-bar {
            background-color: #cc5e61;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .container {
            padding: 20px;
            width: 100%;
            
        }

        .main {
            padding: 20px;
        }

        .heading {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: left;
        }

        .divider {
            border: 0;
            border-top: 2px solid #cc5e61;
            margin-bottom: 20px;
            width: 95%; 
        }

        .cust_cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: flex-start;
        }

        .quot_card {
            background-color: #cc5e61;
            border: 2px solid black;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 95%; 
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .ticket_card {
            background-color: #cc5e61;
            border: 2px solid black;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 95%; 
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .feedback_card {
            background-color: #cc5e61;
            border: 2px solid black;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 95%; 
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-content {
            display: flex;
            flex-direction: column;
        }

        .number {
            font-size: 24px;
            font-weight: bold;
            color: black;
        }

        .card-name {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <?php 
    include 'header2.php'; 

    include '../mainconn/db_connect.php';

    $quotationCount = $conn->query("SELECT COUNT(*) AS total FROM quotations")->fetch_assoc()['total'];
    $ticketCount = $conn->query("SELECT COUNT(*) AS total FROM tickets")->fetch_assoc()['total'];
    $feedbackCount = $conn->query("SELECT COUNT(*) AS total FROM feedback")->fetch_assoc()['total'];
    ?>

    <div class="container">
    
        <div class="main">
            <div class="heading">Customer Dashboard</div>
            <hr class="divider">

            <div class="cust_cards">
                <div class="quot_card">
                    <div class="card-content">
                        <div class="number"><?php echo $quotationCount; ?></div>
                        <div class="card-name">Total Quotations</div>
                    </div>
                </div>
                <div class="ticket_card">
                    <div class="card-content">
                        <div class="number"><?php echo $ticketCount; ?></div>
                        <div class="card-name">Total Tickets</div>
                    </div>
                </div>
                <div class="feedback_card">
                    <div class="card-content">
                        <div class="number"><?php echo $feedbackCount; ?></div>
                        <div class="card-name">Total Feedbacks</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
