<?php
session_start();

require ('helpers.php');

$page_title = 'Новый таск';

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
$get_categories_result = mysqli_stmt_get_result($get_categories_stmt);
$tasks_categories = mysqli_fetch_all($get_categories_result, MYSQLI_ASSOC);

$get_tasks_sql = "SELECT * FROM `tasks` WHERE `user_id` = ?";
$getTasksStmt = mysqli_prepare($conn, $get_tasks_sql);
mysqli_stmt_bind_param($getTasksStmt, 'i', $user_id);
mysqli_stmt_execute($getTasksStmt);
$getTasksRes = mysqli_stmt_get_result($getTasksStmt);
$tasks_list = mysqli_fetch_all($getTasksRes, MYSQLI_ASSOC);
// db queries end

function getTacksCount(array $tasks_list = [], int $taskCategoryId = 0) {
    $tasksCount = 0;

    foreach ($tasks_list as $task) {
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
//    foreach ($tasks_categories as $value) {
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
$aside_content = include_template('aside.php', [
    'tasks_categories' => $tasks_categories,
    'tasks_list' => $tasks_list,
]);

$main_content = include_template('addProject.php', [
    'tasks_categories' => $tasks_categories,
    'tasks_list' => $tasks_list,
    'aside_content' => $aside_content,
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
    'page_title' => $page_title,
    'main_content' => $main_content,
]);

print($layout_content);

?>
