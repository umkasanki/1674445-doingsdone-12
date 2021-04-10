<div class="content">
    <?php print($asideContent); ?>

    <main class="content__main">
        <h2 class="content__main-heading">Список задач</h2>

        <form class="search-form" action="index.php" method="get" autocomplete="off">
            <input class="search-form__input" type="text" name="q" value="" placeholder="Поиск по задачам">

            <input class="search-form__submit" type="submit" name="" value="Искать">
        </form>

        <div class="tasks-controls">
            <nav class="tasks-switch">
                <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                <a href="/" class="tasks-switch__item">Повестка дня</a>
                <a href="/" class="tasks-switch__item">Завтра</a>
                <a href="/" class="tasks-switch__item">Просроченные</a>
            </nav>

            <label class="checkbox">
                <!--добавить сюда атрибут "checked", если переменная $show_complete_tasks равна единице-->
                <input class="checkbox__input visually-hidden show_completed" type="checkbox"
                       <?php if ($show_complete_tasks === 1): ?>checked<?php endif; ?>
                >
                <span class="checkbox__text">Показывать выполненные</span>
            </label>
        </div>

        <table class="tasks">
            <?php foreach ($tasksList as $task): ?>
                <?php if (!$show_complete_tasks && $task['status']) { continue; } ?>
                <?php if ($currentCategoryId !== null && ($task['category_id'] != $currentCategoryId)) { continue; } ?>

                <tr class="tasks__item task <?php if ($task['status']) {
                    print('task--completed');
                } ?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <?php if ($task['status']): ?>
                                <input class="checkbox__input visually-hidden" type="checkbox" checked>
                            <?php else: ?>
                                <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                            <?php endif; ?>
                            <span class="checkbox__text">
                                        <?php print($task['name']); ?>
                                    </span>
                        </label>
                    </td>

                    <td class="task__file">
                        <?php if ($task['file_url']): ?>
                            <?php
                                $fileUrlArray = explode("/", $task['file_url']);
                                $fileName = $fileUrlArray[count($fileUrlArray) - 1];
                            ?>
                            <a class="download-link" href="<?php print($task['file_url']); ?>"><?php print($fileName); ?></a>
                        <?php endif; ?>
                    </td>

                    <td class="task__date"><?php print($task['expire_date']); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if(!count($tasksList)): ?>
                <p>Ничего не найдено</p>
            <?php endif; ?>
        </table>
    </main>
</div>
