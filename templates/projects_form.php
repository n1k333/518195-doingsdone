<div class="modal" <?=isset($attribute)?$attribute:''?> id="task_add">
  <button class="modal__close" type="button" name="button" href="/">Закрыть</button>
  <h2 class="modal__heading">Добавление задачи</h2>

  <form class="form"  action="/index.php?action=new_project" method="post" enctype="multipart/form-data">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>
      <input class="form__input <?=$form_name['error']?>" type="text" name="name" id="name" value="<?=htmlspecialchars($form_name['main'])?>" placeholder="Введите название">
      <p class="form__message"><?=$form_name['error_message']?></p>
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>
      <select class="form__input form__input--select <?=$form_project['error']?>" name="project" id="project">
        <?php if(!empty($projects_form)):?>
            <option value=""></option>
          <?php foreach($projects_form as $key => $val):?>
            <option value="<?=$key?>" <?=$key==$form_project['main']?'selected':''?>><?=htmlspecialchars($val)?></option>
          <?php endforeach;?>
        <?php endif;?>
      </select>
      <p class="form__message"><?=$form_project['error_message']?></p>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Срок выполнения</label>
      <input class="form__input form__input--date <?=$form_date['error']?>" type="text" name="date" id="date" value="" placeholder="Введите дату и время YYYY-MM-DD hh:mm">
        <p class="form__message"><?=$form_date['error_message']?></p>
    </div>

    <div class="form__row">
      <label class="form__label" for="preview">Файл</label>
      <div class="form__input-file <?=$form_file['error']?>">
        <input class="visually-hidden <?=$form_file['error']?>" type="file" name="preview" id="preview" value="">
        <label class="button button--transparent" for="preview">
          <span>Выберите файл</span>
        </label>
      </div>
			<p class="form__message"><?=$form_file['error_message']?></p>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="" value="Добавить">
    </div>
    <div class="form__row">
      <p class="form__message"><?=!empty($form_name['error'].$form_project['error'].$form_date['error'].$form_file['error'])?'<h3>Пожалуйста, исправьте ошибки в форме</h3>':''?></p>
    </div>
  </form>
</div>
