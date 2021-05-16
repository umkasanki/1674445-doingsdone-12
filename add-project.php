<?php
session_start();

require ('helpers.php');

$pageTitle = 'Новый таск';

if (isset($_SESSION['userid'])) {
    $user_id = $_SESSION['userid'];
} else {
    header("Location: auth.php"); exit;
}

// db queries
$conn = db_connect('doingsdone');

$get_categories_sql = "SELECT * FROM `categories` WHERE `user_id` = ?";
$get_categories_stmt = mysqli_prepare($conn, $get_categories_sql);
mysqli_stmt_bind_param($get_categories_stmt, 'i', $user_id);
mysqli_stmt_execute($get_categories_stmt);
$getCategoriesRes = mysqli_stmt_get_result($get_categories_stmt);
$tasksCategories = mysqli_fetch_all($getCategoriesRes, MYSQLI_ASSOC);

$getTasksSql = "SELECT * FROM `tasks` WHERE `user_id` = ?";
$getTasksStmt = mysqli_prepare($conn, $getTasksSql);
mysqli_stmt_bind_param($getTasksStmt, 'i', $user_id);
mysqli_stmt_execute($getTasksStmt);
$getTasksRes = mysqli_stmt_get_result($getTasksStmt);
$tasksList = mysqli_fetch_all($getTasksRes, MYSQLI_ASSOC);
// db queries end

function getTacksCount(array $tasksList = [], int $taskCategoryId = 0) {
    $tasksCount = 0;

    foreach ($tasksList as $task) {
        if ($task['category_id'] == $taskCategoryId) {
            $tasksCount++;
        }
    }

    return $tasksCount;
}

// обработка формы
if (isset($_FILES['file'])) {
    $fileName = $_FILES['file']['name'];
    $uploadPath = __DIR__ . '/uploads/';
    $fileUrl = '/uploads/' . $fileName;
    move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath . $fileName);
}

function getFilesVal($name) {
    if (isset($_FILES[$name])) {
        $fileName = $_FILES[$name]['name'];
        $fileUrl = '/uploads/' . $fileName;
        return compact('fileName', 'fileUrl');
    }
    // @todo вопрос: нужен ли тут return?
}

function validateFilled($name) {
    if (empty($_POST[$name])) {
        return "Это поле должно быть заполнено";
    }
}

function validateEmail($name) {
    if (!filter_input(INPUT_POST, $name, FILTER_VALIDATE_EMAIL)) {
        return "Введите корректный email";
    }
}

// @todo вопрос: Для идентификатора выбранного проекта проверять, что он ссылается на реально существующий проект.
//function validateCategory() {
//    foreach ($tasksCategories as $value) {
//        if ($value['cat_id'] == $_POST['project']) {
//            return true;
//        }
//    }
//    return 'Выберите проект';
//}

$errors = [];
foreach ($_POST as $key => $value) {
    if ($key == 'name') {
        $len = strlen($_POST[$key]);
        if ($len < 1) {
            $errors[$key] = 'Введите название проекта';
        }
    }
}

$errors = array_filter($errors);

// отправка запросов
if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($errors) === 0) {
    $addTaskQr = "INSERT INTO `categories` (cat_name, user_id)
                  VALUES (?, ?)";

    $stmp = mysqli_prepare($conn, $addTaskQr);

    $name = get_post_val('name');
    $user_id = $user_id;

    mysqli_stmt_bind_param($stmp, 'si',
        $name, $user_id);

    $addTaskQrResult = mysqli_stmt_execute($stmp);

    if (!$addTaskQrResult) {
        $error = mysqli_error($conn);
        print("Ошибка MySQL: " . $error);
    }
}

// шаблонизация
$asideContent = include_template('aside.php', [
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
]);

$mainContent = include_template('addProject.php', [
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
    'asideContent' => $asideContent,
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
]);

print($layout_content);

?>
