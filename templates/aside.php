<section class="content__side">
    <?php if (isset($user) && count($user)): ?>
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
    <?php else: ?>
        <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>
        <a class="button button--transparent content__side-button" href="form-authorization.html">Войти</a>
    <?php endif; ?>
</section>
