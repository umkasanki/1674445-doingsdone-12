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

