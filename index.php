<?php
require_once('helpers.php');
require_once('connection.php');
require_once('DataBase\Procedures.php');
require_once('DataBase\SqlServerHelper.php');
require_once('functions\TimeHelper.php');
require_once('functions\StringHelper.php');

$is_auth = rand(0, 1);

$user_name = 'Егор Толбаев'; // укажите здесь ваше имя

date_default_timezone_set('Europe/Moscow');

$sqlServerHelper = new SqlServerHelper();

$types = $sqlServerHelper->StoredProcedureHandler($mainConnection, Procedures::sqlTypeContent);

$posts = $sqlServerHelper->StoredProcedureHandler($mainConnection, Procedures::sqlPostUserType);

$page_content = include_template('main.php', ['posts' => $posts, 'types' => $types]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'user_name' => $user_name,
    'is_auth' => $is_auth,
    'title' => 'readme: популярное'
]);

print($layout_content);
