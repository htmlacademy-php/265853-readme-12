-- -----------------------------------------------------
-- Создание списка типов контента для поста
-- -----------------------------------------------------
INSERT INTO `content_type` (`type_name`,`icon_type`)
VALUES ('Текст','text'),
	    ('Цитата','quote'),
	    ('Картинка','photo'),
		('Видео','video'),
		('Ссылка','link');

-- ------------------------------------------------------
-- Добавление пользователей
-- ------------------------------------------------------
INSERT INTO `users` (`date_add`,`email`,`login`,`password`,`avatar`)
VALUES ('2018-03-25 16:15:00', 'vlad@mail.ru', 'Владик', ' ', 'img/userpic-medium.jpg'),
	    ('2019-02-05 18:35:03', 'mark@gmail.com', 'Марк', ' ', 'img/userpic-petro.jpg'),
	    ('2020-05-020 00:34:15', 'larisa@gmail.com', 'Лариса', ' ', 'img/userpic-larisa-small.jpg'),
	    ('2019-12-19 12:25:05',  'vicktor@gmail.com', 'Виктор', ' ', 'img/userpic-mark.jpg');

-- -----------------------------------------------------
-- Создание списка постов
-- -----------------------------------------------------
INSERT INTO `posts` (`date_add`, `title`,`content_text`,`quote_author`,`img_url`,`video_url`,`link`,`number_views`,`user_id`,`type_id`)
VALUES ('2020-05-31 12:24:12', 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'С.А.Есенин', NULL, NULL, NULL, 10, 3, 2),
       ('2020-05-30 23:43:12', 'Игра престолов',
        'Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального сезона своего любимого сериала!Не могу дождаться начала финального сезона своего любимого сериала!',
        NULL, NULL, NULL, NULL, 1, 1, 1),
       ('2020-05-25 12:43:12', 'Наконец, обработал фотки!', NULL, NULL, 'img/rock-medium.jpg', NULL, NULL, 2, 4, 4),
       ('2020-05-17 12:43:12', 'Моя мечта', NULL, NULL, 'img/coast-medium.jpg', NULL, NULL, 3, 3, 3),
       ('2019-12-01 12:43:12', 'Лучшие курсы', NULL, NULL, NULL, NULL, 'www.htmlacademy.ru', 6, 1, 5),
       ('2020-03-02 12:43:12', 'Озеро Байкал', 'Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.',
		  NULL, NULL, NULL,NULL, 6, 1, 1);

-- -----------------------------------------------------
-- Создание списка комментариев
-- -----------------------------------------------------

INSERT INTO `comments` (`date_add`, `content`, `user_id`, `post_id`)
VALUES ('2020-05-31 12:30:31', 'Скорей бы!!!', 4, 2),
	    ('2020-04-05 10:10:11', 'Очень красиво', 3, 3);

-- -----------------------------------------------------
-- Получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента
-- -----------------------------------------------------

SELECT P.`id`, P.`title`, P.`content_text`,P.`img_url`,P.`video_url`,P.`link`, P.`number_views`, US.`login`, CT.`type_name`
FROM 	`posts` AS P
	INNER JOIN  `users` AS US
		ON P.`user_id` = US.`id`
	INNER JOIN  `content_type` AS CT
		ON P.`type_id` = CT.`id`
ORDER BY  P.`number_views` DESC

