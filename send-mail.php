<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminEmail = "nairaditya724@gmail.com"; // Your email to receive the enquiry
    $subjectToAdmin = "New Contact Form Submission";

    // Sanitize inputs
    $name = htmlspecialchars($_POST["name"] ?? '');
    $email = htmlspecialchars($_POST["email"] ?? '');
    $message = htmlspecialchars($_POST["message"] ?? '');

    // --- Email to Admin ---
    $bodyToAdmin = "Name: $name\n";
    $bodyToAdmin .= "Email: $email\n";
    $bodyToAdmin .= "Message:\n$message";

    $headersToAdmin = "From: no-reply@pdsimpexp.in\r\n";
    $headersToAdmin .= "Reply-To: $email\r\n";

    // --- Email to User ---
    $subjectToUser = "Thank You for Contacting Us";
    $bodyToUser = "Dear $name,\n\n";
    $bodyToUser .= "Thank you for reaching out to us. We have received your enquiry and will get back to you shortly.\n\n";
    $bodyToUser .= "Best regards,\nTeam PDS Import Export";

    $headersToUser = "From: no-reply@pdsimpexp.in\r\n";
    $headersToUser .= "Reply-To: no-reply@pdsimpexp.in\r\n";

    // --- Send Emails ---
    $sentToAdmin = mail($adminEmail, $subjectToAdmin, $bodyToAdmin, $headersToAdmin);
    $sentToUser = mail($email, $subjectToUser, $bodyToUser, $headersToUser);

    // --- Output Confirmation or Error ---
    if ($sentToAdmin && $sentToUser) {
        echo "
        <html>
        <head>
            <title>Thank You</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: #f4f4f4; }
                .container { background: white; padding: 30px; border-radius: 10px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .btn { padding: 10px 20px; background: #007BFF; color: white; border: none; border-radius: 5px; text-decoration: none; margin-top: 20px; display: inline-block; }
                .btn:hover { background: #0056b3; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Thank You for Contacting Us!</h2>
                <p>We’ve received your message and will get back to you soon.</p>
                <a class='btn' href='index.html'>Back to Home</a>
            </div>
        </body>
        </html>
        ";
    } else {
        echo "Something went wrong. Please try again.";
    }
}
?>
