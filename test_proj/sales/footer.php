<!-- footer.php -->
<footer>
    <div class="footer-content">
        <p>&copy; <?php echo date("Y"); ?> Your Company Name. All rights reserved.</p>
        <p><a href="privacy_policy.php">Privacy Policy</a> | <a href="terms_of_service.php">Terms of Service</a></p>
    </div>
</footer>

<style>
    footer {
        background-color: #007BFF; /* Blue background for footer */
        color: white;
        padding: 10px 0;
        text-align: center;
        position: absolute;
        bottom: 0;
        width: 100%;
    }

    .footer-content p {
        margin: 0;
        font-size: 14px;
    }

    .footer-content a {
        color: white;
        text-decoration: none;
        margin: 0 10px;
    }

    .footer-content a:hover {
        text-decoration: underline;
    }
</style>
