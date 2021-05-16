<?php
session_start();

require ('helpers.php');

$pageTitle = 'Новый таск';

if (isset($_SESSION['userid'])) {
    $userId = $_SESSION['userid'];
} else {
    header("Location: auth.php"); exit;
}

// db queries
$conn = db_connect('doingsdone');

$get_categories_sql = "SELECT * FROM `categories` WHERE `user_id` = ?";
$get_categories_stmt = mysqli_prepare($conn, $get_categories_sql);
mysqli_stmt_bind_param($get_categories_stmt, 'i', $userId);
mysqli_stmt_execute($get_categories_stmt);
$getCategoriesRes = mysqli_stmt_get_result($get_categories_stmt);
$tasksCategories = mysqli_fetch_all($getCategoriesRes, MYSQLI_ASSOC);

$getTasksSql = "SELECT * FROM `tasks` WHERE `user_id` = ?";
$getTasksStmt = mysqli_prepare($conn, $getTasksSql);
mysqli_stmt_bind_param($getTasksStmt, 'i', $userId);
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
$rules = [
    'name' => function() {
        return validateFilled('name');
    },
    'project' => function() {
        // не понял как ошибку поправить. видимо что-то с областью видимости
        // return validateCategory();
        return validateFilled('project');
    },
    'date' => function() {
        if (!is_date_valid($_POST['date'])) {
            return 'Выберите дату';
        }

        $taskExpireDate = date_create_from_format('Y-m-d', $_POST['date']);
        $currDate = date_create('now');

        if ($taskExpireDate < $currDate) {
            return 'Выберите корректную дату';
        }
    },
    'file' => function() {
        if (isset($_FILES['file'])) {
            if ($_FILES['file']['size'] > 200000) {
                return "Максимальный размер файла: 200Кб";
            }
        }
    },
];

//var_dump( strtotime('2012-03-25') );

foreach ($_POST as $key => $value) {
    if (isset($rules[$key])) {
        $rule = $rules[$key];
        $errors[$key] = $rule($value);
    }
}

foreach ($_FILES as $key => $value) {
    if (isset($rules[$key])) {
        $rule = $rules[$key];
        $errors[$key] = $rule($value);
    }
}

$errors = array_filter($errors);

// отправка запросов
if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($errors) === 0) {
    $currDate = date_format(date_create('now'), 'Y-m-d');
    $addTaskQr = "INSERT INTO `tasks` (publish_date, status, name, file_url, expire_date, user_id, category_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmp = mysqli_prepare($conn, $addTaskQr);

    $name = get_post_val('name');
    $date = get_post_val('date');
    $project = get_post_val('project');
    $status = 0;
    $userId = 5;
    $fileUrl = getFilesVal('file')['fileUrl'];

    mysqli_stmt_bind_param($stmp, 'sisssii',
        $currDate, $status, $name, $fileUrl, $date, $userId, $project);

    $addTaskQrResult = mysqli_stmt_execute($stmp);

    if (!$addTaskQrResult) {
        $error = mysqli_error($conn);
        print("Ошибка MySQL: " . $error);
    } else {
        header("Location: index.php"); exit;
    }
}

// шаблонизация
$asideContent = include_template('aside.php', [
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
]);

$mainContent = include_template('addTaskMain.php', [
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
