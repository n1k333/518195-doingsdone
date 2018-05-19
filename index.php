<?php
// соединение с mysqli
$link = mysqli_connect("localhost", "root", "", "things_are_fine");
if (!$link) {
    printf("Текст ошибки: %s\n", mysqli_connect_error());
    exit();
}
// определяем user_id;
$user_id = 2;

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
// определяем $title
$title = "Дела в порядке";
// подключаем файл с функциями
require_once('functions.php');
// проверяем $_GET['page'] и присваеваем результат переменной $category
$category = !isset($_GET['page'])?'/':$_GET['page'];
// получаем список категорий с task=tasks.tasks_name,
// cdate=tasks.deadline_task, category=projects.projects_name,
// status=tasks.date_task_execution

$cat_objective = getCatObjective($user_id, $category, $link);
if (empty($cat_objective)) {
  header('Location: 404.php');
  exit();
}

$main = renderTemplate('templates/index.php', array(
  'cat_objective' => $cat_objective,
  'show_complete_tasks' => $show_complete_tasks,
));
$cat_projects = getCategoriesByUser($user_id, $link);

$layout_content = renderTemplate('templates/layout.php', array(
  'category' => $category,
  'title' => $title,
  'cat_projects' => $cat_projects,
  'cat_objective' => $cat_objective,
  'main' => $main,
));
print($layout_content);

mysqli_close($link);
