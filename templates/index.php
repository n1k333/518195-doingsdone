<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
        <a href="/" class="tasks-switch__item">Повестка дня</a>
        <a href="/" class="tasks-switch__item">Завтра</a>
        <a href="/" class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?=$show_complete_tasks==1?'checked':''?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <tr class="tasks__item task">
        <td class="task__select">
            <label class="checkbox task__checkbox">
                <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                <span class="checkbox__text">Сделать главную страницу Дела в порядке</span>
            </label>
        </td>
        <td class="task__file">
            <a class="download-link" href="#">Home.psd</a>
        </td>
        <td class="task__date"></td>
    </tr>

    <!--показывать следующий тег <tr/>, если переменная $show_complete_tasks равна единице-->
    <?php if ($show_complete_tasks == 1): ?>
      <?php foreach ($cat_objective as $key => $val): ?>
      <tr class="tasks__item task  <?=task_date_limit($val['cdate'])==1?"task--important":'';?>
        <?=$val['status']!==null?'task--completed':''?>">
        <td class="task__select">
          <label class="checkbox task__checkbox">
            <input class="checkbox__input visually-hidden" type="checkbox" checked>
            <span class="checkbox__text"><?=htmlspecialchars($val['tasks']); ?></span>
          </label>
        </td>
        <td class="task__date"><?=!empty($val['cdate'])?htmlspecialchars($val['cdate']):'Нет'; ?></td>
        <td class="task__controls"></td>
        <td class="task__category"><?=htmlspecialchars($val['category']); ?></td>
        <td class="task__controls"></td>
        <td class="task__status"><?=htmlspecialchars($val['status']); ?></td>
        <td class="task__controls"></td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
</table>
