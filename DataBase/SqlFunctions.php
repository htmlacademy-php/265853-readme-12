<?php
require_once('DataBase\SqlServerHelper.php');

class SqlFunctions
{
    private $sqlServerHelper;

    public function __construct()
    {
        $this->sqlServerHelper = new SqlServerHelper();
    }

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
        return $this->sqlServerHelper->requestHandler($connect, $sql);
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
        $sql = "SELECT P.*, CT.`icon_type`, U.`avatar`, U.`login`, IFNULL(L.`likes`, 0) AS likes, IFNULL(COM.`comments`, 0) AS comments,
            IFNULL(P.`content_text`,IFNULL(P.`img_url`,IFNULL(P.`video_url`,P.`link`))) AS content_text,P.`video_url`
            FROM `posts` P
            INNER JOIN `users` U ON P.`user_id` = U.`id`
            INNER JOIN `content_type` CT ON P.`type_id` = CT.`id`
            LEFT JOIN (SELECT L.`post_id`, COUNT(*) AS likes FROM `likes` L GROUP BY L.`post_id`) AS L ON L.`post_id` = P.`id`
            LEFT JOIN (SELECT COM.`post_id`, COUNT(*) AS comments FROM `comments` COM GROUP BY COM.`post_id`) AS COM ON COM.`post_id` = P.`id`
            ORDER BY $sort_value $sorting";
        return $this->sqlServerHelper->requestHandler($connect, $sql);
    }

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
        $result = $this->sqlServerHelper->requestHandler($connect, $sql);
        return empty($result) ? NULL : $result;
    }

    /**
     * Получаем количество постов пользователя по id
     * @param mysqli $connect
     * @param string $user_id идентификатор пользователя
     * @return array
     */
    function GetUserPostsCount(mysqli $connect, string $user_id)
    {
        $sql = "SELECT COUNT(*) AS posts_count FROM posts p
                WHERE p.user_id = $user_id";
        $result = $this->sqlServerHelper->requestHandler($connect, $sql);
        return empty($result) ? NULL : $result;
    }

    /**
     * Функция для получения id типа
     * @param mysqli $connect Строка соединения
     * @param string $post_name тип поста
     *
     * @return
     */
    function GetTypePostId(mysqli $connect, string $post_name)
    {
        $post_name = "'" . $post_name . "'";
        $sql = "SELECT id FROM content_type WHERE icon_type = $post_name";
        $result = $this->sqlServerHelper->requestHandler($connect, $sql);
        $result = reset($result[0]);
        return empty($result) ? null : $result;
    }
}
