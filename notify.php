<?php
require_once 'vendor/autoload.php';
require_once 'DbHelper.php';
require_once 'helpers.php';

$dbHelper = new DbHelper("mysql", "mysql", "127.0.0.1", "doit");

$tasksData = null;

if (!$dbHelper->getLastError()) {
    $tasksData = mysqli_fetch_all($dbHelper->executeQuery(
        "SELECT u.id, u.email, u.name, t.name, t.user_id, t.expire_date, t.id as task_id
            FROM users u
            JOIN tasks t
            ON u.id = t.user_id
            WHERE t.status = 0
            AND expire_date = CURRENT_DATE();"));
}
else {
    print($dbHelper->getLastError());
}

if (!$dbHelper->getLastError()) {
    $tasksByUser = mysqli_fetch_all($dbHelper->executeQuery(
        'SELECT u.id, u.email, u.name, t.name, t.user_id, t.expire_date, t.id as task_id
            FROM users u
            JOIN tasks t
            ON u.id = t.user_id
            WHERE t.status = 0
            AND expire_date = CURRENT_DATE()'
    ));
}
else {
    print($dbHelper->getLastError());
}

$notificationData = array();

// test
$notificationDataSchema = array(
    'oleg' => array(
        'name' => 'Oleg',
        'email' => 'umkasanki@gmail.com',
        'tasks' => array(
            array(
                'task name',
                'task date',
            ),
            array(
                'task 2 name',
                'task 2 date',
            ),
        )
    ),
    'denis' => array(
        'name' => 'Denis',
        'email' => 'denis@tishkin.com',
        'tasks' => array(
            array(
                'task 3 name',
                'task 3 date',
            ),
            array(
                'task 5 name',
                'task 5 date',
            ),
        )
    ),
);

$userArr = array(
    ('user' . '3') => array(
        'name' => 'Roma',
        'email' => 'roma@tishkin.com',
        'tasks' => array(
            array(
                'task 3 name',
                'task 3 date',
            ),
            array(
                'task 5 name',
                'task 5 date',
            ),
        )
    ),
);

$notificationDataSchema = array_merge($notificationDataSchema, array(
    ('user' . '3') => array(
        'name' => 'Roma',
        'email' => 'roma@tishkin.com',
        'tasks' => array(
            array(
                'task 3 name',
                'task 3 date',
            ),
            array(
                'task 5 name',
                'task 5 date',
            ),
        )
    ),
));

dump($notificationDataSchema);

foreach ($notificationDataSchema as $key => $value) {
    print '<h3>new email</h3>';
    print '<div>' . $value['name'] . '</div>';
    print '<div>' . $value['email'] . '</div>';

    foreach ($value['tasks'] as $task) {
        print '<li>' . $task[0] . '</li>';
        print '<li>' . $task[1] . '</li>';
        print '<br>';
    }
    print '<hr>';
}

//dump($tasksData);

foreach ($tasksData as $taskLine) {
    if (!array_key_exists(('user' . $taskLine[0]), $tasksData)) {
        $notificationData = array_merge($notificationData, array(
            ('user' . $taskLine[0]) => array(),
        ));
        $notificationData[('user' . $taskLine[0])]['tasks'] = array();
    }
    $notificationData[('user' . $taskLine[0])]['email'] = $taskLine[1];
    $notificationData[('user' . $taskLine[0])]['name'] = $taskLine[2];
    array_push($notificationData[('user' . $taskLine[0])]['tasks'], array(
        $taskLine[3],
        $taskLine[5]
    ));
}

foreach ($notificationData as $key => $value) {
    print '<br>';
    print '<h3>new email</h3>';
    print '<div>' . $value['name'] . '</div>';
    print '<div>' . $value['email'] . '</div>';

    foreach ($value['tasks'] as $task) {
        print '<li>' . $task[0] . '</li>';
        print '<li>' . $task[1] . '</li>';
        print '<br>';
    }
    print '<hr>';
}

dump($notificationData);

//if (count($notificationData)) {
//    // Create the Transport
//    $transport = (new Swift_SmtpTransport('phpdemo.ru', 25))
//        ->setUsername('keks@phpdemo.ru')
//        ->setPassword('htmlacademy')
//    ;
//    // Create the Mailer using your created Transport
//    $mailer = new Swift_Mailer($transport);
//
//    foreach ($notificationDataSchema as $key => $value) {
//        try {
//            // Create a message
//            $message = new Swift_Message();
//            $message->setSubject('Уведомление от сервиса «Дела в порядке');
//            $message->setFrom(['keks@phpdemo.ru']);
//            $message->addTo($notificationDataSchema[$key]['email']);
//
//            $messageText = '<b>Уважаемый, ' . $key . ' следующие задачи:</b><br>';
//
//            foreach ($notificationData[$userName]['tasks'] as $task) {
//                $messageText .= ('- У вас запланирована задача' . $task[0] . ' на  ' . $task[1] . '<br>');
//            }
//            $message->setBody($messageText, 'text/html');
//
//            // Send the message
////            $result = $mailer->send($message);
//        } catch (Exception $e) {
//            echo $e->getMessage();
//        }
//    }
//}

?>
