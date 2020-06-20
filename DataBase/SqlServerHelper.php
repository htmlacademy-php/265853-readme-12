<?php

/**Набор функций для работы с SQl запросами*/
class SqlServerHelper
{
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
}
