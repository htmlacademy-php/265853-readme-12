<?php
require_once('helpers.php');

$is_auth = rand(0, 1);

$user_name = 'Егор Толбаев'; // укажите здесь ваше имя

date_default_timezone_set('Europe/Moscow');

$posts = [
    [
        'title' => 'Цитата',
        'type' => 'post-quote',
        'post_content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'user_name' => 'Лариса',
        'user_avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Игра престолов',
        'type' => 'post-text',
        'post_content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
        'user_name' => 'Владик',
        'user_avatar' => 'userpic.jpg'
    ],
    [
        'title' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'post_content' => 'rock-medium.jpg',
        'user_name' => 'Виктор',
        'user_avatar' => 'userpic-mark.jpg'
    ],
    [
        'title' => 'Моя мечта',
        'type' => 'post-photo',
        'post_content' => 'coast-medium.jpg',
        'user_name' => 'Лариса',
        'user_avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Лучшие курсы',
        'type' => 'post-link',
        'post_content' => 'www.htmlacademy.ru',
        'user_name' => 'Владик',
        'user_avatar' => 'userpic.jpg'
    ],
    [
        'title' => 'Озеро Байкал',
        'type' => 'post-text',
        'post_content' => 'Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.',
        'user_name' => 'Владик',
        'user_avatar' => 'userpic.jpg'
    ]
];

function crop_text($text, $number_char = 300)
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

function GetDateRelativeFormat(string $date): string
{
    if (mb_strlen($date) == 0) {
        return "Дата не указанна";
    }

    $datePost = date_create("$date");
    $dateNow = date_create("now");
    $interval = date_diff($datePost, $dateNow);

    $minutes = $interval->format('%i');
    $hours = $interval->format('%H');
    $days = $interval->format('%d');
    $months = $interval->format('%m');
    $years = $interval->format('%Y');

    if ($minutes != 0) {
        $timeHasPassed = $minutes . ' ' . get_noun_plural_form($minutes, "минута", "минуты", "минут") . " назад";
    } elseif ($hours != 0) {
        $hours = floor($hours);
        $timeHasPassed = $hours . ' ' . get_noun_plural_form($hours, "час", "часа", "часов") . " назад";
    } elseif ($days != 0) {
        $timeHasPassed = $days . ' ' . get_noun_plural_form($days, "день", "дня", "дней") . " назад";
    } elseif ($days > 7 && $days < 35) {
        $week = floor($days / 7);
        $timeHasPassed = $week . ' ' . get_noun_plural_form($week, "неделя", "недели", "недель") . " назад";
    } elseif ($months != 0) {
        $months = floor($months);
        $timeHasPassed = $months . ' ' . get_noun_plural_form($months, "месяц", "месяца", "месяцев") . " назад";
    } elseif ($years != 0) {
        $years = floor($years);
        $timeHasPassed = $years . ' ' . get_noun_plural_form($months, 'год', 'года', 'лет') . ' назад';
    } else {
        $timeHasPassed = 'меньше минуты назад';
    }

    return $timeHasPassed;
}

$page_content = include_template('main.php', ['posts' => $posts]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'user_name' => $user_name,
    'is_auth' => $is_auth,
    'title' => 'readme: популярное'
]);

print($layout_content);
