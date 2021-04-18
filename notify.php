<?php
require_once 'vendor/autoload.php';
require_once 'DbHelper.php';

$dbHelper = new DbHelper("mysql", "mysql", "127.0.0.1", "doit");

if (!$dbHelper->getLastError()) {
    $tasks = mysqli_fetch_all($dbHelper->executeQuery("SELECT * FROM tasks WHERE status = 0 AND expire_date = CURRENT_DATE()"));
}
else {
    print($dbHelper->getLastError());
}

if (count($tasks)) {
    try {
        // Create the Transport
        $transport = (new Swift_SmtpTransport('phpdemo.ru', 25))
            ->setUsername('keks@phpdemo.ru')
            ->setPassword('htmlacademy')
        ;

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = new Swift_Message();
        $message->setSubject('Уведомление от сервиса «Дела в порядке');
        $message->setFrom(['keks@phpdemo.ru']);
        $message->addTo('umkasanki@gmail.com');

        $messageText = '<b>Сегодня нужно выполнить следующие задачи:</b><br>';

        foreach ($tasks as $task) {
            $messageText .= ('- ' . $task[3] . '<br>');
        }
        $message->setBody($messageText, 'text/html');

        // Send the message
        $result = $mailer->send($message);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>