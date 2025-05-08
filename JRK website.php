<?php
// Set error reporting for testing
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Function to sanitize form data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get and sanitize form data
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? sanitize_input($_POST['subject']) : 'Contact Form Submission';
    $message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
    
    // Validate inputs
    if (empty($name)) {
        $response['errors'][] = 'Name is required';
    }
    
    if (empty($email)) {
        $response['errors'][] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = 'Invalid email format';
    }
    
    if (empty($message)) {
        $response['errors'][] = 'Message is required';
    }
    
    // If no errors, proceed with email sending
    if (empty($response['errors'])) {
        
        // Recipient email - replace with your email
        $to = "test@example.com";
        
        // Email content
        $email_content = "Name: $name\n";
        $email_content .= "Email: $email\n\n";
        $email_content .= "Message:\n$message\n";
        
        // Email headers
        $headers = "From: $name <$email>" . "\r\n";
        
        // FOR TESTING: Log email instead of sending
        $log_file = 'email_log.txt';
        $log_content = date('Y-m-d H:i:s') . " - TO: $to | SUBJECT: $subject | CONTENT: $email_content\n\n";
        file_put_contents($log_file, $log_content, FILE_APPEND);
        
        // Comment this section during testing to avoid actual emails
        /* 
        // Send email
        if (mail($to, $subject, $email_content, $headers)) {
            $response['success'] = true;
            $response['message'] = 'Your message has been sent. Thank you!';
        } else {
            $response['message'] = 'Sorry, there was an error sending your message.';
        }
        */
        
        // For testing purposes, simulate success
        $response['success'] = true;
        $response['message'] = 'TEST MODE: Your message was logged but not sent. Check email_log.txt';
    }
}

// Return JSON response for AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// For non-AJAX requests, redirect back to the form with status
if ($response['success']) {
    header('Location: index.html?status=success&msg=' . urlencode($response['message']));
} else {
    header('Location: index.html?status=error&msg=' . urlencode(implode(', ', $response['errors'])));
}
exit;
?>