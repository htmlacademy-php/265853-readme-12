<div class="post-details__image-wrapper post-quote">
    <div class="post__main">
        <blockquote>
            <p>
                <?= htmlspecialchars($post['content'],ENT_QUOTES); ?>
            </p>
            <cite><?= htmlspecialchars($post['quote_author'],ENT_QUOTES); ?></cite>
        </blockquote>
    </div>
</div>
