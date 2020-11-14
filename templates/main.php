<?php
$sorting_number_views = (isset($sorting_parameters['sort_value']) && ($sorting_parameters['sort_value'] === 'number_views')) ? 'sorting__link--active' : null;
$sorting_likes = (isset($sorting_parameters['sort_value']) && ($sorting_parameters['sort_value'] === 'likes')) ? 'sorting__link--active' : null;
$sorting_post_date = (isset($sorting_parameters['sort_value']) && ($sorting_parameters['sort_value'] === 'date_add')) ? 'sorting__link--active' : null;
$sorting_button_all = (isset($sorting_parameters['type']) && ($sorting_parameters['type'] === 'all')) ? 'filters__button--active' : null;

$sorting_class = (isset($sorting_parameters['sorting']) && ($sorting_parameters['sorting'] === 'ASC')) ? 'sorting__link--reverse' : null;

$url_number_views = setUrl($sorting_parameters['type'], 'number_views', ($sorting_parameters['sorting'] === 'DESC') ? "ASC" : "DESC");
$url_likes = setUrl($sorting_parameters['type'], 'likes', ($sorting_parameters['sorting'] === 'DESC') ? "ASC" : "DESC");
$url_post_date = setUrl($sorting_parameters['type'], 'date_add', ($sorting_parameters['sorting'] === 'DESC') ? "ASC" : "DESC");
$usl_all = setUrl('all', $sorting_parameters['sort_value'], $sorting_parameters['sorting']);
?>

<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link  <?= $sorting_number_views ?> <?= $sorting_class ?>"
                           href="<?= $url_number_views ?>">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= $sorting_likes ?> <?= $sorting_class ?>" href="<?= $url_likes ?>">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= $sorting_post_date ?> <?= $sorting_class ?>"
                           href="<?= $url_post_date ?>">
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all <?= $sorting_button_all ?>"
                           href="<?= $usl_all ?>">
                            <span>Все</span>
                        </a>
                    </li>
                    <?php foreach ($types as $key => $value): ?>
                        <li class="popular__filters-item filters__item ">
                            <a href="<?= setUrl($value['icon_type'], $sorting_parameters['sort_value'], $sorting_parameters['sorting']) ?>"
                               class="filters__button filters__button--<?= $value['icon_type'] ?> <?= ($sorting_parameters['type'] === $value['icon_type']) ? 'filters__button--active' : ""; ?> button">
                                <span class="visually-hidden"><?= $value['type_name'] ?></span>
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= $value['icon_type'] ?>"></use>
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="popular__posts">
            <?php foreach ($posts as $key => $value):
                $timeHelper = new TimeHelper();
                $stringHelper = new StringHelper();

                $post_id = htmlspecialchars($value['id']);
                $post_content = htmlspecialchars($value['content_text']);
                $post_type = htmlspecialchars($value['icon_type']);
                $post_title = htmlspecialchars($value['title']);
                $user_avatar = htmlspecialchars($value['avatar']);
                $user_name = htmlspecialchars($value['login']);
                $video_url = htmlspecialchars($value['video_url']);
                $quote_author = htmlspecialchars($value['quote_author']);
                ?>
                <article class="popular__post post post-<?= $post_type ?>">
                    <header class="post__header">
                        <h2><a href="/post.php?post_id=<?= $post_id ?>"><?= $post_title ?></a></h2>
                    </header>
                    <div class="post__main">
                        <!--содержимое для поста-цитаты-->
                        <?php if ($post_type === 'quote'): ?>
                            <blockquote>
                                <p>
                                    <?= $post_content ?>
                                </p>
                                <cite><?= $quote_author ?></cite>
                            </blockquote>

                            <!--содержимое для поста-ссылки-->
                        <?php elseif ($post_type === 'link'): ?>
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="http://" title="Перейти по ссылке">
                                    <div class="post-link__info-wrapper">
                                        <div class="post-link__icon-wrapper">
                                            <img src="https://www.google.com/s2/favicons?domain=vitadental.ru"
                                                 alt="Иконка">
                                        </div>
                                        <div class="post-link__info">
                                            <h3><?= $post_title ?></h3>
                                        </div>
                                    </div>
                                    <span><?= $post_content ?></span>
                                </a>
                            </div>

                            <!--содержимое для поста-фото-->
                        <?php elseif ($post_type === 'photo'): ?>
                            <div class="post-photo__image-wrapper">
                                <img src="../img/<?= $post_content ?>" alt="Фото от пользователя" width="360"
                                     height="240">
                            </div>
                            <!--содержимое для поста-видео-->
                        <?php elseif ($post_type === 'video'): ?>
                            <div class="post-video__block">
                                <div class="post-video__preview">
                                    <?= embed_youtube_cover($video_url); ?>
                                </div>
                                <a href="<?= htmlspecialchars($video_url) ?>" class="post-video__play-big button">
                                    <svg class="post-video__play-big-icon" width="14" height="14">
                                        <use xlink:href="#icon-video-play-big"></use>
                                    </svg>
                                    <span class="visually-hidden">Запустить проигрыватель</span>
                                </a>
                            </div>
                        <?php else: ?>
                            <!--здесь содержимое карточки-->
                            <p><?= $stringHelper->cropText($post_content) ?></p>
                        <?php endif; ?>
                    </div>
                    <footer class="post__footer">
                        <div class="post__author">
                            <a class="post__author-link" href="#" title="Автор">
                                <div class="post__avatar-wrapper">
                                    <!--укажите путь к файлу аватара-->
                                    <img class="post__author-avatar" src="../img/<?= $user_avatar ?>"
                                         alt="Аватар пользователя">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= $user_name ?></b>
                                    <?php
                                    $post_date = $timeHelper->GetPostTime($key);
                                    ?>
                                    <time class="post__time" title="<?= $post_date->format('d.m.Y H:i') ?>"
                                          datetime="<?= $post_date->format('Y-m-d H:i:s') ?>"><?= $timeHelper->GetDateRelativeFormat($post_date); ?></time>
                                </div>
                            </a>
                        </div>
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                    <svg class="post__indicator-icon" width="20" height="17">
                                        <use xlink:href="#icon-heart"></use>
                                    </svg>
                                    <svg class="post__indicator-icon post__indicator-icon--like-active" width="20"
                                         height="17">
                                        <use xlink:href="#icon-heart-active"></use>
                                    </svg>
                                    <span>0</span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button" href="#"
                                   title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span>0</span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
