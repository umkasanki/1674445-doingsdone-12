<?php
session_start();

require ('helpers.php');

$pageTitle = 'Главная';

$show_complete_tasks = filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_STRING);

if (isset($_SESSION['userid'])) {
    $userId = $_SESSION['userid'];
} else {
    header("Location: guest.php"); exit;
}

// db queries
$conn = db_connect('doingsdone');

$getCategoriesSql = "SELECT * FROM `categories` WHERE `user_id` = ?";
$getCategoriesStmt = mysqli_prepare($conn, $getCategoriesSql);
mysqli_stmt_bind_param($getCategoriesStmt, 'i', $userId);
mysqli_stmt_execute($getCategoriesStmt);

$getCategoriesRes = mysqli_stmt_get_result($getCategoriesStmt);
$tasksCategories = mysqli_fetch_all($getCategoriesRes, MYSQLI_ASSOC);

$taksFilterDate = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);
if ($taksFilterDate == 'outdated') {
    $getTasksSql = "SELECT * FROM `tasks` WHERE `user_id` = ? and expire_date < CURDATE()";
} elseif ($taksFilterDate == 'today') {
    $getTasksSql = "SELECT * FROM `tasks` WHERE `user_id` = ? and
                            (expire_date = CURRENT_DATE())";
} elseif ($taksFilterDate == 'tomorrow') {
    $getTasksSql = "SELECT * FROM `tasks` WHERE `user_id` = ? and
                            (expire_date < DATE_ADD(NOW(), INTERVAL 1 DAY) and expire_date > CURDATE())";
} else {
    $getTasksSql = "SELECT * FROM `tasks` WHERE `user_id` = ?";
}
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

// get an id of current category from url param
$currentCategoryId = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);

// Search

$searchQuery = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_URL);

if ($searchQuery) {
    $sql = "SELECT * FROM `tasks` WHERE MATCH(name) AGAINST(?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $searchQuery);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $tasksList = mysqli_fetch_all($res, MYSQLI_ASSOC);
}

// show 404 if count of tasks in the current category < 1
$setNotFound = true;

foreach ($tasksList as $task) {
//    print('<br>');
//    print('catid' . ' - ' . $task['category_id'] . ' - ' . $currentCategoryId);
    if ($task['category_id'] === $currentCategoryId) {
        $setNotFound = false;
    }
}

// invert tasks status
// get data from url param
$currentTaskId = filter_input(INPUT_GET, 'task_id', FILTER_SANITIZE_NUMBER_INT);
$currentTaskStatus = filter_input(INPUT_GET, 'check', FILTER_SANITIZE_NUMBER_INT);

if ($currentTaskId) {
    if ($currentTaskStatus == 1) {
        $status = 0;
    } else {
        $status = 1;
    }
//    print('$currentTaskStatus' . ' ' . $currentTaskStatus . '<br>');
//    print('$status' . ' ' . $status . '<br>');
    $sql = "UPDATE `tasks` SET `status` = ? WHERE `id` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $status, $currentTaskId);
    mysqli_stmt_execute($stmt);
    header("Location: index.php"); exit;
}
// invert tasks status end

$asideContent = include_template('aside.php', [
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
]);

$mainContent = include_template('main.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
    'asideContent' => $asideContent,
    'currentCategoryId' => $currentCategoryId,
    'taksFilterDate' => $taksFilterDate,
]);

$layout_content = include_template('layout.php', [
    'pageTitle' => $pageTitle,
    'mainContent' => $mainContent,
]);


print($layout_content);

?>
