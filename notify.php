<?php
    require_once 'vendor/autoload.php';

    try {
        // Create the Transport
        $transport = (new Swift_SmtpTransport('phpdemo.ru', 25))
            ->setUsername('keks@phpdemo.ru')
            ->setPassword('htmlacademy')
        ;

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message('Wonderful Subject'))
            ->setFrom(['keks@phpdemo.ru' => 'John Doe'])
            ->setTo(['umkasanki@gmail.com'])
            ->setBody('Here is the message itself')
        ;

        // Send the message
        $result = $mailer->send($message);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>