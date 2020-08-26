<!-- пост-ссылка -->
<div class="post__main">
    <div class="post-link__wrapper">
        <a class="post-link__external" href="http://<?= htmlspecialchars($post['link'],ENT_QUOTES); ?>" title="Перейти по ссылке">
            <div class="post-link__info-wrapper">
                <div class="post-link__icon-wrapper">
                    <img src="https://www.google.com/s2/favicons?domain=<?= htmlspecialchars($post['link'],ENT_QUOTES); ?>" alt="Иконка">
                </div>
                <div class="post-link__info">
                    <h3><?= htmlspecialchars($post['title'],ENT_QUOTES); ?></h3>
                </div>
            </div>
        </a>
    </div>
</div>
