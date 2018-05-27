<header class="main-header">
  <a href="#">
    <img src="img/logo.png" width="153" height="42" alt="Логотип Дела в порядке">
  </a>

  <div class="main-header__side">
    <?php if(!empty($user)):?>
      <a class="main-header__side-item button button--plus open-modal" href="javascript:;" target="task_add">Добавить задачу</a>
      <div class="main-header__side-item user-menu">
        <div class="user-menu__image">
          <img src="img/user-pic.jpg" width="40" height="40" alt="Пользователь">
        </div>
        <div class="user-menu__data">
          <p><?=$user['name']?></p>

          <a href="/logout.php">Выйти</a>
        </div>
      <?php else:?>
        <div class="main-header__side">
          <a class="main-header__side-item button button--transparent open-modal"  href="javascript:;"
          target="user_login">Войти</a>
        </div>
      <?php endif?>
    </div>
  </div>
</header>
