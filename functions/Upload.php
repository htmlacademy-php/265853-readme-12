<?php

/**Набор функций для загрузки файло на сервер*/
class Upload
{
    public $file_path;
    public $file_size;
    public $tmp_name;
    public $file_name;
    public $file_type;

    public $file_url;

    private $valid_file_types, $maximum_file_size;

    function __construct()
    {
        $this->startUpload();
    }

    public function startUpload()
    {
        $this->file_name = basename($_FILES['user-file-photo']['name']);
        $this->file_size = $_FILES['user-file-photo']['size'];
        $this->tmp_name = $_FILES['user-file-photo']['tmp_name'];
        $this->file_type = $_FILES['user-file-photo']['type'];

        $this->file_url = (!empty($_POST['photo-url'])) ? $_POST['photo-url'] : "";
        //TODO: пока что не придумал другого решения, решил проверять если есть тип то это фото из добавления поста иначе аватарка
        $this->file_path = $_SERVER['DOCUMENT_ROOT'] . ((!empty($_POST['type'])) ? "/uploads/" : "/userAvatar/");
        $this->valid_file_types = ['image/png', 'image/jpeg', 'image/gif'];
        $this->maximum_file_size = 104857600;
    }

    /**
     * Функция для загрузки изображения по URL
     *
     * @return string Ошибка валидации
     */
    function getImgByLink()
    {
        ob_start();
        $content = file_get_contents($this->file_url);
        ob_get_clean();

        if (!$content) {
            return 'Файл по данной ссылке не найден';
        }

        $url_with_parameters = explode("?", $this->file_url);
        $url = $url_with_parameters[0];
        $file_info = new finfo(FILEINFO_MIME_TYPE);

        if (!in_array($file_info->buffer($content), $this->valid_file_types)) {
            return "Не подходящий формат изображения. Используйте jpg, png или gif";
        }

        if (!is_dir($this->file_path)) {
            if (!mkdir($this->file_path, 0777, true)) {
                return 'Не удалось создать директорию...';
            }
        }
        if (!file_put_contents(($this->file_path . basename($url)), $content)) {
            return 'Файл не был загружен.';
        }
    }

    /**
     * Функция для загрузки прикрепленного изображения
     *
     * @return string Ошибка валидации
     */
    function uploadImgFile()
    {
        if (($this->file_size >= $this->maximum_file_size)) {
            return 'Файл слишком большой!';
        }
        if (!in_array($this->file_type, $this->valid_file_types)) {
            return 'Не подходящий формат прикрепленного изображения. Используйте jpg, png или gif.';
        }

        if (!is_dir($this->file_path)) {
            if (!mkdir($this->file_path, 0777, true)) {
                return 'Не удалось создать директорию...';
            }
        }
        $upload_file = $this->file_path . $this->file_name;
        if (!move_uploaded_file($this->tmp_name, $upload_file)) {
            return 'Файл не был загружен.';
        }
    }
}
