<?php
/**Адрес сервера*/
define('HOST', 'localhost');
/**Имя пользователя*/
define('USER', 'root');
/**Пароль*/
define('PASSWORD', 'root');
/**Имя базы данных*/
define('DATABASE', 'readme');

class Connection
{
    private $host, $user, $password, $database;

    function __construct($host, $user, $password, $database)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->dbConnect();
    }

    /**
     * Устанавливает соединение с базой данных(БД) и возвращает объект соединения
     * @return mysqli $connect  Объект-соединение с БД
     */
    function dbConnect(): mysqli
    {
        $connect = mysqli_connect($this->host, $this->user, $this->password, $this->database);

        if (!$connect) {
            exit("Ошибка подключения: " . mysqli_connect_error());
        }
        mysqli_set_charset($connect, "utf8");
        return ($connect);
    }

    function dbClose(mysqli $connection)
    {
        mysqli_close($connection);
    }
}

$connection = new Connection(HOST, USER, PASSWORD, DATABASE);
/**Главное подключение к БД*/
$mainConnection = $connection->dbConnect();
