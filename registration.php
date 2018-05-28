<?php
// соединение с mysqli
$link = mysqli_connect("localhost", "root", "", "things_are_fine");
if (!$link) {
    printf("Текст ошибки: %s\n", mysqli_connect_error());
    exit();
}
$error = 0;
$email = array(
  'email'=>'',
  'error'=>'',
  'error_message' => '');
$password = array(
  'password' => '',
  'error'=>'',
  'error_message' => '');
$name = array(
  'name'=>'',
  'error'=>'',
  'error_message' => '');

// определяем $title
$title = "Дела в порядке";
// подключаем файл с функциями
require_once('functions.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email['error'] = 'form__input--error';
    $email['error_message'] = 'Введите корректный адрес e-mail';
    $error = 1;
  } else {
    $email['email'] = $_POST['email'];
    if (check_if_user_exists($link, $email['email'])===1) {
      $email['error'] = 'form__input--error';
      $email['error_message'] = 'Такой e-mail адрес уже есть в системе';
      $error = 1;
    }
  }
  if (empty($_POST['password'])) {
    $password['error'] = 'form__input--error';
    $password['error_message'] = 'Пароль не может быть пустым';
    $error = 1;
  } else {
    $password['password'] = $_POST['password'];
  }
  if (empty($_POST['name'])) {
    $name['error'] = 'form__input--error';
    $name['error_message'] = 'Имя не может быть пустым';
    $error = 1;
  } else {
    $name['name'] = $_POST['name'];
  }
  if ($error === 0) {
    create_new_user($link, $email['email'], $password['password'], $name['name']);
    header('Location: /');
    exit();
  }
}
// Операции с данными полученными из формы


$layout_content = renderTemplate('templates/register.php', array(
  'error' => $error,
  'title' => $title,
  'email' => $email,
  'password' => $password,
  'name' => $name,
  ));


print($layout_content);

mysqli_close($link);
