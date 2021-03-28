<?php
require ('helpers.php');

$pageTitle = 'Авторизация';

$user = ['test'];

// validation
$errors = [];

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
