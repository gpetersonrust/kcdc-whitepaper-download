<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #ffffff; padding: 20px; border: 1px solid #dddddd; border-radius: 4px;">
        <h1 style="color: #2c3e50; margin-bottom: 20px;">Your Whitepaper Download</h1>
        
        <p>Dear <?php echo esc_html($name); ?>,</p>
        
        <p>Thank you for requesting our whitepaper. You can download it using the secure link below:</p>
        
        <p style="margin: 30px 0;">
            <a href="<?php echo esc_url($download_url); ?>" 
               style="background-color: #3498db; 
                      color: #ffffff; 
                      padding: 12px 24px; 
                      text-decoration: none; 
                      border-radius: 4px; 
                      display: inline-block;">
                Click Here
            </a>
        </p>
        
        <p><small>This is a one-time use link. If you need to download the whitepaper again, please submit a new request.</small></p>
        
        <hr style="border: 0; border-bottom: 1px solid #dddddd; margin: 20px 0;">
        
        <p style="color: #666666; font-size: 12px;">
            If you did not request this download, please disregard this email.
        </p>
    </div>
</body>
</html>