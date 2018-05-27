<?php
// соединение с mysqli
$link = mysqli_connect("localhost", "root", "", "things_are_fine");
if (!$link) {
    printf("Текст ошибки: %s\n", mysqli_connect_error());
    exit();
}

session_start();
if(!empty($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $user_name = $_SESSION['user_name'];
  $user = array('id'=>$user_id, 'name'=>$user_name);
}
// определяем user_id;
$body_class = '';
$category = '';
$cat_objective = '';
$errors = '';
$errors_name = '';
$errors_projekt = '';
$attribute = 'hidden';
$left_section = '';
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

if (empty($_SESSION['user_id'])) {
  $body_class = 'body-background';
  $header = renderTemplate('templates/header.php', array(
    'user' => '',
  ));
  $email = array('main'=>'', 'error'=>'', 'error_message'=> '');
  $password = array('main'=>'', 'error'=>'', 'error_message'=> '');
  if (!empty($_GET['action']) and $_GET['action'] === 'login' and $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['email'])) {
      $email['main'] = $_POST['email'];
    } else {
      $email['error'] = 'form__input--error';
      $email['error_message'] = 'эл. почта не может быть пустым полем';
    }
    if (!empty($_POST['password'])) {
      $password['main'] = $_POST['password'];
    } else {
      $password['error'] = 'form__input--error';
      $password['error_message'] = 'пароль не может быть пустым полем';
    }
    if(!empty($email['error']) or !empty($password['error'])) {
      $body_class = 'overlay';
      $attribute = '';
    } else {
      $status = login($link, $email['main'], $password['main']);
      if($status===1) {
        $body_class = 'overlay';
        $attribute = '';
        $email['error'] = 'form__input--error';
        $email['error_message'] = 'не правильный адресс эл. почты';
      }
      if($status===2) {
        $body_class = 'overlay';
        $attribute = '';
        $password['error'] = 'form__input--error';
        $password['error_message'] = 'Вы ввели неверный пароль';
      }
      if($status===0) {
        header('Location: /');
        exit();
      }
    }
  }
  $autorization_form = renderTemplate('templates/autorization_form.php', array(
    'attribute' => $attribute,
    'email' => $email,
    'password' => $password,
  ));
  $footer = renderTemplate('templates/footer.php', array(
    'projects_form' => $autorization_form,
  ));
  $main = renderTemplate('templates/guest.php', array());



} else {

  // Операции с данными полученными из формы
  if (!empty($_GET['action']) && $_GET['action'] === 'new_project' and $_SERVER['REQUEST_METHOD'] === 'POST') {
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
        echo "Извините файл уже существует.";
        exit();
      }
      if ($_FILES["preview"]["size"] > 500000) {
        echo "Извините размер файла слишком большой.";
        exit();
      }
      if (!move_uploaded_file($_FILES["preview"]["tmp_name"], $target_file)) {
        echo "Извините, произошла ошибка при загрузке файла.";
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
      exit();
    }
  }

  // проверяем $_GET['page'] и присваеваем результат переменной $category
  $category = !isset($_GET['page'])?'/':$_GET['page'];
  // получаем список категорий с task=tasks.tasks_name,
  // cdate=tasks.deadline_task, category=projects.projects_name,
  // status=tasks.date_task_execution

  $cat_objective = getCatObjective($user_id, $category, $link);
  /*if (empty($cat_objective)) {
    header('Location: 404.php');
    exit();
  }*/

  $main = renderTemplate('templates/index.php', array(
    'cat_objective' => $cat_objective,
    'show_complete_tasks' => $show_complete_tasks,
  ));
  $cat_projects = getCategoriesByUser($user_id, $link);
  $all_projects = get_all_projects($link);

  $header = renderTemplate('templates/header.php', array(
    'user' => $user,
  ));

  $projects_form = renderTemplate('templates/projects_form.php', array(
    'projects_form' => $all_projects,
    'errors' => $errors,
    'errors_name' => $errors_name,
    'errors_projekt' => $errors_projekt,
    'select_name_error' => $select_name_error,
    'select_project_error' => $select_project_error,
    'attribute' => $attribute));
  $left_section = renderTemplate('templates/left_section.php', array(
    'cat_projects' => $cat_projects,
  ));
  $footer = renderTemplate('templates/footer.php', array(
    'projects_form' => $projects_form,
  ));
}

  $layout_content = renderTemplate('templates/layout.php', array(
    'header' => $header,
    'body_class' => $body_class,
    'category' => $category,
    'title' => $title,
    'left_section' => $left_section,
    'cat_objective' => $cat_objective,
    'main' => $main,
    'footer' => $footer,
  ));
print($layout_content);
mysqli_close($link);
