<?php
require_once "index.php";

$is_auth = rand(0, 1);
$user_name = 'Егор Толбаев'; // укажите здесь ваше имя
$page_title = 'Readme: Публикация';

$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;

//region Функции для получения информации из БД
/**
 * Полная информация по посту  по его id
 * @param mysqli $connect
 * @param string $post_id
 * @return array|null
 */
function GetPostById(mysqli $connect, string $post_id)
{
    $sql = "SELECT p.*, ct.icon_type, u.avatar, u.login,
    IFNULL((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes_count,
    IFNULL((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments_count,
    IFNULL((SELECT COUNT(*) FROM subscriptions sub WHERE sub.user_id = p.user_id), 0) AS subscribers_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type ct ON p.type_id = ct.id
    LEFT JOIN likes l ON l.post_id = p.id
    LEFT JOIN comments com ON com.post_id = p.id
    LEFT JOIN subscriptions sub ON sub.user_id = p.user_id
    WHERE p.id = $post_id
    GROUP BY l.id";
    $result = requestHandler($connect, $sql);
    return empty($result) ? NULL : $result;
}

/**
 * Количество постов пользователя по id
 * @param mysqli $connect
 * @param string $user_id идентификатор пользователя
 * @return array
 */
function GetUserPostsCount(mysqli $connect, string $user_id)
{
    $sql = "SELECT COUNT(*) AS posts_count FROM posts p
    WHERE p.user_id = $user_id";
    $result = requestHandler($connect, $sql);
    return empty($result) ? NULL : $result;
}
//endregion

$post = GetPostById($mainConnection, $post_id)[0];

if (!$post) {
    //header("HTTP/1.0 404 Not Found");
    //$error_msg = 'Страница не найдена.Ошибка 404: ' . mysqli_error($mainConnection);
    //die($error_msg);
    $page_content = include_template('Error-404.php', []);
}
else {
    $post_content = include_template("post-types/post-{$post['icon_type']}.php", ['post' => $post]);
    $posts_count = GetUserPostsCount($mainConnection, $post['user_id']);
    $page_content = include_template('post-show.php', [
        'post_content' => $post_content,
        'post' => $post,
        'user_posts_count' => $posts_count[0]
    ]);
}
    $layout_content = include_template('layout.php', [
        'page_content' => $page_content,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'title' => $page_title
    ]);

print($layout_content);
