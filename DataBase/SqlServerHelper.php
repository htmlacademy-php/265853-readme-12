<?php

/**Набор функций для работы с SQl запросами*/
class SqlServerHelper
{
    /**
     * Обработка хранимых процедур
     *
     * @param mysqli $connect Строка соединения
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
     * @param mysqli $connect Строка соединения
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

    /**
     * Добавления постов в БД
     *
     * @param mysqli $connect Строка соединения
     * @param $stmt mysqli_stmt Запрос
     *
     * @return int|string Ответ
     */
    function addPostToDB(mysqli $connect, mysqli_stmt $stmt)
    {
        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            return 'Не удалось добавить пост' . mysqli_error($connect);
        }
        return mysqli_insert_id($connect);
    }

    /**
     * Добавления тегов в БД
     *
     * @param mysqli string Строка соединения
     * @param array|string $tags массив с тегами
     * @param int $post_id id добавленного поста
     * @return mysqli_result Ответ
     */
    function addTagsToPosts(mysqli $connect, array $tags, int $post_id)
    {
        $tag_sql = 'INSERT INTO hashtags (title) VALUES (?)';
        foreach ($tags as $tag) {
            if($tag) {
                $search_sql = "SELECT h.id FROM hashtags h WHERE h.title = '$tag'";

                $result = mysqli_query($connect, $search_sql);
                if (!$result) {
                    $error = mysqli_error($connect);
                    die("Ошибка MySQL: " . $error);
                }
                $search_result = mysqli_fetch_all($result, MYSQLI_ASSOC);

                if (!empty($search_result)) {
                    $tags_id[] = reset($search_result[0]);
                } else {
                    $values['title'] = $tag;
                    $tag_stmt = db_get_prepare_stmt($connect, $tag_sql, $values);
                    $result = mysqli_stmt_execute($tag_stmt);
                    if ($result) {
                        $tags_id[] = mysqli_insert_id($connect);
                    } else {
                        return 'Не удалось добавить теги' . mysqli_error($connect);
                    }
                }
            }
        }
        foreach ($tags_id as $tag_id) {
            $tag_post_sql = "INSERT INTO post_hashtag (post_id,hashtag_id) VALUES ($post_id, $tag_id)";
            $tag_post_stmt = mysqli_prepare($connect, $tag_post_sql);
            $result = mysqli_stmt_execute($tag_post_stmt);
            if (!$result) {
                return 'Ошбика' . mysqli_error($connect);
            }
        }
        return $result;
    }
}
