<?php
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
$connect = dbConnect($host, $user, $password, $database);
