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
     * @return array Ошибка валидации
     */
    function checkUrl(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return array('status' => 'false',
                'message' => 'Не вереный формат ссылки.');
        }
        return array('status' => 'true',
            'message' => 'Ссылка верная');
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
    function checkTags(string $tags)
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
    }

    /**
     * Функция для валидации по длине
     * @param string $text текст
     * @param int $minLength минимальная длина
     * @param int $maxLength максимальная длина
     * @return string Ошибка валидации
     */
    function validateLength(string $text, int $minLength = 3, int $maxLength = 30){
        if (mb_strlen($text) < $minLength || mb_strlen($text) > $maxLength) {
            return "Значение поля должно быть не меньше $minLength и не больше $maxLength символов";
        }
    }
}
