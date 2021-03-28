<?php
require ('helpers.php');

$pageTitle = 'Авторизация';

// validation
$errors = [];

//templating
$asideContent = include_template('registerAside.php');
$mainContent = include_template('authMain.php', [
    'asideContent' => $asideContent,
    'errors' => $errors,
]);
$layout_content = include_template('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
]);

print($layout_content);
?>
