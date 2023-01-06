<?php

namespace App\Service;

class MailService
{

    public function send(array $data)
    {
        $header = "MIME-Version: 1.0\r\n";
        $header .= 'From: "BlogOC"<contact@blogoc.com>'. "\n"; 
        $header .= "Content-Type:text/html; charset='utf-8'" ."\n";
        $header .= "Content-Transfer-Encoding: 8bit";

        ob_start();
        require_once 'C:\wamp64\www\blogOC\src\template\MailTemplate.phtml';
        $message = ob_get_contents();
        ob_end_clean();

        ini_set('SMTP', 'smtp.gmail.com');
        ini_set('smtp_port', '587');
        ini_set('sendmail_from', 'ulfhedinn2@gmail.com');

        return mail('ulfhedinn2@gmail.com', 'BlogOC', $message, $header);
    }

}