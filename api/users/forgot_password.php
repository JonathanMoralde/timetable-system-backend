<?php
// * API FOR SENDING PASSWORD RESET LINK
// TODO CHANGE THE LINK OF THE CHANGE PASSWORD PAGE
// TODO CONVERT TO USING PREPARED STATEMENTS

include_once '../../includes/db.php';
include '../../includes/header.php';
header("Content-Type: multipart/form-data");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';


if(isset($_POST['email'])){

    $email= $_POST['email'];
    // $email = mysqli_escape_string($conn, $_POST['email']);
    $token = md5(rand());

    $check_email = "SELECT email FROM users WHERE email='$email' LIMIT 1";
    $check_email_run = mysqli_query($conn, $check_email);

    if(mysqli_num_rows($check_email_run) > 0){
        $row = mysqli_fetch_array($check_email_run);
        $fetch_email = $row['email'];

        $update_token = "UPDATE users SET verify_token ='$token' WHERE email='$fetch_email' LIMIT 1";
        $update_token_run = mysqli_query($conn, $update_token);

        if($update_token_run){
                send_password_reset($fetch_email, $token);
                echo json_encode(array("success"=>true, "data"=>"Password reset link sent, please check you email!"));
                // $_SESSION['success'] = "Success";
                // header("location: forgot-password.php?success=1");
            }else{
                // failed to update token
                echo json_encode(array("success"=>false, "data"=>"Failed to send password reset link, please try again later!"));
        }
    }else{
        // email does not exist/not registered
        echo "no email found";
    }
}


function send_password_reset($fetch_email, $token){
    $mail = new PHPMailer(true);
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debugging

    try{
    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = "smtp.gmail.com";
    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Username = "automatedclasstimetabling.sys@gmail.com";
    $mail->Password = "trpp jxhz vxzr bsii";
    
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;


    $name = 'ACTS';
    $mail->setFrom("automatedclasstimetabling.sys@gmail.com",$name);
    $mail->addAddress($fetch_email);

    $mail->isHTML(true);
    $mail->Subject = "Password reset link from Autoamted Class Timetabling System";

    
    $email_template = "
    <h2>To reset your password, please click on the link below. If you did not request a password reset, please disregard this email</h2>
    <h5>Reset your password by clicking on the link</h5>
    <a href='http://localhost:9000/reset-password/$token/$fetch_email'> Click Here </a>
    <br></br>

    ";
    // $mail->Debugoutput = 'html'; // Display debug output as HTML
    
   $mail->Body = $email_template;
   $mail->send();
//    echo 'Message has been sent';
    }
    catch (Exception $e){
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


