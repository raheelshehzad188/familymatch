<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Password Reset Request</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 30px;">
  <div style="max-width: 500px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 30px;">
    <h2 style="color: #2d7ff9;">FamilyMatch Password Reset</h2>
    <p>We received a request to reset your password. Click the button below to set a new password:</p>
    <p style="text-align: center;">
      <a href="{{RESET_LINK}}" style="display: inline-block; background: #2d7ff9; color: #fff; padding: 12px 24px; border-radius: 4px; text-decoration: none; font-weight: bold;">
        Reset Password
      </a>
    </p>
    <p>If you did not request this, you can safely ignore this email.</p>
    <hr style="margin: 30px 0;">
    <p style="font-size: 12px; color: #888;">If the button doesn&#39;t work, copy and paste this link into your browser:<br>
      <a href="{{RESET_LINK}}" style="color: #2d7ff9;">{{RESET_LINK}}</a>
    </p>
  </div>
</body>
</html> 