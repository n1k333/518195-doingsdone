<div class="modal" <?=isset($attribute)?$attribute:''?> id="user_login">
  <button class="modal__close" type="button" name="button">Закрыть</button>

  <h2 class="modal__heading">Вход на сайт</h2>

  <form class="form" action="/index.php?action=login" method="post">
    <div class="form__row">
      <label class="form__label" for="email">E-mail <sup>*</sup></label>
      <input class="form__input <?=$email['error']?>" type="text" name="email" id="email" value="<?=htmlspecialchars($email['main'])?>" placeholder="Введите e-mail">
      <p class="form__message"><?=$email['error_message']?></p>
    </div>

    <div class="form__row">
      <label class="form__label" for="password">Пароль <sup>*</sup></label>
      <input class="form__input <?=$password['error']?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">
      <p class="form__message"><?=$password['error_message']?></p>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="" value="Войти">
    </div>
    <div class="form__row">
      <p class="form__message"><?=(empty($password['error']) and empty($email['error']))?'':'<h3>Пожалуйста, исправьте ошибки в форме</h3>';?></p>
    </div>
  </form>
</div>
