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
        if(!file_put_contents(($file_path . basename($url)), $content)){
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
        if(!move_uploaded_file($files['userpic-file-photo']['tmp_name'], $upload_file)){
            return 'Файл небыл згружен.';
        }
    }
}
