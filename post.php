<?php
require_once('helpers.php');
require_once('connection.php');
require_once('DataBase\Procedures.php');
require_once('DataBase\SqlFunctions.php');

$is_auth = rand(0, 1);
$user_name = 'Егор Толбаев'; // укажите здесь ваше имя
$page_title = 'Readme: Публикация';

$procedures = new Procedures();
$sqlFunctions = new SqlFunctions();

$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;

$post = $sqlFunctions->GetPostById($mainConnection, $post_id)[0];

if (!$post) {
    $page_content = include_template('Error-404.php', []);
} else {
    $post_content = include_template("post-types/post-{$post['icon_type']}.php", ['post' => $post]);
    $posts_count = $sqlFunctions->GetUserPostsCount($mainConnection, $post['user_id']);
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
