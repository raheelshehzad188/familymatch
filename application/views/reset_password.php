<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; }
        .container { max-width: 400px; margin: 60px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 30px; }
        h2 { color: #2d7ff9; text-align: center; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; }
        input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; background: #2d7ff9; color: #fff; border: none; padding: 12px; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .msg { margin: 15px 0; padding: 10px; border-radius: 4px; }
        .msg.success { background: #e0f7e9; color: #1b7e3c; }
        .msg.error { background: #ffeaea; color: #c00; }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Your Password</h2>
    <?php if (isset($msg)) { ?>
        <div class="msg <?php echo $msg_type; ?>"> <?php echo $msg; ?> </div>
    <?php } ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
        <button type="submit">Reset Password</button>
    </form>
</div>
</body>
</html> 