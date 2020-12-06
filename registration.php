<?php
require_once('helpers.php');
require_once('connection.php');
require_once('DataBase\Procedures.php');
require_once('DataBase\SqlFunctions.php');
require_once('DataBase\SqlServerHelper.php');
require_once('functions\Validation.php');
require_once('functions\UploadException.php');
require_once('functions\Upload.php');


$user_name = 'Егор Толбаев'; // укажите здесь ваше имя
$page_title = 'Readme: Регистрация';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $connection = new Connection();
    $sqlFunctions = new SqlFunctions();
    $validation = new Validation();

    $registration_data = $_POST;

    $required_fields = ['email', 'login', 'password', 'password-repeat'];

    //определяем список проверок для полей
    $rules = [
        'email' => $validation->checkEmail($connection->mainConnection, $registration_data['email']),
        'login' => $validation->checkLogin($connection->mainConnection, $registration_data['login']),
        'password' => $validation->checkPassword($connection->mainConnection, htmlspecialchars(stripslashes($registration_data['password']))), //обработать $password чтобы скрипты не работали и чтобы тэги не работали
        'password-repeat' => $validation->compareValues(htmlspecialchars(stripslashes($registration_data['password'])), htmlspecialchars(stripslashes($registration_data['password-repeat'])))
    ];

    $errors = $validation->checkRequiredFields($required_fields);

    $errors = $validation->checkRules($rules, $errors, $registration_data);

    if (!empty($_FILES['user-file-photo']['name'])) {
        $registration_data = array_merge($registration_data, ['user-file-photo' => '']);
        //если он загружен без ошибок
        if ($_FILES['user-file-photo']['error'] === UPLOAD_ERR_OK) {
            $upload = new Upload();
            $rules = $validation->RecordFaultyRules($rules, 'user-file-photo', $upload->uploadImgFile());
        } else {
            //что бы знать по какой причине фаил не загружен
            $rules = $validation->RecordFaultyRules($rules, 'user-file-photo', new UploadException($_FILES['picture']['error']));
        }
    }

    $errors = $validation->checkRules($rules, $errors, $registration_data);

    if (empty($errors)) {
        $sql = 'INSERT INTO users (email, login, password, avatar) VALUES (?, ?, ?, ?)';
        $user = [
            'email' => $registration_data['email'],
            'login' => $registration_data['login'],
            'password' => password_hash(htmlspecialchars(stripslashes($registration_data['password'])), PASSWORD_DEFAULT),
            'avatar' => (!empty($_FILES['user-file-photo']['name'])) ? $_FILES['user-file-photo']['name'] : 'default.jpg'
        ];

        $stmt = db_get_prepare_stmt($connection->mainConnection, $sql, $user);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            header("Location: index.php");
        }

        $errors['input-file'] = 'не удалось зарегистрировать нового пользователя' . mysqli_error($connection->mainConnection);
    }
}
//TODO: выкинуть повтор
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

$page_content = include_template('registration-show.php', [
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'page_content' => $page_content,
    'title' => $page_title,
    'is_auth' => 0,
    'user_name' => $user_name,
]);

print($layout_content);
