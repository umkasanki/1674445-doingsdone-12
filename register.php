<?php
require ('helpers.php');
$pageTitle = 'Регистрация';

//get data
$conn = mysqli_connect('127.0.0.1', 'mysql', 'mysql', 'doit');
if ($conn === false) {
    print('DB connection error' . mysqli_connect_error());
    exit();
}

mysqli_set_charset($conn, 'utf8');
$getUsersQr = "SELECT * FROM `users`";
$getUsersQrRes = mysqli_query($conn, $getUsersQr);
$usersList = mysqli_fetch_all($getUsersQrRes, MYSQLI_ASSOC);

foreach ($usersList as $user) {
    var_dump($user['email']);
    var_dump($user['name']);
}

// validation
$errors = [];

foreach ($_POST as $key => $value) {
    if ($key == 'email') {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) == false) {
            $errors[$key] = 'Введите корректный емайл';
        } else {
            foreach ($usersList as $user) {
                if ($user['email'] == $_POST['email']) {
                    $errors[$key] = 'Этот емайл занят, выберите другой';
                }
            }
        }
    }

    if ($key == 'password') {
        $len = strlen($_POST[$key]);
        if ($len < 1) {
            $errors[$key] = 'Введите пароль';
        }
        if ($len < 8 and $len > 0) {
            $errors[$key] = 'Длина пароля должна быть не менее 8 символов';
        }
    }
    if ($key == 'name') {
        $len = strlen($_POST[$key]);
        if ($len < 1) {
            $errors[$key] = 'Введите логин';
        } else {
            foreach ($usersList as $user) {
                if ($user['name'] == $_POST['name']) {
                    $errors[$key] = 'Этот логин занят, выберите другой';
                }
            }
        }

    }
}


$errors = array_filter($errors);

if (count($errors)) {
//    print (getPostVal('name'));
    var_dump($_POST);
    print ('<br>');
    var_dump($errors);
//    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($errors) === 0) {
    var_dump('register user');
}

//templating
$asideContent = include_template('registerAside.php');
$mainContent = include_template('registerMain.php', [
    'asideContent' => $asideContent,
    'errors' => $errors,
]);
$layout_content = include_template('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
]);

print($layout_content);
?>
