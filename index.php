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

    $markPost = strtotime($date); //преобразуем текстовую дату
    if ($markPost == false) {
        return "Дата не указанна";
    }

    $markNow = time();
    $markDiff = $markNow - $markPost;

    $timeInterval = [
        'minute' => 60,
        'hour' => 3600,
        'day' => 86400,
        'week' => 604800
    ];

    if ($markDiff / $timeInterval['minute'] < 60) {
        $numDiff = ceil($markDiff / $timeInterval['minute']);
        if ($markDiff == 0) {
            $timeHasPassed = 'меньше минуты назад';
        } else {
            $timeHasPassed = "$numDiff " . get_noun_plural_form($numDiff, "минута", "минуты", "минут") . " назад";
        }
    } else if ($markDiff / $timeInterval['hour'] < 24) {
        $numDiff = ceil($markDiff / $timeInterval['hour']);
        $timeHasPassed = "$numDiff " . get_noun_plural_form($numDiff, "час", "часа", "часов") . " назад";
    } elseif ($markDiff / $timeInterval['day'] < 7) {
        $numDiff = ceil($markDiff / $timeInterval['day']);
        $timeHasPassed = "$numDiff " . get_noun_plural_form($numDiff, "день", "дня", "дней") . " назад";
    } elseif ($markDiff / ($timeInterval['day'] * 7) < 5) {
        $numDiff = ceil($markDiff / ($timeInterval['day'] * 7));
        $timeHasPassed = "$numDiff " . get_noun_plural_form($numDiff, "неделя", "недели", "недель") . " назад";
    } else {
        $numDiff = ceil($markDiff / ($timeInterval['week'] * 5));
        $timeHasPassed = "$numDiff " . get_noun_plural_form($numDiff, "месяц", "месяца", "месяцев") . " назад";
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
