<?php
$link = mysqli_connect("localhost", "root", "", "things_are_fine");
mysqli_query($link, "SET NAMES 'utf8'");
mysqli_query($link, "SET CHARACTER SET 'utf8'");
mysqli_query($link, "SET SESSION collation_connection = 'utf8_general_ci'");
if (!$link) {
    printf("Текст ошибки: %s\n", mysqli_connect_error());
    exit();
}
