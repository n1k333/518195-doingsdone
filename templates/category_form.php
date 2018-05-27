<div class="modal" <?=isset($catattribute)?$catattribute:''?> id="project_add">
  <button class="modal__close" type="button" name="button">Закрыть</button>

  <h2 class="modal__heading">Добавление проекта</h2>

  <form class="form"  action="/index.php?action=new_category" method="post">
    <div class="form__row">
      <label class="form__label" for="project_name">Название <sup>*</sup></label>

      <input class="form__input <?=$project['error']?>" type="text" name="name" id="project_name" value="<?=$project['name']?>" placeholder="Введите название проекта">
      <p class="form__message"><?=$project['error_message']?></p>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="" value="Добавить">
    </div>
    <div class="form__row">
      <p class="form__message"><?=empty($project['error'])?'':'<h3>Пожалуйста, исправьте ошибки в форме</h3>';?></p>
    </div>
  </form>
</div>
