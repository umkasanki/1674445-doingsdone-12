<?php
require ('helpers.php');
$pageTitle = 'Регистрация';

$asideContent = include_template('registerAside.php');
$mainContent = include_template('registerMain.php', [
    'asideContent' => $asideContent,
]);
$layout_content = include_template('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
]);

print($layout_content);
?>