#Можно еще так, но при большом количестве данных нужно посмотреть на производительность
SELECT `ALL`.`id`,`ALL`.`title`,`ALL`.`content_text`,`ALL`.`number_views`,`ALL`.`login`,`ALL`.`type_name`
FROM (
	SELECT  P.`id`, P.`title`,P.`content_text`, P.`number_views`, US.`login`, CT.`type_name`
		FROM 	`posts` AS P
			INNER JOIN  `users` AS US
				ON P.`user_id` = US.`id`
			INNER JOIN  `content_type` AS CT
				ON P.`type_id` = CT.`id`
		WHERE P.`content_text` IS  NOT null
UNION
	SELECT  P.`id`, P.`title`,P.`img_url`, P.`number_views`, US.`login`, CT.`type_name`
		FROM 	`posts` AS P
			INNER JOIN  `users` AS US
				ON P.`user_id` = US.`id`
			INNER JOIN  `content_type` AS CT
				ON P.`type_id` = CT.`id`
		WHERE P.`img_url` IS  NOT null
UNION
	SELECT  P.`id`, P.`title`,P.`video_url`, P.`number_views`, US.`login`, CT.`type_name`
		FROM 	`posts` AS P
			INNER JOIN  `users` AS US
				ON P.`user_id` = US.`id`
			INNER JOIN  `content_type` AS CT
				ON P.`type_id` = CT.`id`
		WHERE P.`video_url` IS  NOT null
UNION
	SELECT  P.`id`, P.`title`,P.`link`, P.`number_views`, US.`login`, CT.`type_name`
		FROM 	`posts` AS P
			INNER JOIN  `users` AS US
				ON P.`user_id` = US.`id`
			INNER JOIN  `content_type` AS CT
				ON P.`type_id` = CT.`id`
		WHERE P.`link` IS  NOT null
) AS `ALL`
ORDER BY  `ALL`.`number_views` DESC

-- -----------------------------------------------------
-- Получить список постов для конкретного пользователя
-- -----------------------------------------------------

SELECT P.`id`, P.`content_text`,P.`img_url`,P.`video_url`,P.`link`,US.`login`
FROM `posts` AS P
	INNER JOIN  `users` AS US
		ON P.`user_id` = US.`id`
WHERE US.`id` = 1

#Можно еще так, но при большом количестве данных нужно посмотреть на производительность
SELECT `ALL`.`id`, `ALL`.`content_text`, `ALL`.`login`
FROM (
	SELECT P.`id`, P.`content_text`, US.`login`,US.`id` AS uID
		FROM `posts` AS P
			INNER JOIN  `users` AS US
				ON P.`user_id` = US.`id`
		WHERE P.`content_text` IS NOT NULL
UNION
	SELECT P.`id`, P.`img_url`, US.`login`,US.`id` AS uID
		FROM `posts` AS P
			INNER JOIN  `users` AS US
				ON P.`user_id` = US.`id`
		WHERE P.`img_url` IS NOT NULL
UNION
	SELECT P.`id`, P.`video_url`, US.`login`,US.`id` AS uID
		FROM `posts` AS P
			INNER JOIN  `users` AS US
				ON P.`user_id` = US.`id`
		WHERE P.`video_url` IS NOT NULL
UNION
	SELECT P.`id`, P.`link`, US.`login`,US.`id` AS uID
		FROM `posts` AS P
			INNER JOIN  `users` AS US
				ON P.`user_id` = US.`id`
		WHERE P.`link` IS NOT NULL
)`ALL`
WHERE `ALL`.`uID` = 1

-- -----------------------------------------------------------------------------------------------------
-- Получить список комментариев для одного поста, в комментариях должен быть логин пользователя
-- -----------------------------------------------------------------------------------------------------

SELECT P.`id`, C.`content`, US.`login`
FROM `comments` AS C
	INNER JOIN  `users` AS US
		ON C.`user_id` = US.`id`
	INNER JOIN `posts` AS P
		ON C.`post_id` = P.`id`
WHERE P.`id` = 2

-- -----------------------------------------------------
-- Добавить лайк к посту
-- -----------------------------------------------------

INSERT INTO `likes` (`user_id`, `post_id`)
VALUES
       (1, 3),
       (2, 5),
       (4, 2);

-- -----------------------------------------------------
-- Подписаться на пользователя
-- -----------------------------------------------------

INSERT  INTO `subscriptions` (`user_id`, `subscriber_id`)
VALUES (4, 1);
