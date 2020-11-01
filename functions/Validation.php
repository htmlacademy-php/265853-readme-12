<?php

/**Набор функций для валидации*/
class Validation
{
    private $valid_file_types, $maximum_file_size;

    function __construct()
    {
        $this->valid_file_types = ['image/png', 'image/jpeg', 'image/gif'];
        $this->maximum_file_size = 104857600;
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
     * Функция для загрузки изображения по URL
     * @param string $url ссылка на сайт
     *
     * @return string Ошибка валидации
     */
    function getImgByLink(string $url)
    {
        ob_start();
        $content = file_get_contents($url);
        ob_get_clean();

        if (!$content) {
            return 'Файл по данной ссылке не найден';
        }

        $url_with_parameters = explode("?", $url);
        $url = $url_with_parameters[0];
        $file_info = new finfo(FILEINFO_MIME_TYPE);

        if (!in_array($file_info->buffer($content), $this->valid_file_types)) {
            return "Не подходящий формат изображения. Используйте jpg, png или gif";
        }
        $file_path = (__DIR__ . '/..') . "/uploads/";
        if (!file_exists($file_path)) {
            mkdir($file_path, 0777, true);
        }
        if (!file_put_contents(($file_path . basename($url)), $content)) {
            return 'Файл небыл згружен';
        }
    }

    /**
     * Функция для загрузки прикрепленного изображения
     * @param array $files данные о файле
     *
     * @return string Ошибка валидации
     */
    function uploadImgFile(array $files)
    {
        if (($files['userpic-file-photo']['size'] >= $this->maximum_file_size)) {
            return 'Файл слишком большой!';
        }
        if (!in_array($files['userpic-file-photo']['type'], $this->valid_file_types)) {
            return 'Не подходящий формат прикрепленного изображения. Используйте jpg, png или gif.';
        }
        $file_path = (__DIR__ . '/..') . "/uploads/";
        if (!file_exists($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $upload_file = $file_path . basename($files['userpic-file-photo']['name']);
        if (!move_uploaded_file($files['userpic-file-photo']['tmp_name'], $upload_file)) {
            return 'Файл небыл згружен.';
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
        //Сделал такую валидацию, что бы можно было вводить буквы и цифры и знак хештега("#")
        if (preg_match('/[^a-zа-я-Z0-9-# ]+/msiu', $tags)) {
            return 'Теги должны состоять только из букв и цифр, допустим знак решётка(#).';
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

    //Хотел использовать check_youtube_url из helper.php но он всегда отдает false:(

    /**
     * Функция проверяет доступно ли видео по ссылке на youtube
     * @param string $youtube_url Ссылка на youtube видео
     *
     * @return string Ошибку если валидация не прошла
     */
    function my_check_youtube_url($youtube_url)
    {
        $headers = get_headers('https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . extract_youtube_id($youtube_url));
        if (!is_array($headers) or $headers[0] !== 'HTTP/1.0 200 OK') {
            return "Видео по такой ссылке не найдено. Проверьте ссылку на видео";
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
