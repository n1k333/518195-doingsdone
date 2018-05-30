<?php

date_default_timezone_set('Europe/Moscow');

/*********************************************
* Функция принимает путь к темплейту и массив
* элементов $key = > $value, которые вставляет
* в темплейт, где $key превращается в имя переменной
* а $value превращается в значение переменной
**********************************************/
function renderTemplate($path, $params) {
  if (!file_exists($path)) {
    return ('');
  }
  if (!empty($params)) {
    foreach($params as $key => $value) {
      $$key = $value;
    }
  }
  ob_start();
  require($path);
  return ob_get_clean();
}

/*********************************************
* Функция возвращает 1, если дата отстоит меньше
* чем на 24 часа
**********************************************/
function task_date_limit($task_date) {
  if(empty($task_date)) {
    return 0;
  }
  $ts_midnight = strtotime($task_date);
  $secs_to_midnight = $ts_midnight - time();
  $hours = floor($secs_to_midnight / 3600);
  if ($hours <= 24) {
    return 1;
  }
}

/*********************************************
* Функция возвращает все категории пользователя
* вместе с количеством активных заданий в категории
* также общее количество активных заданий
**********************************************/
function getCategoriesByUser($user_id, $link) {
  $result = array();
  $result['Все']=['/', 0];
  $counter = 0;
  $stmt = mysqli_prepare($link, "SELECT id, projects_name, MAX(count)
      FROM (
					SELECT projects.id AS id, projects_name, COUNT(*) AS count FROM tasks
						JOIN projects ON tasks.projects_id = projects.id WHERE users_id = ? AND date_task_execution IS NULL
						GROUP BY id
					UNION
					SELECT projects.id AS id, projects_name, 0 AS count FROM projects
						JOIN users_projects ON projects_id = projects.id WHERE users_id = ?
						GROUP BY id
						) t1
					GROUP BY id, projects_name");
  mysqli_stmt_bind_param($stmt, 'ii', $user_id, $user_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $id, $project_name, $count);
  while (mysqli_stmt_fetch($stmt)) {
    $result[$project_name] = array($id, $count);
    $counter = $counter + $count;
  }
  $result['Все']=['/', $counter];
  return $result;
}

/*********************************************
* Функция возвращает все проекты пользователя
**********************************************/
function get_all_projects($link, $user_id) {
	$result = array();
  $stmt = mysqli_prepare($link, "SELECT p.id, p.projects_name from projects p
																	JOIN users_projects u ON u.projects_id = p.id
																	WHERE u.users_id = ?");
	mysqli_stmt_bind_param($stmt, 'i', $user_id);
	mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt,$id, $project_name);
  while (mysqli_stmt_fetch($stmt)) {
    $result[$id] = $project_name;
  }
  return $result;
}

/*********************************************
* Функция возвращает информацию о задачах
* пользователя
* делает фильтрацию по значению $category (today, tomorrow, missed, /, [integer])
**********************************************/
function getCatObjective($user_id, $category, $link) {
  $result = array();
  $sql = "SELECT tasks.id as id, tasks_name as tasks, deadline_task as cdate, projects_name as category,
          date_task_execution as status, file_reference as file
          FROM tasks JOIN projects on projects_id = projects.id
          WHERE users_id = ? ";
  switch ($category) {
    case 'today':
      $sql = $sql . "AND DATE_FORMAT(deadline_task, '%Y-%m-%d') = CURDATE()";
      $stmt = mysqli_prepare($link, $sql);
      mysqli_stmt_bind_param($stmt, 'i', $user_id);
      break;
    case 'tomorrow':
      $sql = $sql . "AND DATE_FORMAT(deadline_task, '%Y-%m-%d') = CURDATE() + INTERVAL 1 DAY";
      $stmt = mysqli_prepare($link, $sql);
      mysqli_stmt_bind_param($stmt, 'i', $user_id);
      break;
    case 'missed':
      $sql = $sql . 'AND deadline_task < now()';
      $stmt = mysqli_prepare($link,$sql);
      mysqli_stmt_bind_param($stmt, 'i', $user_id);
      break;
    case '/':
      $stmt = mysqli_prepare($link, $sql);
      mysqli_stmt_bind_param($stmt, 'i', $user_id);
      break;
    default:
      $sql = $sql . 'and projects_id = ?';
      $stmt = mysqli_prepare($link, $sql);
      mysqli_stmt_bind_param($stmt, 'ii', $user_id, $category);
      break;
  }
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $id, $tasks, $cdate, $category, $status, $file);
  while (mysqli_stmt_fetch($stmt)) {
    $result[] =   [
        'id' => $id,
        'tasks' => $tasks,
        'cdate' => $cdate,
        'category' => $category,
        'status' => $status,
        'file' => $file,
      ];
  }
  return $result;
}

