<div class="modal" <?=isset($attribute)?$attribute:''?> id="task_add">
  <button class="modal__close" type="button" name="button" href="/">Закрыть</button>

  <h2 class="modal__heading">Добавление задачи</h2>

  <form class="form"  action="/index.php?action=new_project" method="post" enctype="multipart/form-data">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>

      <input class="form__input <?=empty($select_name_error)?'':$select_name_error?>" type="text" name="name" id="name" value="" placeholder="Введите название">
      <p class="form__message"><?=empty($errors_name)?'':$errors_name;?></p>
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>

      <select class="form__input form__input--select <?=empty($select_project_error)?'':$select_project_error?>" name="project" id="project">
        <?php if(!empty($projects_form)):?>
            <option value=""></option>
          <?php foreach($projects_form as $key => $val):?>
            <option value="<?=$key?>"><?=htmlspecialchars($val)?></option>
          <?php endforeach;?>
        <?php endif;?>

      </select>
      <p class="form__message"><?=empty($errors_projekt)?'':$errors_projekt;?></p>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Срок выполнения</label>

      <input class="form__input form__input--date <?=empty($errors_date)?'':$errors_date?>" type="text" name="date" id="date" placeholder="Введите дату и время">
        <p class="form__message"><?=empty($errors_date_message)?'':$errors_date_message;?></p>
    </div>

    <div class="form__row">
      <label class="form__label" for="preview">Файл</label>

      <div class="form__input-file">
        <input class="visually-hidden" type="file" name="preview" id="preview" value="">

        <label class="button button--transparent" for="preview">
          <span>Выберите файл</span>
        </label>
      </div>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="" value="Добавить">
    </div>
    <div class="form__row">
      <p class="form__message"><?=empty($errors)?'':$errors;?></p>
    </div>
  </form>
</div>
