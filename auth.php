<?php
session_start();

require ('helpers.php');

$pageTitle = 'Авторизация';
$user = [];

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

// validation
$errors = [];
foreach ($_POST as $key => $value) {
    if ($key == 'email') {
        $len = strlen($_POST[$key]);
        if ($len < 1) {
            $errors[$key] = 'Введите email';
        }
        elseif ($len > 0 && filter_var($value, FILTER_VALIDATE_EMAIL) == false) {
            $errors[$key] = 'Введите корректный емайл';
        } else {
            foreach ($usersList as $item) {
                if ($item['email'] == $_POST['email']) {
                    $user = $item;
                }
            }

            if (!count($user)) {
                $errors[$key] = 'Пользователь не найден';
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
        if (count($user)) {
            if (!password_verify($_POST['password'], $user['password'])) {
                $errors[$key] = 'неверный пароль';
            }
        }
    }
}

$errors = array_filter($errors);

// auth
if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($errors) === 0) {

    $_SESSION['userid'] = $user['id'];
    $_SESSION['username'] = $user['name'];

    if (isset($_SESSION['userid'])) {
        header("Location: index.php"); exit;
    }
}


if (isset($_SESSION['userid'])) {
//    header("Location: index.php"); exit;
    echo ($_SESSION['userid']);
}


//templating
$asideContent = include_template('aside.php', [
    'user' => $user,
]);
$mainContent = include_template('authMain.php', [
    'asideContent' => $asideContent,
    'errors' => $errors,
]);
$layout_content = include_template('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
    'user' => $user,
]);

print($layout_content);
?>
