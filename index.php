<?php

// подключаем файл с функциями
require_once('functions.php');
// соединение с mysqli
require('connection.php');

session_start();

if(!empty($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $user_name = $_SESSION['user_name'];
  $user = array('id'=>$user_id, 'name'=>$user_name);
}
// Определяем переменные
$title = "Дела в порядке";
$body_class = '';
$cat_objective = '';
$form_file = $form_date = $form_name = $form_project = array(
			'main'=>'',
			'error'=>'',
			'error_message' => '');
$attribute = 'hidden';
$catattribute = 'hidden';
$left_section = '';
$footer = '';
$show_complete_tasks = !empty($_GET['show_completed']);
$category = !isset($_GET['page'])?'/':$_GET['page'];

/********************************************************
* Используем сценарии Авторизации или Логику приложения
*********************************************************/
if (empty($_SESSION['user_id'])) {
	// Авторизация
	require('autorization.php');
} else {
	// Логика приложения
	require('actions.php');
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
