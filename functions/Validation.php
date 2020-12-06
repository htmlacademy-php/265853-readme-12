<?php
require_once('DataBase\SqlServerHelper.php');

/**Набор функций для валидации*/
class Validation
{

    public function __construct()
    {
        $this->sqlServerHelper = new SqlServerHelper();
    }

    /**
     * Функция добавляет ошибки обязательных полей в массив
     * @param  $array *массив в который добавить
     * @param  $field *поле которое проверяем
     * @param  $check *проверка
     * @return array массив данных
     */
    function RecordFaultyRules($array, $field, $check)
    {
        return array_merge($array,
            [
                $field => $check

            ]
        );
    }

    /**
     * Функция проверяет ссылку на корректность
     * @param string $url ссылка
     *
     * @return string Ошибка валидации
     */
    function checkUrl(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return 'Не вереный формат ссылки.';
        }
    }

    /**
     * Функция для валидации тегов
     * @param string $tags тег
     *
     * @return string Ошибка валидации
     */
    function checkTags(string $tags, bool $response = false)
    {
        $pattern = '^[\s\wа-яА-ЯёЁ]+$';
        if (!mb_ereg($pattern, $tags)) {
            return 'Теги должны состоять только из букв и цифр.';
        }

        $tags_line = trim(htmlspecialchars($tags));
        $tags = explode(" ", mb_strtolower($tags_line));
        $tags_array = array_unique($tags, SORT_STRING);

        //Не увидел жестких требований к валидации поэтому решил ограничить по размеру поля в БД
        foreach ($tags_array as $tag) {
            if (mb_strlen($tag) > 50) {
                return "Тег: {$tag} слишком длинный. Подберите синоним или убедитесь что тег состоит из одного слова";
            }
        }
        if ($response == true) {
            return array_unique($tags, SORT_STRING);
        }
    }

    /**
     * Функция для валидации по длине
     * @param string $text текст
     * @param int $minLength минимальная длина
     * @param int $maxLength максимальная длина
     * @return string Ошибка валидации
     */
    function validateLength(string $text, int $minLength = 3, int $maxLength = 30)
    {
        if (mb_strlen($text) < $minLength || mb_strlen($text) > $maxLength) {
            return "Значение поля должно быть не меньше $minLength и не больше $maxLength символов";
        }
    }

    /**
     * Функция проверяет заполнены ли поля формы по указаным ключам
     * @param array $required_fields
     *
     * @return array массив данных
     */
    function checkRequiredFields(array $required_fields): array
    {
        $errors = [];
        foreach ($required_fields as $key => $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = "Поле должно быть заполнено";
            }
        }
        return $errors;
    }

    /**
     * Функция проверяет ошибки по соответствующим ключам и записывает их в массив
     * @param array $rules массив со значениями которые надо проверить
     * @param array $errors массив с уже существующими ошибками
     * @param array $array массив с данными для проверки
     *
     * @return array массив данных с ошибками
     */
    function checkRules(array $rules, array $errors, array $array): array
    {
        foreach ($array as $key => $value) {
            if (empty($errors[$key]) && isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule;
            }
        }
        return $errors;
    }

    /**
     * Проверка email
     *
     * @param mysqli $connect Строка соединения
     * @param string $email email который нужно проверить
     *
     * @return string|null Ошибки если валидация не прошла, null в случаи успеха
     */
    function checkEmail(mysqli $connect, string $email)
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Введите корректный адрсе электронной почты.';
        }

        $sql = "SELECT `email` FROM `users` WHERE `email` LIKE '$email'";
        $a = $this->sqlServerHelper->requestHandler($connect, $sql);
        if (!empty($a)) {
            return 'Пользователь с таким адрессом электронной почты уже зарагестрирован.';
        }
        return null;
    }

    /**
     * Проверка login
     *
     * @param mysqli $connect Строка соединения
     * @param string $login login который нужно проверить
     *
     * @return string|null Ошибки если валидация не прошла, null в случаи успеха
     */
    function checkLogin(mysqli $connect, string $login)
    {
        $check_length = $this->validateLength($login, 3, 50);
        if ($check_length !== NULL) {
            return $check_length;
        }
        $pattern = '^[\s\wа-яА-ЯёЁ]+$';
        if (!mb_ereg($pattern, $login)) {
            return 'Логин должен состоять только из букв английского или русского алфавита и цифр';
        }

        $search_sql = "SELECT `login` FROM `users` WHERE `login` = '$login'";
        $found_user = $this->sqlServerHelper->requestHandler($connect, $search_sql);
        if (!empty($found_user)) {
            return "Логин: $login уже занят";
        }
        return null;
    }

    /**
     * Проверка пороля
     *
     * @param mysqli $connect Строка соединения
     * @param string $password пароль который нужно проверить
     *
     * @return string|null Ошибки если валидация не прошла, null в случаи успеха
     */
    function checkPassword(mysqli $connect, string $password)
    {
        $check_length = $this->validateLength($password, 6, 50);
        if ($check_length !== NULL) {
            return $check_length;
        }
        $pattern = '^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$';
        if (!mb_ereg($pattern, $password)) {
            return 'Пароль должен содержитать символы по меньшей мере из трех следующих категорий:' . "<br/>" .
                '1) Английские буквы верхнего регистра (A – Z)' . "<br/>" .
                '2) Английские символы нижнего регистра (a – z)' . "<br/>" .
                '3) Базовые 10 цифр (0 – 9)' . "<br/>" .
                '4) Non-alphanumeric (Например:!, $, # Или%)' . "<br/>" .
                '5) Юникодные символы';
        }
        return null;
    }

    /**
     * Проверка на совпадения значений
     *
     * @param string|int $value_one Первое значение
     * @param string|int $value_two Второе значение
     *
     * @return string|null Ошибки если валидация не прошла, null в случаи успеха
     */
    function compareValues($value_one, $value_two)
    {
        if ($value_one !== $value_two) {
            return 'Пароли не совпадают';
        }
        return null;
    }
}
