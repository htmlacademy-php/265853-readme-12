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

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $page_parameters['type'] = $_POST['type'];
    $posts = $_POST;
    $required_fields = ['heading'];

    $rules = [
        'heading' => $validation->validateLength($posts['heading']),
        'tags' => $validation->checkTags($posts['tags'])
    ];

    switch ($posts['type']) {
        case 'text':
            $required_fields[] = 'post-text';
            $rules = array_merge(
                $rules,
                [
                    'post-text' => $validation->validateLength($posts['post-text'], 10, 600)
                ]
            );
            break;
        case 'quote':
            $required_fields = array_merge($required_fields, ['quote-text', 'quote-author']);
            $rules = array_merge(
                $rules,
                [
                    'cite-text' => $validation->validateLength($posts['quote-text'], 10, 60)
                ]
            );
            break;
        case 'video':
            $required_fields[] = 'video-url';
            $rules = array_merge(
                $rules,
                [
                    'video-url' => $validation->checkUrl($posts['video-url'])

                ]
            );
            break;
        case 'link':
            $required_fields[] = 'post-link';
            $rules = array_merge(
                $rules,
                [
                    'post-link' => $validation->checkUrl($posts['post-link'])

                ]
            );
            break;
        case 'photo':
            if (empty($_FILES['userpic-file-photo']['name'])) {
                $required_fields[] = 'photo-url';
                $rules = array_merge(
                    $rules,
                    [
                        'photo-url' => $validation->checkUrl($posts['photo-url'])

                    ]
                );
            }
            break;
    }

    if ($posts['type'] === 'video') {
        /*
         if(!check_youtube_url($posts['video-url'])) {
           $errors['video-url'] = "Неверная ссылка, убедитесь что ссылка ведет на youtube";
        }
        */
        $rules = array_merge(
            $rules,
            [
                'video-url' => (/*isset($posts['video-url']) and $posts['video-url'] !== "" and*/ $rules['video-url'] == null) ? $validation->my_check_youtube_url($posts['video-url']) : $rules['video-url']

            ]
        );
    }

    if ($posts['type'] === 'photo') {
        if (!empty($_FILES['userpic-file-photo']['name'])) {
            //если он загружен без ошибок
            if ($_FILES['userpic-file-photo']['error'] === UPLOAD_ERR_OK) {
                $validation->uploadImgFile($_FILES);
            } else {
                //что бы знать по какой причине фаил не загружен
                $error = new UploadException($_FILES['userpic-file-photo']['error']);
            }
        } else if (isset($posts['photo-url'])) {
            if (empty($errors['photo-url'])) {
                $validation->getImgByLink($_POST['photo-url']);
            }
        }
    }

    $errors = $validation->checkRequiredFields($required_fields);
    $errors = $validation->checkRules($rules, $errors, $posts);

}
/**
 * Функция отдает значение если оно есть в  POST запросе
 * @param string $name название поля по которому нужно значение
 *
 * @return string значение из POST запроса
 */
function getPostValue(string $name)
{
    return $_POST[$name] ?? "";
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
