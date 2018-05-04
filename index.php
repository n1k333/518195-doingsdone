<?php
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$cat_project = ['Все', 'Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];

$cat_objective = [
  [
    'tasks' => 'Собеседование в IT компании',
    'cdate' => '01.06.2018',
    'category' => 'Работа',
    'status' => 'Нет'
  ],
  [
    'tasks' => 'Выполнить тестовое задание',
    'cdate' => '25.05.2018',
    'category' => 'Работа',
    'status' => 'Нет'
  ],
  [
    'tasks' => 'Сделать задание первого раздела',
    'cdate' => '25.05.2018',
    'category' => 'Учеба',
    'status' => 'Да'
  ],
  [
    'tasks' => 'Встреча с другом',
    'cdate' => '22.04.2018',
    'category' => 'Входящие',
    'status' => 'Нет'
  ],
  [
    'tasks' => 'Купить корм для кота',
    'cdate' => 'Нет',
    'category' => 'Домашние дела',
    'status' => 'Нет'
  ],
  [
    'tasks' => 'Заказать пиццу',
    'cdate' => 'Нет',
    'category' => 'Домашние дела',
    'status' => 'Нет'
  ],
];

$title = "Дела в порядке";

require_once('functions.php');

$main = renderTemplate('templates/index.php', array(
  'cat_objective' => $cat_objective,
  'show_complete_tasks' => $show_complete_tasks,
));

$layout_content = renderTemplate('templates/layout.php', array(
  'title' => $title,
  'cat_project' => $cat_project,
  'cat_objective' => $cat_objective,
  'main' => $main,

));
print($layout_content);




?>
