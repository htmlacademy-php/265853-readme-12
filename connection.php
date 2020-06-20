<?php
class Connection
{
    /**Адрес сервера*/
    public const HOST = 'localhost';
    /**Имя пользователя*/
    public const USER = 'root';
    /**Пароль*/
    public const PASSWORD = 'root';
    /**Имя базы данных*/
    public const DATABASE = 'readme';

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
}

$connection = new Connection();
/**Главное подключение к БД*/
$mainConnection = $connection->dbConnect($connection::HOST, $connection::USER, $connection::PASSWORD, $connection::DATABASE);