/*********************************************
* Функция проверяет существует ли пользователь
* с определенной эл. почтой
**********************************************/
function check_if_user_exists($link, $email) {
  $stmt = mysqli_prepare($link, "SELECT count(*) AS count FROM users WHERE email = ?");
  mysqli_stmt_bind_param($stmt, 's',  $email);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $count);
  mysqli_stmt_fetch($stmt);
  return $count;
}

/*********************************************
* фунция проверяет существует ли проект с определенным
* номером у определенного пользователя
**********************************************/
function check_if_project_exists($link, $form_project, $user_id) {
  $stmt = mysqli_prepare($link, "SELECT p.id as id FROM projects p
																	JOIN users_projects u ON u.projects_id = p.id
																	WHERE p.id = ? AND u.users_id = ?");
  mysqli_stmt_bind_param($stmt, 'ii',  $form_project, $user_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $id);
  mysqli_stmt_fetch($stmt);
  return $id;
}

/*********************************************
* фунция создает нового пользователя
**********************************************/
function create_new_user($link, $email, $password, $name) {
  $password = password_hash($password, PASSWORD_DEFAULT);
  $stmt = mysqli_prepare($link, "INSERT INTO users (reg_date, email, name, password, contacts)
                                VALUES (CURRENT_TIMESTAMP, ?, ?, ?, ?)");
  mysqli_stmt_bind_param($stmt, 'ssss',  $email, $name, $password, $email);
  mysqli_stmt_execute($stmt);
}

/*********************************************
* фунция меняет статус задания с выполненного
* на невыполненное и наоборот
**********************************************/
function change_state($link, $user_id, $id) {
  $stmt = mysqli_prepare($link, "SELECT id, date_task_execution as cdate from tasks where users_id = ? and id = ?");
  mysqli_stmt_bind_param($stmt, 'ii',  $user_id, $id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $tid, $cdate);
  mysqli_stmt_fetch($stmt);
  unset($stmt);
  if (empty($tid)) { die('Hacker attempt!'); }
  if (empty($cdate)) {
    $stmt = mysqli_prepare($link, "UPDATE tasks SET date_task_execution = CURRENT_TIMESTAMP() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i',  $tid);
    mysqli_stmt_execute($stmt);
  } else {
    $stmt = mysqli_prepare($link, "UPDATE tasks SET date_task_execution = null WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i',  $tid);
    mysqli_stmt_execute($stmt);
  }
}

/*********************************************
* функция создает новою категорию
* если такая уже есть - ничего не меняется и
* ошибки не возникает
* получает ИД проекта и вносит в связанную базу
* ИД пользователя и проекта
**********************************************/
function create_new_category($link, $category, $user_id) {
  $stmt = mysqli_prepare($link, "INSERT IGNORE INTO projects (projects_name) VALUES (?)");
  mysqli_stmt_bind_param($stmt, 's',  $category);
  mysqli_stmt_execute($stmt);
	unset($stmt);
	$stmt = mysqli_prepare($link, "SELECT id FROM projects WHERE projects_name = ?");
  mysqli_stmt_bind_param($stmt, 's',  $category);
  mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id);
	mysqli_stmt_fetch($stmt);
	unset($stmt);
	$stmt = mysqli_prepare($link, "INSERT IGNORE INTO users_projects (projects_id, users_id) VALUES (?, ?)");
  mysqli_stmt_bind_param($stmt, 'ii',  $id, $user_id);
  mysqli_stmt_execute($stmt);
}

/*********************************************
* функция вставляет новую задачу для пользователя
**********************************************/
function insert_new_task($link, $user_id, $form_name, $form_project, $form_date = '', $target_file = '') {
    if (!empty($form_date)) {
      $stmt = mysqli_prepare($link, "INSERT INTO tasks (
        users_id, date_task_creation, tasks_name, file_reference, deadline_task, projects_id)
        VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?, ?)
        ");

      mysqli_stmt_bind_param($stmt, 'isssi',  $user_id,  $form_name, $target_file, $form_date, $form_project);
    } else {
      $stmt = mysqli_prepare($link, "INSERT INTO tasks (
        users_id, date_task_creation, tasks_name, file_reference, deadline_task, projects_id)
        VALUES (?, CURRENT_TIMESTAMP, ?, ?, NULL, ?)
        ");

      mysqli_stmt_bind_param($stmt, 'issi',  $user_id,  $form_name, $target_file, $form_project);
    }
    mysqli_stmt_execute($stmt);
}

/*********************************************
* функция авторизации
**********************************************/
function login($link, $email, $password) {
  $error = '';
  $stmt = mysqli_prepare($link, "SELECT id, name, password FROM users WHERE email = ?");
  mysqli_stmt_bind_param($stmt, 's',  $email);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $id, $name, $hash);
  mysqli_stmt_fetch($stmt);
  if (empty($hash)) {
    return 1;
  } else {
    if (!password_verify($password, $hash)) {
      return 2;
    }
  }

  $_SESSION['user_id'] = $id;
  $_SESSION['user_name'] = $name;
  $_SESSION['user_email'] = $email;
  return 0;
}
