<?php

$siteTitle = 'Дела в порядке';
$show_complete_tasks = rand(0, 1);

$tasksCategories = [
    "Входящие",
    "Учеба",
    "Работа",
    "Домашние дела",
    "Авто"
];

$tasksList = [
    [
        'taskName' => 'Собеседование в IT компании',
        'taskCompleteDate' => '	01.03.2021',
        'taskCategory' => 'Работа',
        'taskCompleteStatus' => true,
    ],
    [
        'taskName' => 'Выполнить тестовое задание',
        'taskCompleteDate' => '25.02.2021',
        'taskCategory' => 'Работа',
        'taskCompleteStatus' => false,
    ],
    [
        'taskName' => 'Сделать задание первого раздела',
        'taskCompleteDate' => '21.02.2021',
        'taskCategory' => 'Учеба',
        'taskCompleteStatus' => false,
    ],
    [
        'taskName' => 'Встреча с другом',
        'taskCompleteDate' => '02.03.2021',
        'taskCategory' => 'Входящие',
        'taskCompleteStatus' => false,
    ],
    [
        'taskName' => 'Купить корм для кота',
        'taskCompleteDate' => null,
        'taskCategory' => 'Домашние дела',
        'taskCompleteStatus' => false,
    ],
    [
        'taskName' => 'Купить корм для кота',
        'taskCompleteDate' => null,
        'taskCategory' => 'Домашние дела',
        'taskCompleteStatus' => false,
    ]
];

function getTacksCount(array $tasksList = [], string $taskCategoryName = '') {
    $tasksCount = 0;

    foreach ($tasksList as $task) {
        if ($task['taskCategory'] === $taskCategoryName) {
            $tasksCount++;
        }
    }

    return $tasksCount;
}

function checkTaskUrgency($taskCompleteDate) {
    $isTaskUrgent = false;

    if ($taskCompleteDate === null) {
        return $isTaskUrgent;
    }

    $currTimestamp = time();
    $completeTimestamp = strtotime($taskCompleteDate);
    $leftTimestamp = strtotime($taskCompleteDate) - time();
    $leftHours = round($leftTimestamp / 3600);

    if ($leftHours <= 24 && $leftHours > 0) {
        $isTaskUrgent = true;
    }

    return $isTaskUrgent;
}

require ('helpers.php');

$mainContent = include_template('main.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasksCategories' => $tasksCategories,
    'tasksList' => $tasksList,
]);

$layout_content = include_template('layout.php', [
    'pageTitle' => 'Главная | ' . $siteTitle,
    'mainContent' => $mainContent,
]);


print($layout_content);



