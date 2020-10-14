<?php
require_once('helpers.php');
require_once('connection.php');

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

//region Набор функций для работы с датой
/**
 * Получение относительного формата даты
 * @param $date DateTime дата которую нужно преобразовать
 *
 * @return string $timeHasPassed результат преобразования
 */
function GetDateRelativeFormat(DateTime $date): string
{
    $current_time = new DateTime('now');
    $interval = $date->diff($current_time);

    $minutes = $interval->format('%i');
    $hours = $interval->format('%H');
    $days = $interval->format('%d');
    $months = $interval->format('%m');
    $years = $interval->format('%Y');

    if ($years != 0) {
        $years = floor($years);
        $timeHasPassed = $years . ' ' . get_noun_plural_form($months, 'год', 'года', 'лет') . ' назад';
    } elseif ($months != 0) {
        $months = floor($months);
        $timeHasPassed = $months . ' ' . get_noun_plural_form($months, "месяц", "месяца", "месяцев") . " назад";
    } elseif ($days > 7 && $days < 35) {
        $week = floor($days / 7);
        $timeHasPassed = $week . ' ' . get_noun_plural_form($week, "неделя", "недели", "недель") . " назад";
    } elseif ($days != 0) {
        $timeHasPassed = $days . ' ' . get_noun_plural_form($days, "день", "дня", "дней") . " назад";
    } elseif ($hours != 0) {
        $hours = floor($hours);
        $timeHasPassed = $hours . ' ' . get_noun_plural_form($hours, "час", "часа", "часов") . " назад";
    } elseif ($minutes != 0) {
        $timeHasPassed = $minutes . ' ' . get_noun_plural_form($minutes, "минута", "минуты", "минут") . " назад";
    } else {
        $timeHasPassed = 'меньше минуты назад';
    }

    return $timeHasPassed;
}

/** Получить рандомную дату для поста
 * @param $index string элемент для которого нужна дата
 *
 * @return DateTime $random_date
 */
function GetPostTime(string $index): DateTime
{
    $random_date = generate_random_date($index);
    try {
        return new DateTime($random_date);
    } catch (Exception $e) {
    }
}

//endregion

//region Набор функций для работы с текстом
/**
 * Обрезает текст если количество символов больше чем заданное значение
 *
 * @param $text string Текст который нужно обработать
 * @param $number_char int Количество символов по умолчанию 300
 *
 * @return string вернет обработанные текст
 */
function cropText(string $text, int $number_char = 300): string
{
    //разобьем текст на отдельные слова
    $split_text = explode(" ", $text);

    $word_length = 0;

    $reduction = false;
    $short_text[] = "";
    //считаем длину каждого слова
    foreach ($split_text as $word) {
        $word_length += mb_strlen($word, 'utf8') + 1;//использую mb_strlen т.к strlen выдает в 2 раза больше символов.
        if ($word_length >= $number_char) {
            $reduction = true;
            break;
        }
        $short_text[] = $word;
    };
    //обратно в текст
    $text = implode(" ", $short_text);

    if ($reduction != false) {
        return "<p>" . $text . "..." . "</p>" . '<a class="post-text__more-link" "href="#">Читать далее</a>';
    } else {
        return "<p>" . $text . "</p>";
    }
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
