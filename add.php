<?php
require_once('helpers.php');
require_once('connection.php');
require_once('DataBase\Procedures.php');
require_once('DataBase\SqlFunctions.php');
require_once('DataBase\SqlServerHelper.php');

$is_auth = rand(0, 1);
$user_name = 'Егор Толбаев'; // укажите здесь ваше имя
$page_title = 'Readme: Публикация';

$procedures = new Procedures();
$sqlFunctions = new SqlFunctions();
$sqlServerHelper = new SqlServerHelper();

function getTypeFromRequest(array $get, array $post = []): ?string
{
    if (isset($get['type'])) {
        return (string)$get['type'];
    } elseif (isset($post['type'])) {
        return (string)$post['type'];
    }
    return null;
}

$types = $sqlServerHelper->StoredProcedureHandler($mainConnection, Procedures::sqlTypeContent);
$form_type = getTypeFromRequest($_GET, $_POST);

$errors = [];
$content = include_template("add-forms/" . $form_type . "-form.php", [
    'errors' => $errors
]);
$page_content = include_template('adding-post.php', [
    'content_types' => $types,
    'form_type' => $form_type,
    'content' => $content,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'page_content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => $page_title
]);

print($layout_content);
