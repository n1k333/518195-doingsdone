<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">

        <ul class="main-navigation__list">

<?php foreach ($cat_projects as $key => $val): ?>
<li class="main-navigation__list-item <?=$val[0]==$category?'main-navigation__list-item--active':''?>">
<?php if($val[0]==='/'):?>
<a class="main-navigation__list-item-link" href="/"><?=htmlspecialchars($key);?></a>
<?php else:?>
<a class="main-navigation__list-item-link" href="?page=<?=$val[0];?>"><?=htmlspecialchars($key);?></a>
<?php endif;?>
<span class="main-navigation__list-item-count"><?=htmlspecialchars($val[1])?></span>
</li>
<?php endforeach; ?>

        </ul>
      </nav>

    <a class="button button--transparent button--plus content__side-button open-modal"
       href="javascript:;" target="project_add">Добавить проект</a>
</section>
