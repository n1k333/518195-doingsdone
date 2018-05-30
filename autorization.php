<?php

/*********************************************************
* Этот кодовый блок предназначен для неавторизированных пользователей
**********************************************************/

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
		if (!filter_var($email['main'], FILTER_VALIDATE_EMAIL)) {
			$email['error'] = 'form__input--error';
      $email['error_message'] = 'Не правильный адресс эл. почты';
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
      $loginstatus = login($link, $email['main'], $password['main']);
			switch($loginstatus) {
				case 1:
					$body_class = 'overlay';
					$attribute = '';
					$email['error'] = 'form__input--error';
					$email['error_message'] = 'Такой эл. почты не зарегистрированно';
					break;
				case 2:
					$body_class = 'overlay';
					$attribute = '';
					$password['error'] = 'form__input--error';
					$password['error_message'] = 'Вы ввели неверный пароль';
					break;
				default:
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