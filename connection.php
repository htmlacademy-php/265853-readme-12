<?php
/**Адрес сервера*/
define('HOST', 'localhost');
/**Имя пользователя*/
define('USER', 'root');
/**Пароль*/
define('PASSWORD', 'root');
/**Имя базы данных*/
define('DATABASE', 'readme');
/**Имя тестовой базы данных*/
//Сделал для своего удобства
//define('DATABASE', 'readmeTest');

class Connection
{
    /**Главное подключение к БД*/
    public $mainConnection;

    function __construct()
    {
        $this->mainConnection = $this->dbConnect();
    }

    /**
     * Устанавливает соединение с базой данных(БД) и возвращает объект соединения
     * @return mysqli $connect  Объект-соединение с БД
     */
    function dbConnect(): mysqli
    {
        $connect = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

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
