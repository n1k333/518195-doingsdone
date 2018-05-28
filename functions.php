<?php
date_default_timezone_set('Europe/Moscow');

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

function task_date_limit($task_date) {
  if(empty($task_date)) {
    return 0;
  }
  $ts_midnight = strtotime($task_date);
  $secs_to_midnight = $ts_midnight - time();
  $hours = floor($secs_to_midnight / 3600);
  if ($hours <= 24) {
    return 1;
  } else {
    return 0;
  }
}

function getCategoriesByUser($user_id, $link) {
  $result = array();
  $result['Все']=['/', 0];
  $counter = 0;
  $stmt = mysqli_prepare($link, "select id, projects_name, max(count)
      from ( SELECT projects.id as id, projects_name, count(*) as count from tasks
      join projects on tasks.projects_id = projects.id where users_id = ? and date_task_execution is null
      group by id UNION SELECT id, projects_name, 0 as count from projects) t1 group by id, projects_name");
  mysqli_stmt_bind_param($stmt, 'i', $user_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $id, $project_name, $count);
  while (mysqli_stmt_fetch($stmt)) {
    $result[$project_name] = array($id, $count);
    $counter = $counter + $count;
  }
  $result['Все']=['/', $counter];
  return $result;
}

function get_all_projects($link) {
  $stmt = mysqli_prepare($link, "SELECT id, projects_name from projects");
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt,$id, $project_name);
  while (mysqli_stmt_fetch($stmt)) {
    $result[$id] = $project_name;
  }
  return $result;
}

function get_all_register($link) {
  $stmt = mysqli_prepare($link, "SELECT id, email from users");
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt,$id, $email);
  while (mysqli_stmt_fetch($stmt)) {
    $result[$id] = $email;
  }
  return $result;
}

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

function check_if_user_exists($link, $email) {
  $stmt = mysqli_prepare($link, "SELECT count(*) AS count FROM users WHERE email = ?");
  mysqli_stmt_bind_param($stmt, 's',  $email);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $count);
  mysqli_stmt_fetch($stmt);
  return $count;
}

function check_if_project_exists($link, $form_project) {
  $stmt = mysqli_prepare($link, "SELECT count(*) AS count FROM projects WHERE id = ?");
  mysqli_stmt_bind_param($stmt, 'i',  $form_project);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $count);
  mysqli_stmt_fetch($stmt);
  return $count;
}

function create_new_user($link, $email, $password, $name) {
  $password = password_hash($password, PASSWORD_DEFAULT);
  $stmt = mysqli_prepare($link, "INSERT INTO users (reg_date, email, name, password, contacts)
                                VALUES (CURRENT_TIMESTAMP, ?, ?, ?, ?)");
  mysqli_stmt_bind_param($stmt, 'ssss',  $email, $name, $password, $email);
  mysqli_stmt_execute($stmt);
}

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


function create_new_category($link, $category) {
  $stmt = mysqli_prepare($link, "INSERT IGNORE INTO projects (projects_name) VALUES (?)");
  mysqli_stmt_bind_param($stmt, 's',  $category);
  mysqli_stmt_execute($stmt);
}

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

function validateDate($date, $format = 'Y-m-d H:i')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
