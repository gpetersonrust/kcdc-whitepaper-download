<?php
/**
 * Admin email template for new whitepaper download requests
 * 
 * Variables available:
 * $name - Requester's name
 * $agency - Agency name
 * $email - Requester's email
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">New Whitepaper Download Request</h2>
        
        <p>A new whitepaper download request has been submitted with the following details:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Name:</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo esc_html($name); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Agency:</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo esc_html($agency); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Email:</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo esc_html($email); ?></td>
            </tr>
        </table>
        
        <p style="color: #666; font-size: 12px;">This is an automated notification. Please do not reply to this email.</p>
    </div>
</body>
</html>