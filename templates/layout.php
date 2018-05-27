<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?=$title?></title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/flatpickr.min.css">
</head>

<body class="<?=$body_class?>"><!--class="overlay"-->
<h1 class="visually-hidden">Дела в порядке</h1>

<div class="page-wrapper">
  <div class="container <?=!empty($left_section)?'container--with-sidebar':''?>">
    <?=$header?>

        <div class="content">
          <?=$left_section?>

            <main class="content__main"><?=$main?></main>
        </div>
    </div>
</div>

<?=$footer?>
