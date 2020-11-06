<?php
$validation_heading = (!empty($errors['heading'])) ? "form__input-section--error" : "";
$error_heading = !empty($errors['heading']) ? $errors['heading'] : "";

$validation_tags = (!empty($errors['tags'])) ? "form__input-section--error" : "";
$error_tags = !empty($errors['tags']) ? $errors['tags'] : "";

$validation_list_errors = (empty($errors)) ? 'visually-hidden' : "";

$isPhotoGet = (isset($_GET['type']) and $_GET['type'] != 'photo');
$isPhotoPost = (isset($_POST['type']) and $_POST['type'] != 'photo');
?>
<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">
                        <?php foreach ($content_types

                        as $post_type => $content_type): ?>
                        <li class="adding-post__tabs-item filters__item">
                            <a class="adding-post__tabs-link filters__button filters__button--<?= $content_type['icon_type']; ?> <?php if ($form_type == $content_type['icon_type']): ?> filters__button--active  tabs__item--active filters__button--active<?php endif; ?>"
                               href="../add.php?type=<?= $content_type['icon_type']; ?>">
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= $content_type['icon_type']; ?>"></use>
                                </svg>
                                <span><?= $content_type['type_name']; ?></span>
                            </a>
                        </li>
                    </ul>
                    <?php endforeach ?>
                </div>
                <div class="adding-post__tab-content">
                    <section class="adding-post__<?= $content_type['icon_type'] ?>">
                        <h2 class="visually-hidden">Форма добавления <?= $content_type['icon_type'] ?></h2>
                        <form class="adding-post__form form" action="../add.php" method="post"
                              enctype="multipart/form-data">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="heading">Заголовок <span
                                                class="form__input-required">*</span></label>
                                        <div
                                            class="form__input-section <?= $validation_heading ?>">
                                            <input class="adding-post__input form__input" id="heading" type="text"
                                                   name="heading" placeholder="Введите заголовок"
                                                   value="<?= getPostValue('heading') ?>">
                                            <button class="form__error-button button" type="button">!<span
                                                    class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Обнаружена ошибка</h3>
                                                <p class="form__error-desc"><?= $error_heading ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?= $content ?>
                                    <?php
                                    if ($isPhotoGet or $isPhotoPost): ?>
                                        <div class="adding-post__input-wrapper form__input-wrapper">
                                            <label class="adding-post__label form__label" for="tags">Теги</label>
                                            <div
                                                class="form__input-section <?= $validation_tags ?>">
                                                <input class="adding-post__input form__input" id="tags" type="text"
                                                       name="tags" placeholder="Введите теги"
                                                       value="<?= getPostValue('tags') ?>">
                                                <button class="form__error-button button" type="button">!<span
                                                        class="visually-hidden">Информация об ошибке</span></button>
                                                <div class="form__error-text">
                                                    <h3 class="form__error-title">Обнаружена ошибка</h3>
                                                    <p class="form__error-desc"><?= $error_tags ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="adding-post__buttons">
                                        <button class="adding-post__submit button button--main" type="submit">
                                            Опубликовать
                                        </button>
                                        <a class="adding-post__close" href="#">Закрыть</a>
                                    </div>
                                </div>
                                <div class="form__invalid-block <?= $validation_list_errors ?>">
                                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                    <ul class="form__invalid-list">
                                        <?php foreach ($errors as $key => $error) : ?>
                                            <li class="form__invalid-item"><?= $error ?></li>
                                        <?php endforeach ?>
                                    </ul>
                                </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>
