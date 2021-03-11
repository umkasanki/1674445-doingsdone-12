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

$asideContent = include_template('aside.php', [
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
]);

$mainContent = include_template('AddTaskMain.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
    'asideContent' => $asideContent,
]);

$layout_content = include_template('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
]);

// обработка формы
if (isset($_FILES['file'])) {
    $fileName = $_FILES['file']['name'];
    $fileUrl = '/uploads/' . $fileName;
    move_uploaded_file($_FILES['file']['tmp_name'], __DIR__ . '/uploads/' . $fileName);
}

print($layout_content);

?>
