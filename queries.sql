-- Запросы для добавления информации в БД

INSERT INTO users (name,
                   email,
                   password)
VALUES ('Oleg', 'oleg@tishkin.by', 'oleg_pass'),
       ('Denis', 'denis@tishkin.by', 'denis_pass');

INSERT INTO categories (cat_name,
                        user_id)
VALUES ('Учеба', 1),
       ('Работа', 1),
       ('Авто', 2),
       ('Входящие', 1),
       ('Домашние дела', 2);

INSERT INTO tasks (publish_date,
                   status,
                   name,
                   expire_date,
                   user_id,
                   category_id)
VALUES
       ('2021-02-23', 0, 'Выполнить тестовое задание', '2021-03-20', 1, 3),
       ('2021-02-24', 1, 'Собеседование в IT компании', '2021-03-21', 2, 4),
       ('2021-02-25', 0, 'Сделать задание первого раздела', '2021-03-22', 1, 5),
       ('2021-02-26', 1, 'Сделать задание первого раздела', '2021-03-23', 1, 6),
       ('2021-02-27', 0, 'Встреча с другом', '2021-03-24', 1, 7);


-- Запросы для действий

-- получить список из всех проектов для одного пользователя;
SELECT * FROM `tasks` WHERE `user_id` = 2;

-- получить список из всех задач для одного проекта
SELECT * FROM `tasks` WHERE `category_id` = 4;

-- пометить задачу как выполненную
UPDATE `tasks` SET `status` = 1 WHERE `id` = 5;

-- обновить название задачи по её идентификатору
UPDATE `tasks` SET `name` = 'Переименнованный таск' WHERE `id` = 4;
