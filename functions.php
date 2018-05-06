<?php
date_default_timezone_set('Europe/Moscow');

function get_count_of_tasks($array = array(), $task_name = 'Все') {
  if ($task_name == 'Все') {
    return count($array);
  }
  $counter = 0;
  foreach($array as $a) {
    if ($task_name == $a['category']) $counter++;
  }
  return $counter;
}

function renderTemplate($path, $params) {
  if (!file_exists($path)) {
    return ('');
  }
  foreach($params as $key => $value) {
    $$key = $value;
  }
  ob_start();
  require($path);
  return ob_get_clean();
}

function task_date_limit($task_date = '06.05.2018') {
  if($task_date == 'Нет') {
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
