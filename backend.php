<?php
// Include the PHPMailer class if using Composer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve tasks as a JSON array
    $tasks = json_decode($_POST['tasks'], true);

    // Initialize an array to hold validation errors
    $errors = [];

    // Loop through the tasks and validate each one
    foreach ($tasks as $index => $task) {
        // Validate taskName
        if (empty($task['taskName'])) {
            $errors[] = "Task name is required for task #" . ($index + 1);
        }

        // Validate taskDescription
        if (empty($task['taskDescription'])) {
            $errors[] = "Task description is required for task #" . ($index + 1);
        }

        // Validate dueDate
        if (empty($task['dueDate'])) {
            $errors[] = "Due date is required for task #" . ($index + 1);
        }

        // Validate priority
        if (!in_array($task['priority'], ['Low', 'Medium', 'High'])) {
            $errors[] = "Priority must be one of: Low, Medium, High for task #" . ($index + 1);
        }
    }

    // If there are any errors, return them as a response
    if (count($errors) > 0) {
        // Return the errors as a JSON response
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }

    // If validation passes, send the data to Google Apps Script
    $url = "https://script.google.com/macros/s/AKfycbw5W-gWx4qeizkyWjvFk9HVkjF_amLU00-Kmes-_LdAdyfnWCA5KIK0JVy-IRVEksTj/exec"; // Replace with your actual URL
    
    foreach ($tasks as $task) {
        $data = array(
            'tasks' => json_encode([$task])  // Send the task as JSON
        );

        // Send each task data to Google Apps Script
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    // Send email notification using PHPMailer
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to Gmail
        $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
        $mail->Username   = 'kameshwaranking5658@gmail.com';         // SMTP username (your email address)
        $mail->Password   = 'uwfv jgds sgdk fdql';                   // SMTP password (your email password or app-specific password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           // Enable TLS encryption
        $mail->Port       = 587;                                     // TCP port to connect to

        //Recipients
        $mail->setFrom('kameshwaranking5658@gmail.com', 'Task Manager'); // Set sender email address
        $mail->addAddress('kameshwaranking5658@gmail.com', 'Employee');  // Add recipient (employee's email address)

        // Content
        $mail->isHTML(true);                                          // Set email format to HTML
        $mail->Subject = 'New Task Assigned';
        
        // Start building the email body with an HTML table
        $mail->Body    = '<p>Dear Employee,</p>';
        $mail->Body   .= '<p>The following tasks have been successfully assigned to you:</p>';
        $mail->Body   .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
        $mail->Body   .= '<tr><th>Task Name</th><th>Description</th><th>Priority</th><th>Due Date</th></tr>';

        // Add tasks to email body in table format
        foreach ($tasks as $task) {
            $mail->Body .= '<tr>';
            $mail->Body .= '<td>' . htmlspecialchars($task['taskName']) . '</td>';
            $mail->Body .= '<td>' . htmlspecialchars($task['taskDescription']) . '</td>';
            $mail->Body .= '<td>' . htmlspecialchars($task['priority']) . '</td>';
            $mail->Body .= '<td>' . htmlspecialchars($task['dueDate']) . '</td>';
            $mail->Body .= '</tr>';
        }

        $mail->Body .= '</table>';
        $mail->Body .= '<p>Best regards,</p>';
        $mail->Body .= '<p>Your Task Management System</p>';

        // Send the email
        $mail->send();

        // Return success response
        echo json_encode(['status' => 'success', 'message' => 'Tasks added successfully and email notification sent.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
    }
}
?>
