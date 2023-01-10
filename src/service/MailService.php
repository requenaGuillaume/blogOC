<?php

namespace App\Service;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{

    private PHPMailer $phpMailer;


    public function __construct()
    {
        $this->phpMailer = new PHPMailer(true);
    }


    public function send(array $data): bool
    {
        try {
            // Server settings
            $this->phpMailer->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->phpMailer->isSMTP();
            $this->phpMailer->SMTPAuth   = true;
            $this->phpMailer->Host = 'smtp.mailtrap.io';
            $this->phpMailer->Port = 2525;
            $this->phpMailer->Username = 'be1ed0603d4451';
            $this->phpMailer->Password = 'dad57734a9e6eb';

            // Recipients
            $this->phpMailer->setFrom('from@example.com', 'Mailer');
            $this->phpMailer->addAddress('joe@example.net', 'Joe User');

            // Charset
            $this->phpMailer->CharSet = PHPMailer::CHARSET_UTF8;

            // Get html content
            ob_start();
            require('src/template/MailTemplate.phtml');
            $htmlBody = ob_get_clean();
            echo $htmlBody;
            exit;
        
            // Content
            $this->phpMailer->isHTML(true);
            $this->phpMailer->Subject = "Contact by {$data['fullName']}";
            $this->phpMailer->Body    = $htmlBody;
            // $this->phpMailer->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $this->phpMailer->send();
            return true;
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$this->phpMailer->ErrorInfo}";
            return false;
        }
    }

}