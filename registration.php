<?php

require('connection.php');

$email = $password = $name = array(
  'main'=>'',
  'error'=>'',
  'error_message' => '');

// определяем $title
$title = "Дела в порядке";
// подключаем файл с функциями
require_once('functions.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty($_POST['email'])) {
    $email['error'] = 'form__input--error';
    $email['error_message'] = 'Адрес e-mail не может быть пустым полем';
  } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$email['main'] = $_POST['email'];
		$email['error'] = 'form__input--error';
    $email['error_message'] = 'Введите корректный адрес e-mail';
	}
	$email['main'] = $_POST['email'];
	if (check_if_user_exists($link, $email['main'])===1) {
		$email['error'] = 'form__input--error';
		$email['error_message'] = 'Такой e-mail адрес уже есть в системе';
	}

  if (empty($_POST['password'])) {
    $password['error'] = 'form__input--error';
    $password['error_message'] = 'Пароль не может быть пустым';
  } else {
    $password['main'] = $_POST['password'];
  }
  if (empty($_POST['name'])) {
    $name['error'] = 'form__input--error';
    $name['error_message'] = 'Имя не может быть пустым';
  } else {
    $name['main'] = $_POST['name'];
  }
  if (empty($name['error'] . $password['error'] . $email['error'])) {
    create_new_user($link, $email['main'], $password['main'], $name['main']);
    header('Location: /');
    exit();
  }
}
// Операции с данными полученными из формы


$layout_content = renderTemplate('templates/register.php', array(
  'title' => $title,
  'email' => $email,
  'password' => $password,
  'name' => $name,
  ));


print($layout_content);

mysqli_close($link);
