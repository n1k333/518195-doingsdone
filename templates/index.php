<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/" class="tasks-switch__item
<?=empty($_GET['page'])?'tasks-switch__item--active':''?>
        ">Все задачи</a>
        <a href="/index.php?page=today" class="tasks-switch__item
<?=(!empty($_GET['page']) and $_GET['page'] === 'today')?'tasks-switch__item--active':''?>
        ">Повестка дня</a>
        <a href="/index.php?page=tomorrow" class="tasks-switch__item
<?=(!empty($_GET['page']) and $_GET['page'] === 'tomorrow')?'tasks-switch__item--active':''?>
        ">Завтра</a>
        <a href="/index.php?page=missed" class="tasks-switch__item
<?=(!empty($_GET['page']) and $_GET['page'] === 'missed')?'tasks-switch__item--active':''?>
        ">Просроченные</a>
    </nav>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input class="checkbox__input visually-hidden show_completed" type="checkbox"<?=$show_complete_tasks == 1?'checked':''?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <!--показывать следующий тег <tr/>, если переменная $show_complete_tasks равна единице-->
    <?php foreach ($cat_objective as $key => $val): ?>
      <? if(htmlspecialchars($val['status'])===null or $show_complete_tasks > 0):?>
    <tr class="tasks__item task <?=task_date_limit($val['cdate'])==1?"task--important":'';?> <?=htmlspecialchars($val['status'])!==null?'task--completed':''?>">
      <td class="task__select">
        <label class="checkbox task__checkbox">
          <input class="checkbox__input visually-hidden" type="checkbox" <?=htmlspecialchars($val['status'])!==null?'checked':''?>">
          <a href="/index.php?action=change_state&id=<?=htmlspecialchars($val['id'])?>"><span class="checkbox__text"><?=htmlspecialchars($val['tasks']); ?></span></a>
        </label>
      </td>
      <td class="task__date"><?=!empty(htmlspecialchars($val['cdate']))?htmlspecialchars($val['cdate']):'Нет'; ?></td>
      <td class="task__controls"></td>
      <td class="task__category"><?=htmlspecialchars($val['category']); ?></td>
      <td class="task__controls"></td>
      <td class="task__status"><?=htmlspecialchars($val['status']); ?></td>
      <td class="task__controls"></td>
    </tr>
  <?php endif?>
    <?php endforeach; ?>
</table>
