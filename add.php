<?php
require_once('helpers.php');
require_once('connection.php');
require_once('DataBase\Procedures.php');
require_once('DataBase\SqlFunctions.php');
require_once('DataBase\SqlServerHelper.php');
require_once('functions\Validation.php');
require_once('functions\UploadException.php');
require_once('functions\Upload.php');

$is_auth = rand(0, 1);
$user_name = 'Егор Толбаев'; // укажите здесь ваше имя
$page_title = 'Readme: Публикация';

$sqlServerHelper = new SqlServerHelper();
$connection = new Connection();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sqlFunctions = new SqlFunctions();
    $validation = new Validation();

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
                    'quote-text' => $validation->validateLength($posts['quote-text'], 10, 60)
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
            if (empty($_FILES['user-file-photo']['name'])) {
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
        $rules = array_merge(
            $rules,
            [
                'video-url' => (/*isset($posts['video-url']) and $posts['video-url'] !== "" and*/ $rules['video-url'] == null) ? $validation->my_check_youtube_url($posts['video-url']) : $rules['video-url']

            ]
        );
    }

    if ($posts['type'] === 'photo') {
        if (!empty($_FILES['user-file-photo']['name'])) {
            $upload = new Upload();
            //если он загружен без ошибок
            if ($_FILES['user-file-photo']['error'] === UPLOAD_ERR_OK) {
                $upload->uploadImgFile();
            } else {
                //что бы знать по какой причине фаил не загружен
                $error = new UploadException($_FILES['user-file-photo']['error']);
            }
        } else if (isset($posts['photo-url'])) {
            if (empty($errors['photo-url'])) {
                $upload = new Upload();
                $upload->getImgByLink();
            }
        }
    }

    $errors = $validation->checkRequiredFields($required_fields);
    $errors = $validation->checkRules($rules, $errors, $posts);

    if (empty($errors)) {
        $user_id = rand(1, 4);
        $db_post['title'] = $_POST['heading'];
        $type_id = $sqlFunctions->GetTypePostId($connection->mainConnection, $posts['type']);
        switch ($posts['type']) {
            case 'text':
                $column = 'content_text';
                $db_post['content_text'] = $posts['post-text'];
                break;
            case 'quote':
                $column = 'content_text, quote_author';
                $db_post['content_text'] = $posts['quote-text'];
                $db_post['quote_author'] = $posts['quote-author'];
                break;
            case 'photo':
                $column = 'img_url';
                $db_post['img_url'] = (!empty(basename($_FILES['user-file-photo']['name']))) ? '../uploads/' . basename($_FILES['user-file-photo']['name']) : '../uploads/' . basename($posts['photo-url']);
                break;
            case 'video':
                $column = 'video_url';
                $db_post['video_url'] = $posts['video-url'];
                break;
            case 'link':
                $column = 'link';
                $db_post['link'] = $posts['post-link'];
                break;
            default:
                throw new Exception('Unexpected value');
        }


        $sql = "INSERT INTO posts (title, $column, user_id, type_id) VALUES (?, ?, $user_id, $type_id)";
        if ($posts['type'] === 'quote') {
            $sql = "INSERT INTO posts (title, $column, user_id, type_id) VALUES (?, ?, ?, $user_id, $type_id)";
        }

        $tags = $validation->checkTags($posts['tags'], true);

        $stmt = db_get_prepare_stmt($connection->mainConnection, $sql, $db_post);

        $post_id = $sqlServerHelper->addPostToDB($connection->mainConnection, $stmt);

        $result = $sqlServerHelper->addTagsToPosts($connection->mainConnection, $tags, $post_id);
        if ($result) {
            header("Location: post.php?post_id=" . $post_id);
        }
    }
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

$types = $sqlServerHelper->StoredProcedureHandler($connection->mainConnection, Procedures::sqlTypeContent);

$form_type = $_REQUEST['type'];

$content = include_template("add-forms/" . $form_type . "-form.php", [
    'errors' => $errors
]);
$page_content = include_template('adding-post.php', [
    'content_types' => $types,
    'form_type' => '$form_type',
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
