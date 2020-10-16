<?php
require_once('helpers.php');
require_once('connection.php');
require_once('DataBase\Procedures.php');
require_once('DataBase\SqlServerHelper.php');
require_once('DataBase\SqlFunctions.php');
require_once('functions\TimeHelper.php');
require_once('functions\StringHelper.php');

$is_auth = rand(0, 1);

$user_name = 'Егор Толбаев'; // укажите здесь ваше имя

date_default_timezone_set('Europe/Moscow');

$sqlServerHelper = new SqlServerHelper();
$sqlFunctions = new SqlFunctions();

/**
 * Получаем ссылку на посты/посты
 *
 * @param $type string Тип контента
 * @param $sort_value string Поле по которому сортируем
 * @param $sorting string Тип сортировки
 * @param $page_url string Старница отображения
 *
 * @return string готовый url
 */
function setUrl(string $type, string $sort_value, string $sorting, string $page_url = "index.php")
{
    $params = $_GET;

    $params['type'] = $type;
    $params['sort_value'] = $sort_value;
    $params['sorting'] = $sorting;
    $query = http_build_query($params);
    return "/" . $page_url . "?" . $query;
}

$sorting_parameters = [];
$sorting_parameters['sort_value'] = $_GET['sort_value'] ?? 'number_views';
$sorting_parameters['sorting'] = $_GET['sorting'] ?? 'DESC';
$sorting_parameters['type'] = $_GET['type'] ?? 'all';

$sort_value = $sorting_parameters['sort_value'];
$sorting = $sorting_parameters['sorting'];

$types = $sqlServerHelper->StoredProcedureHandler($mainConnection, Procedures::sqlTypeContent);

$posts = $sqlServerHelper->StoredProcedureHandler($mainConnection, Procedures::sqlPostUserType);

if (isset($_GET['type'])) {
    if ($_GET['type'] === 'all') {
        $posts = $sqlFunctions->popularPosts($mainConnection, $sort_value, $sorting);
    } else {
        $posts = $sqlFunctions->popularPostsCategorySorting($mainConnection, $sorting_parameters['type'], $sort_value, $sorting);
    }
}

$page_content = include_template('main.php', [
    'posts' => $posts,
    'types' => $types,
    'sorting_parameters' => $sorting_parameters]);

$layout_content = include_template('layout.php', [
    'page_content' => $page_content,
    'user_name' => $user_name,
    'is_auth' => $is_auth,
    'title' => 'readme: популярное'
]);

print($layout_content);
