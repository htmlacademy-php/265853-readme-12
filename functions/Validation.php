<?php

/**Набор функций для валидации*/
class Validation
{

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
}
