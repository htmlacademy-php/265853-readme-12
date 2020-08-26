<?php
require_once('helpers.php');

$is_auth = rand(0, 1);

$user_name = 'Егор Толбаев'; // укажите здесь ваше имя

date_default_timezone_set('Europe/Moscow');

//region Подключение к базе данных
/**Адрес сервера*/
$host = 'localhost';
/**Имя пользователя*/
$user = 'root';
/**Пароль*/
$password = 'root';
/**Имя базы данных*/
$database = 'readme';

/**
 * Устанавливает соединение с базой данных(БД) и возвращает объект соединения
 *
 * @param $host string Хост
 * @param $user string Имя пользователя БД
 * @param $password string Пароль пользователя БД
 * @param $database string Имя БД
 *
 * @return mysqli $connect  Объект-соединение с БД
 */
function dbConnect(string $host, string $user, string $password, string $database): mysqli
{
    $connect = mysqli_connect($host, $user, $password, $database);

    if (!$connect) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }
    mysqli_set_charset($connect, "utf8");
    return ($connect);
}

/**Главное подключение к БД*/
$mainConnection = dbConnect($host, $user, $password, $database);
//endregion

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
function cropText(string $text, int $number_char = 300) : string
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

$types = StoredProcedureHandler($mainConnection, $sqlTypeContent);

$posts = StoredProcedureHandler($mainConnection, $sqlPostUserType);

$page_content = include_template('main.php', [
    'posts' => $posts,
    'types' => $types]);

$layout_content = include_template('layout.php', [
    'page_content' => $page_content,
    'user_name' => $user_name,
    'is_auth' => $is_auth,
    'title' => 'readme: популярное'
]);

//TODO:Не уверен что это правельный вариант, но пока что не нашел другого решения
if(!isset($_GET['post_id']))
{
    print($layout_content);
}
