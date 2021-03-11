<?php
require ('helpers.php');

$pageTitle = 'Главная';

$show_complete_tasks = rand(0, 1);

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

// get an id of current category from url param
$currentCategoryId = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);

// show 404 if count of tasks in the current category < 1
$setNotFound = true;
foreach ($tasksList as $task) {
    if ($task['category_id'] === $currentCategoryId) {
        $setNotFound = false;
    }
}

if ($currentCategoryId !== null && $setNotFound) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}

$mainContent = include_template('main.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
]);

$layout_content = include_template('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
]);


print($layout_content);

?>
