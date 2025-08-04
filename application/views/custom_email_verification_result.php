<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification Result</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; color: #222; margin:0; padding:0;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; max-width: 500px; margin: 40px auto; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align:center;">
        <?php if (isset($status) && $status): ?>
            <h2 style="color: #28a745; margin-top:0;">Success!</h2>
        <?php else: ?>
            <h2 style="color: #dc3545; margin-top:0;">Error</h2>
        <?php endif; ?>
        <p style="font-size: 18px; margin: 24px 0;">
            <?php echo isset($message) ? htmlspecialchars($message) : 'Something went wrong.'; ?>
        </p>
        <a href="/familymatch/" style="display: inline-block; padding: 10px 22px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; margin-top: 10px;">Go to Home</a>
    </div>
</body>
</html> 