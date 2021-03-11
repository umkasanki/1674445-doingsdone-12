<?php
require ('helpers.php');

$pageTitle = 'Новый таск';

// db queries
$conn = mysqli_connect('mysql-5.7-33062.database.nitro', 'nitro', 'nitro', 'doit');
if ($conn === false) {
    print_r('DB connection error' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8');

$getCategoriesQr = "SELECT * FROM `categories` WHERE `user_id` = 1";
$getTasksQr = "SELECT * FROM `tasks` WHERE `user_id` = 1";

$getCategoriesQrRes = mysqli_query($conn, $getCategoriesQr);
$getTasksQrRes = mysqli_query($conn, $getTasksQr);

if (!$getCategoriesQrRes || !$getTasksQrRes) {
    print_r('MySQL error:' . mysqli_error($conn));
}

$tasksCategories = mysqli_fetch_all($getCategoriesQrRes, MYSQLI_ASSOC);
$tasksList = mysqli_fetch_all($getTasksQrRes, MYSQLI_ASSOC);
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

function getPostVal($name) {
    return $_POST[$name] ?? "";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isFormSubmitted = true;
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

function isCorrectLength($name, $min, $max) {
    $len = strlen($_POST[$name]);

    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }
}

// Для идентификатора выбранного проекта проверять, что он ссылается на реально существующий проект.
function validateCategory() {
    foreach ($tasksCategories as $value) {
        if ($value['cat_id'] == $_POST['project']) {
            return true;
        }
    }
    return 'Выберите проект';
}

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
    },
];

//var_dump( strtotime('2012-03-25') );

foreach ($_POST as $key => $value) {
    if (isset($rules[$key])) {
        $rule = $rules[$key];
        $errors[$key] = $rule($value);
    }
}

$errors = array_filter($errors);

$asideContent = include_template('aside.php', [
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
]);

$mainContent = include_template('AddTaskMain.php', [
    'show_complete_tasks' => $show_complete_tasks,
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
