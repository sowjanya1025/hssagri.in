<?php
//require 'db.php'; // Include your PDO database connection
include'account.php';
$account =  new account();



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(50));

        // Set token expiration time (e.g., 1 hour)
        $expires = time() + 3600;

        // Insert token into the database
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE email = :email");
        $stmt->execute([
            'token' => $token,
            'expires' => $expires,
            'email' => $email
        ]);

        // Create reset URL
        $resetUrl = "http://yourwebsite.com/reset_password.php?token=" . $token;

        // Send email to user
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: " . $resetUrl;
        $headers = "From: no-reply@yourwebsite.com\r\n";
        mail($email, $subject, $message, $headers);

        echo "A password reset link has been sent to your email address.";
    } else {
        echo "No account found with that email address.";
    }
} else {
    echo "Invalid request.";
}
