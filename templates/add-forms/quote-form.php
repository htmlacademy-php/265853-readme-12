<h2 class="visually-hidden">Форма добавления цитаты</h2>
<form class="adding-post__form form" action="../../add.php" method="post">
    <input type="hidden" id="type" name="type" value="quote">
    <div class="form__text-inputs-wrapper">
        <div class="form__text-inputs">
            <div class="adding-post__input-wrapper form__textarea-wrapper">
                <label class="adding-post__label form__label" for="cite-text">Текст цитаты <span
                        class="form__input-required">*</span></label>
                <div class="form__input-section">
                    <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input"
                              id="cite-text" placeholder="Текст цитаты"></textarea>
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                    </button>
                    <div class="form__error-text">
                        <h3 class="form__error-title">Заголовок сообщения</h3>
                        <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                    </div>
                </div>
            </div>
            <div class="adding-post__textarea-wrapper form__input-wrapper">
                <label class="adding-post__label form__label" for="quote-author">Автор <span
                        class="form__input-required">*</span></label>
                <div class="form__input-section">
                    <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author">
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                    </button>
                    <div class="form__error-text">
                        <h3 class="form__error-title">Заголовок сообщения</h3>
                        <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

