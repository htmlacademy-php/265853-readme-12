<h2 class="visually-hidden">Форма добавления фото</h2>
<form class="adding-post__form form" action="../../add.php" method="post" enctype="multipart/form-data">
    <input type="hidden" id="type" name="type" value="photo">
    <div class="form__text-inputs-wrapper">
        <div class="form__text-inputs">
            <div class="adding-post__input-wrapper form__input-wrapper">
                <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
                <div
                    class="form__input-section <?= (!empty($errors['photo-url'])) ? "form__input-section--error" : "" ?>">
                    <input class="adding-post__input form__input" id="photo-url" type="text" name="photo-url"
                           placeholder="Введите ссылку" value="<?= getPostValue('photo-url') ?>">
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                    </button>
                    <div class="form__error-text">
                        <h3 class="form__error-title">Заголовок сообщения</h3>
                        <p class="form__error-desc"><?= (!empty($errors['photo-url']) ? $errors['photo-url'] : "") ?></p>
                    </div>
                </div>
            </div>
            <div class="adding-post__input-wrapper form__input-wrapper">
                <label class="adding-post__label form__label" for="tags">Теги</label>
                <div class="form__input-section <?= (!empty($errors['tags'])) ? "form__input-section--error" : "" ?>">
                    <input class="adding-post__input form__input" id="tags" type="text" name="tags"
                           placeholder="Введите теги" value="<?= getPostValue('tags') ?>">
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                    </button>
                    <div class="form__error-text">
                        <h3 class="form__error-title">Заголовок сообщения</h3>
                        <p class="form__error-desc"><?= (!empty($errors['tags']) ? $errors['tags'] : "") ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="adding-post__input-file-container form__input-container form__input-container--file">
        <div class="adding-post__input-file-wrapper form__input-file-wrapper">
            <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
                <input class="adding-post__input-file form__input-file" id="userpic-file-photo" type="file"
                       name="userpic-file-photo" title=" ">
                <div class="form__file-zone-text">
                    <span>Перетащите фото сюда</span>
                </div>
            </div>
            <button class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button"
                    type="button">
                <span>Выбрать фото</span>
                <svg class="adding-post__attach-icon form__attach-icon" width="10" height="20">
                    <use xlink:href="#icon-attach"></use>
                </svg>
            </button>
        </div>
        <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">
        </div>
    </div>
</form>

