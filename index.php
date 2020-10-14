<?php
require_once('helpers.php');
require_once('connection.php');
require_once('functions\TimeHelper.php');
require_once('functions\StringHelper.php');

$is_auth = rand(0, 1);

$user_name = 'Егор Толбаев'; // укажите здесь ваше имя

date_default_timezone_set('Europe/Moscow');

//region Список хранимых процедур из БД MySQL
/**Получить типы контента*/
$sqlTypeContent = "CALL GetTypeContent";
/**Получить список постов*/
$sqlPostUserType = "CALL GetPostUserType";
//endregion

//region Набор функций для работы с SQl запросами
/**
 * Обработка хранимых процедур
 *
 * @param mysqli string Строка соединения
 * @param $storedProcedure string Хранимая процедура
 *
 * @return  array $result
 */
function StoredProcedureHandler(mysqli $connect, string $storedProcedure): array
{
    $final_result = [];
    mysqli_multi_query($connect, $storedProcedure) or die (mysqli_error($connect));
    $result = mysqli_store_result($connect);
    if ($result) {
        $final_result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    mysqli_next_result($connect);
    return $final_result;
}

/**
 * Обработка запросов
 *
 * @param mysqli string Строка соединения
 * @param $stringSQL string Запрос
 *
 * @return  array $result
 */
function requestHandler(mysqli $connect, string $stringSQL): array
{
    $result = mysqli_query($connect, $stringSQL);
    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($connect));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

//endregion

//region Набор функций получения постов
/**
 * Получаем все посты с указанным типов  и сотрируем
 *
 * @param mysqli string Строка соединения
 * @param $type string Тип контента
 * @param $sort_value string Поле по которому сортируем
 * @param $sorting string Тип сортировки
 *
 * @return array вернет массив с постами
 */
function popularPostsCategorySorting(mysqli $connect, string $type, string $sort_value = 'number_views', string $sorting = 'DESC')
{
    $sql = "SELECT  P.`id`,P.`date_add`,P.`title`,
		            IFNULL(P.`content_text`,IFNULL(P.`img_url`,IFNULL(P.`video_url`,P.`link`))) AS content_text , P.`number_views`,
                    P.`user_id`,
	                P.`type_id`,
		            CT.`icon_type`, U.`avatar`, U.`login`, IFNULL(L.`likes`, 0) AS likes, IFNULL(COM.`comments`, 0) AS comments_value,
		            P.`video_url`
        FROM `posts` P
        INNER JOIN `users` U ON P.`user_id` = U.`id`
        INNER JOIN `content_type` CT ON P.`type_id` = CT.`id`
        LEFT JOIN (SELECT L.`post_id`, COUNT(*) AS likes FROM `likes` L GROUP BY L.`post_id`) AS L ON L.`post_id` = P.`id`
        LEFT JOIN (SELECT COM.`post_id`, COUNT(*) AS comments FROM `comments` COM GROUP BY COM.`post_id`) AS COM ON COM.`post_id` = P.`id`
        WHERE CT.`icon_type` = '$type'
        ORDER BY $sort_value $sorting";
    return requestHandler($connect, $sql);
}

/**
 * Получаем популярные посты
 *
 * @param mysqli string Строка соединения
 * @param $sort_value string Поле по которому сортируем
 * @param $sorting string Тип сортировки
 *
 * @return array вернет массив с постами
 */
function popularPosts(mysqli $connect, string $sort_value = 'number_views', string $sorting = 'ASC')
{
    $sql = "
    SELECT P.*, CT.`icon_type`, U.`avatar`, U.`login`, IFNULL(L.`likes`, 0) AS likes, IFNULL(COM.`comments`, 0) AS comments,
    IFNULL(P.`content_text`,IFNULL(P.`img_url`,IFNULL(P.`video_url`,P.`link`))) AS content_text,P.`video_url`
    FROM `posts` P
    INNER JOIN `users` U ON P.`user_id` = U.`id`
    INNER JOIN `content_type` CT ON P.`type_id` = CT.`id`
    LEFT JOIN (SELECT L.`post_id`, COUNT(*) AS likes FROM `likes` L GROUP BY L.`post_id`) AS L ON L.`post_id` = P.`id`
    LEFT JOIN (SELECT COM.`post_id`, COUNT(*) AS comments FROM `comments` COM GROUP BY COM.`post_id`) AS COM ON COM.`post_id` = P.`id`
    ORDER BY $sort_value $sorting
    ";
    return requestHandler($connect, $sql);
}

//endregion

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

$types = StoredProcedureHandler($mainConnection, $sqlTypeContent);

$posts = StoredProcedureHandler($mainConnection, $sqlPostUserType);


if (isset($_GET['type'])) {
    if ($_GET['type'] === 'all') {
        $posts = popularPosts($mainConnection, $sort_value, $sorting);
    } else {
        $posts = popularPostsCategorySorting($mainConnection, $sorting_parameters['type'], $sort_value, $sorting);
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
