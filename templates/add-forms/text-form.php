<?php
$validation_post_text = (!empty($errors['post-text'])) ? "form__input-section--error" : "";
$error_post_text = !empty($errors['post-text']) ? $errors['post-text'] : "";
?>
<h2 class="visually-hidden">Форма добавления текста</h2>
<form class="adding-post__form form" action="../../add.php" method="post">
    <input type="hidden" id="type" name="type" value="text">
    <div class="form__text-inputs-wrapper">
        <div class="form__text-inputs">
            <div class="adding-post__textarea-wrapper form__textarea-wrapper">
                <label class="adding-post__label form__label" for="post-text">Текст поста <span
                        class="form__input-required">*</span></label>
                <div
                    class="form__input-section <?= $validation_post_text ?>">
                    <textarea class="adding-post__textarea form__textarea form__input" id="post-text" name="post-text"
                              placeholder="Введите текст публикации"><?= getPostValue('post-text') ?></textarea>
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                    </button>
                    <div class="form__error-text">
                        <h3 class="form__error-title">Обнаружена ошибка</h3>
                        <p class="form__error-desc"><?= $error_post_text ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
