--
-- список проектов
--
INSERT INTO `projects` (`id`, `projects_name`) VALUES
(5, 'Авто'),
(1, 'Входящие'),
(4, 'Домашние дела'),
(3, 'Работа'),
(2, 'Учеба');
--
-- Список задач
--
INSERT INTO `tasks` (`date_task_creation`, `tasks_name`, `file_reference`, `deadline_task`, `users_id`, `projects_id`) VALUES
('2018-05-09 06:13:56', 'Собеседование в IT компании', '', '2018-10-08 21:00:00', 2, 3),
('2018-05-09 06:13:56', 'Выполнить тестовое задание', '', '2018-10-08 21:00:00', 2, 2),
('2018-05-09 06:13:56', 'Сделать задание первого раздела', '', '2018-10-08 21:00:00', 1, 3),
('2018-05-09 06:13:56', 'Встреча с другом', '', '2018-10-08 21:00:00', 1, 1),
('2018-05-09 06:13:56', 'Заказать пиццу', '', '2018-10-08 21:00:00', 1, 4),
('2018-05-09 06:13:56', 'Купить корм для кота', '', '2018-10-08 21:00:00', 2, 4);
--
-- список пользователей
--
INSERT INTO `users` (`reg_date`, `email`, `name`, `password`, `contacts`) VALUES
('2018-05-09 06:08:33', 'ignat.v@gmail.com', 'Игнат', '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka', 'tel.1234567'),
('2018-05-09 06:10:10', 'kitty_93@li.ru', 'Леночка', '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa', 'tel.2345678'),
('2018-05-09 06:11:10', 'warrior07@mail.ru', 'Руслан', '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW', 'tel.2345678');
--
-- получить список из всех проектов для одного пользователя
--
select projects_name from tasks
join projects on tasks.projects_id = projects.id
where users_id = 2
--
-- получить список из всех задач для одного проекта
--
select tasks_name from tasks WHERE projects_id = 3;
--
-- пометить задачу как выполненную
--
UPDATE `tasks` SET `date_task_execution` = now() WHERE `tasks`.`id` = 2;
--
-- получить все задачи для завтрашнего дня
--
select tasks_name from tasks where deadline_task BETWEEN makedate(year(now()), date_format(now(),'%j')+1) and makedate(year(now()), date_format(now(),'%j')+2);
--
-- обновить название задачи по её идентификатору
--
UPDATE `tasks` SET `tasks_name` = 'Собеседование в IT компании2' WHERE `tasks`.`id` = 1;
