<?php
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

?>
