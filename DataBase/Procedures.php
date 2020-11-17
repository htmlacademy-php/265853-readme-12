<?php
require_once('DataBase\SqlServerHelper.php');

/**Список хранимых процедур из БД MySQL*/
class Procedures
{
    /**Получить типы контента*/
    public const  sqlTypeContent = "CALL GetTypeContent";
    /**Получить список постов*/
    public const  sqlPostUserType = "CALL GetPostUserType";
}
