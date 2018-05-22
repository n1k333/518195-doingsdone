<?php
// соединение с mysqli
$link = mysqli_connect("localhost", "root", "", "things_are_fine");
if (!$link) {
    printf("Текст ошибки: %s\n", mysqli_connect_error());
    exit();
}
// определяем user_id;
$user_id = 2;
$body_class = '';
$errors = '';
$errors_name = '';
$errors_projekt = '';
$attribute = 'hidden';
$select_name_error = '';
$select_project_error = '';
$file_name = '';
$form_date = '';
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
// определяем $title
$title = "Дела в порядке";
// подключаем файл с функциями
require_once('functions.php');


// Операции с данными полученными из формы
if (!empty($_GET['action']) && $_GET['action'] === 'new_project') {
  if (!empty($_POST['name'])) {
    $form_name = $_POST['name'];
  } else {
    $select_name_error = 'form__input--error';
    $errors_name = 'Имя задачи не может быть пустым полем';
  }
  if (!empty($_POST['project'])) {
    $form_project =   $_POST['project'];
  } else {
    $select_project_error = 'form__input--error';
    $errors_projekt = 'Название проекта не может быть пустым полем';
  }
  if (!empty($_POST['date'])) {
    $form_date = $_POST['date'];
  }
  if (!empty($_FILES["preview"]["name"]) && empty($errors)) {
    $target_dir = getcwd() . DIRECTORY_SEPARATOR;
    $file_name =  basename($_FILES["preview"]["name"]);
    $target_file = $target_dir . $file_name;
    if (file_exists($target_file)) {
      echo "Sorry, file already exists.";
      exit();
    }
    if ($_FILES["preview"]["size"] > 500000) {
      echo "Sorry, your file is too large.";
      exit();
    }
    if (!move_uploaded_file($_FILES["preview"]["tmp_name"], $target_file)) {
      echo "Sorry, there was an error uploading your file.";

      exit();
    }
  }

  if(!empty($errors_name) or !empty($errors_projekt)) {
    $errors = '<h3>Пожалуйста, исправьте ошибки в форме</h3>';
    $body_class = 'overlay';
    $attribute = '';
  } else {
    insert_new_task($link, $user_id, $form_name, $form_project, $form_date, $file_name);
    header('Location: /index.php');
  }
}

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
$all_projects = get_all_projects($link);

$projects_form = renderTemplate('templates/projects_form.php', array(
  'projects_form' => $all_projects,
  'errors' => $errors,
  'errors_name' => $errors_name,
  'errors_projekt' => $errors_projekt,
  'select_name_error' => $select_name_error,
  'select_project_error' => $select_project_error,
  'attribute' => $attribute));
$layout_content = renderTemplate('templates/layout.php', array(
  'body_class' => $body_class,
  'category' => $category,
  'title' => $title,
  'cat_projects' => $cat_projects,
  'cat_objective' => $cat_objective,
  'main' => $main,
  'projects_form' => $projects_form,
));
print($layout_content);

mysqli_close($link);
