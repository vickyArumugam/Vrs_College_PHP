<?php
include 'cors.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Path to Composer's autoload file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $paperTitle = htmlspecialchars($_POST['paper_title']); // Sanitize input
    $authorName = htmlspecialchars($_POST['author_name']);
    $mobileNo = htmlspecialchars($_POST['mobile_no']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitize email
    $institution = htmlspecialchars($_POST['institution']);
    $category = htmlspecialchars($_POST['category']);

    // Handle file upload with additional validation
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];

        // Allowed file extensions
        $allowedExtensions = ['pdf', 'docx', 'pptx'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // Check if the file type is allowed
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            echo "Invalid file type. Only PDF, DOCX, and PPTX files are allowed.";
            exit;
        }

        // Set file upload directory and check if it exists
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        $destFilePath = $uploadDir . $fileName;
        
        // Move the file to the desired directory
        if (move_uploaded_file($fileTmpPath, $destFilePath)) {
            echo "File is successfully uploaded.\n";
        } else {
            echo "There was an error uploading the file.\n";
        }
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);  // This should not raise the 'Undefined type' error if autoload is working correctly
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com;';   // Set the SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'vignesh9080bit@gmail.com'; // SMTP username
        $mail->Password = 'ucplzutufuxmynyi'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('vignesh9080bit@gmail.com');
        $mail->addAddress('vignesh9080bit@gmail.com');

        // Attachments (only if the file was uploaded)
        if (isset($destFilePath)) {
            $mail->addAttachment($destFilePath);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Paper Submission: ' . $paperTitle;
        $mail->Body    = "
            <h1>Paper Title: $paperTitle</h1>
            <p><strong>Author Name:</strong> $authorName</p>
            <p><strong>Mobile Number:</strong> $mobileNo</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Institution:</strong> $institution</p>
            <p><strong>Category:</strong> $category</p>
        ";

        // Send email
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
