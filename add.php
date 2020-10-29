<?php
require_once('helpers.php');
require_once('connection.php');
require_once('DataBase\Procedures.php');
require_once('DataBase\SqlFunctions.php');
require_once('DataBase\SqlServerHelper.php');
require_once('functions\Validation.php');
require_once('functions\UploadException.php');

$is_auth = rand(0, 1);
$user_name = 'Егор Толбаев'; // укажите здесь ваше имя
$page_title = 'Readme: Публикация';

$procedures = new Procedures();
$sqlFunctions = new SqlFunctions();
$sqlServerHelper = new SqlServerHelper();
$validation = new Validation();

//$errors = ['heading' => 'Заголовок. Это поле должно быть заполнено.'];
$errors = [];
//TODO: не забыть проверить ссылку на ютубе при помощи check_youtube_url из helper.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //есть ссылка
    if (isset($_POST['photo-url']) and !empty($_POST['photo-url'])) {
        $result = $validation->checkUrl($_POST['photo-url']);
        if ($result['status'] === "true") {
            $validation->getImgByLink($_POST['photo-url']);
        } else {
            $errors = ['photo-url' => $result['message']];
        }
    }

    //если фаил есть
    if (!empty($_FILES['userpic-file-photo']['name'])) {
        //если он загружен без ошибок
        if ($_FILES['userpic-file-photo']['error'] === UPLOAD_ERR_OK) {
            $validation->uploadImgFile($_FILES);
        } else {
            //что бы знать по какой причине фаил не загружен
            $error = new UploadException($_FILES['userpic-file-photo']['error']);
        }
    }

    $page_parameters['type'] = $_POST['type'];
    $posts = $_POST;
    $required_fields = ['heading'];

}

function getTypeFromRequest(array $get, array $post = []): ?string
{
    if (isset($get['type'])) {
        return (string)$get['type'];
    } elseif (isset($post['type'])) {
        return (string)$post['type'];
    }
    return null;
}

$types = $sqlServerHelper->StoredProcedureHandler($mainConnection, Procedures::sqlTypeContent);
$form_type = getTypeFromRequest($_GET, $_POST);

$content = include_template("add-forms/" . $form_type . "-form.php", [
    'errors' => $errors
]);
$page_content = include_template('adding-post.php', [
    'content_types' => $types,
    'form_type' => $form_type,
    'content' => $content,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'page_content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => $page_title
]);

print($layout_content);
