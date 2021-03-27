<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php
            foreach ($tasksCategories as $category): ?>
                <li class="main-navigation__list-item <?php if ($currentCategoryId === $category['cat_id']): ?>main-navigation__list-item--active<?php endif; ?>">
                    <a class="main-navigation__list-item-link" href="<?php print('/?category=' . $category['cat_id']); ?>">
                        <?php print($category['cat_name']); ?>
                    </a>
                    <span class="main-navigation__list-item-count">
                                    <?php
                                    print(getTacksCount($tasksList, $category['cat_id']));
                                    ?>
                                </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
       href="pages/form-project.html" target="project_add">Добавить проект</a>
</section>
