<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php'; // Adjust the path if needed

// Function to send the email
function sendUpdateEmail($taskId,$taskName, $status, $reason, $attachment) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username   = 'kameshwaranking5658@gmail.com';         // SMTP username (your email address)
        $mail->Password   = 'uwfv jgds sgdk fdql';  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; 

        // Recipients
        $mail->setFrom('kameshwaranking5658@gmail.com', 'Task Update System');
        $mail->addAddress('kameshwaranking5658@gmail.com', 'Admin'); // Admin's email

        // Content with table layout
        $mail->isHTML(true);
        $mail->Subject = 'Task Update Notification';
        $mail->Body = "
        <h2>Task Update Notification</h2>
        <table style='border: 1px solid #ddd; width: 100%; border-collapse: collapse;'>
            <thead>
                <tr style='background-color: #f4f4f4;'>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Task ID</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Task Name</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Status</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Reason</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Attachment</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$taskId}</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$taskName}</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$status}</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$reason}</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$attachment}</td>
                </tr>
            </tbody>
        </table>";

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Get POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taskId'])) {
    $taskId = $_POST['taskId'];
    $taskName = $_POST['taskName'];
    $status = $_POST['status'];
    $reason = $_POST['reason'];
    $attachment = $_POST['attachment'];

    // Send the email notification
    sendUpdateEmail($taskId,$taskName, $status, $reason, $attachment);
}
?>
