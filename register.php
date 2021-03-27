<?php
require ('helpers.php');
$pageTitle = 'Регистрация';

// validation
$errors = [];

foreach ($_POST as $key => $value) {
    if ($key == 'email') {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) == false) {
            $errors[$key] = 'Введите корректный емайл';
        }
    }
    if ($key == 'password') {
        if (empty($POST[$key])) {
            $errors[$key] = 'Введите пароль';
        }
        $len = strlen($_POST[$key]);
        if ($len < 8 and $len > 0) {
            $errors[$key] = 'Длина пароля должна быть не менее 8 символов';
        }
    }
    if ($key == 'name') {
        if (empty($POST[$key])) {
            $errors[$key] = 'Введите логин';
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
