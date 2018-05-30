<?php
/*************************
** Создать новую задачу
**************************/

  if (!empty($_GET['action']) && $_GET['action'] === 'new_project' and $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка названия задачи на пустое поле
		if (!empty($_POST['name'])) {
      $form_name['main'] = $_POST['name'];
    } else {
      $form_name['error'] = 'form__input--error';
      $form_name['error_message'] = 'Имя задачи не может быть пустым полем';
    }

		// Валидация ИД проекта на:
		//	- пустое поле
		//	- есть ли у пользователя такая задача
    if (!empty($_POST['project'])) {
      $form_project['main'] = $_POST['project'];
      if(!check_if_project_exists($link, $form_project['main'], $user_id)) {
        $form_project['error'] = 'form__input--error';
        $form_project['error_message'] = 'Такого проекта не существует!';
      }
    } else {
      $form_project['error'] = 'form__input--error';
      $form_project['error_message'] = 'Название проекта не может быть пустым полем';
    }
		// Валидация даты
    if (!empty($_POST['date'])) {
      $test_date = str_replace('.', '-', $_POST['date']);
      $test_date = trim($test_date) . ' 23:59';
      $test_date = substr($test_date, 0, 16);
      $date = DateTime::createFromFormat('Y-m-d G:i', $test_date);
      $date_errors = DateTime::getLastErrors();
      if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {
        $form_date['error'] = 'form__input--error';
        $form_date['error_message'] = 'Неверный формат даты';
      } else {
        $form_date['main'] = date("Y-m-d G:i", strtotime($test_date));
      }
    }
		// Валидация файла
    if (!empty($_FILES["preview"]["name"])) {
      $target_dir = getcwd() . DIRECTORY_SEPARATOR;
      $temp_name =  basename($_FILES["preview"]["name"]);
			$imageFileType = strtolower(pathinfo($temp_name,PATHINFO_EXTENSION));
			$form_file['main'] = uniqid('image').'.'. $imageFileType;
			if($imageFileType!=='jpg' and $imageFileType!=='png' and $imageFileType!=='jpeg' and $imageFileType!=='gif') {
				$form_file['error'] = 'form__input--error';
				$form_file['error_message'] = 'Только *.jpg, *.png, *.jpeg, *.gif Форматы разрешены на сайте';
			} elseif ($_FILES["preview"]["size"] > (6*1024*1024)) {
        $form_file['error'] = 'form__input--error';
				$form_file['error_message'] = "Извините размер файла слишком большой.";
			} else {
				$target_file = $target_dir . "uploads/" . $form_file['main'];
				move_uploaded_file($_FILES["preview"]["tmp_name"], $target_file);
			}
    }

    if(!empty($form_name['error'].$form_project['error'].$form_date['error'].$form_file['error'])) {
      $body_class = 'overlay';
      $attribute = '';
    } else {
			// Новая задача заносится в базу
      insert_new_task($link, $user_id, $form_name['main'], $form_project['main'], $form_date['main'], $form_file['main']);
      header('Location: /index.php');
      exit();
    }
  }

/*************************
** Создать новый проект
**************************/

  $project =  array('name'=>'', 'error'=>'', 'error_message'=> '');
  $category_form = renderTemplate('templates/category_form.php', array(
    'catattribute' => $catattribute,
    'project' => $project,
  ));
  if (!empty($_GET['action']) && $_GET['action'] === 'new_category' and $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['name'])) {
      $project['main'] = $_POST['name'];
      create_new_category($link, $project['main'], $user_id);
      header('Location: /');
      exit();
    } else {
      $project['error'] = 'form__input--error';
      $project['error_message'] = 'Название проекта не может быть пустым полем';
      $body_class = 'overlay';
      $catattribute = '';
    }
		// Модальная форма ввода проектов
    $category_form = renderTemplate('templates/category_form.php', array(
      'catattribute' => $catattribute,
      'project' => $project,
    ));
  }

/*********************************************************************************
** Изменить состояние (выполнено или не выполнено) для задач по идентификатору
**********************************************************************************/

  if (!empty($_GET['action']) and $_GET['action'] === 'change_state' and !empty($_GET['id'])) {
    $id = $_GET['id'];
    change_state($link, $user_id, $id);
    header('Location: /');
    exit();
  }

/*********************************************************************************
** Выводим на экран информацию
**********************************************************************************/
	$cat_projects = getCategoriesByUser($user_id, $link);
	$all_projects = get_all_projects($link, $user_id);

	// Показываем заголовок
	$header = renderTemplate('templates/header.php', array(
		'user' => $user,
	));

	// Показываем проекты пользователя в левом меню
	$left_section = renderTemplate('templates/left_section.php', array(
		'cat_projects' => $cat_projects,
		'category' => $category,
	));

	// Показываем задачи пользователя
	$cat_objective = getCatObjective($user_id, $category, $link);
	$main = renderTemplate('templates/index.php', array(
		'cat_objective' => $cat_objective,
		'show_complete_tasks' => $show_complete_tasks,
	));

	// Собираем модальную форму ввода информации о задачах
	$projects_form = renderTemplate('templates/projects_form.php', array(
		'form_project' => $form_project,
		'form_name' => $form_name,
		'form_date' => $form_date,
		'form_file' => $form_file,
		'projects_form' => $all_projects,
		'attribute' => $attribute));

	// Показываем подвал с модальной формой ввода задач и проектов
	$footer = renderTemplate('templates/footer.php', array(
		'projects_form' => $projects_form . $category_form,
	));
