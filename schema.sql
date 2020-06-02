CREATE DATABASE readme
        DEFAULT CHARACTER SET utf8
        DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE `users` (
    `id`       INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `date_add` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `email`    VARCHAR(50) NOT NULL UNIQUE,
    `login`    VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(50) NOT NULL,
    `avatar`   VARCHAR(128)
);

CREATE TABLE `content_type` (
    `id`        INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `type_name` VARCHAR(128) NOT NULL UNIQUE,
    `icon_type` VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE `posts` (
    `id`       INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `date_add` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `title`    VARCHAR(128) NOT NULL,
    `content_text` TEXT,
    `quote_author` VARCHAR(128),
    `img_url`      VARCHAR(128),
    `video_url`    VARCHAR(128),
    `link`         VARCHAR(256),
    `number_views` INT NOT NULL DEFAULT '0',
    `user_id` INT NOT NULL,
    `type_id` INT NOT NULL,
    FOREIGN KEY (`user_id`)  REFERENCES `users` (`Id`),
    FOREIGN KEY (`type_id`)  REFERENCES `content_type` (`Id`)
);

CREATE TABLE `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `date_add` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `content` TEXT NOT NULL,
    `user_id` INT NOT NULL,
    `post_id` INT NOT NULL,
    FOREIGN KEY (`user_id`)  REFERENCES `users` (`Id`),
    FOREIGN KEY (`post_id`)  REFERENCES `posts` (`Id`)
);

CREATE TABLE `likes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `user_id` INT NOT NULL,
  `post_id` INT NOT NULL,
  FOREIGN KEY (`user_id`)  REFERENCES `users` (`Id`),
  FOREIGN KEY (`post_id`)  REFERENCES `posts` (`Id`)
);

CREATE TABLE `subscriptions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `user_id` INT NOT NULL,
  `subscriber_id` INT NOT NULL,
  FOREIGN KEY (`user_id`)  REFERENCES `users` (`Id`),
  FOREIGN KEY (`subscriber_id`)  REFERENCES `users` (`Id`)
);

CREATE TABLE `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `date_add` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `content` TEXT NOT NULL,
    `message_sender` INT NOT NULL,
    `message_recipient` INT NOT NULL,
    FOREIGN KEY (`message_sender`)  REFERENCES `users` (`Id`),
    FOREIGN KEY (`message_recipient`)  REFERENCES `users` (`Id`)
);

CREATE TABLE `hashtags` (
    `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `title` VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE `post_hashtag` (
    `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `post_id` INT NOT NULL,
    `hashtag_id` INT NOT NULL,
    FOREIGN KEY (`post_id`)  REFERENCES `posts` (`Id`),
    FOREIGN KEY (`hashtag_id`)  REFERENCES `hashtags` (`Id`)
);

CREATE INDEX `c_login_email` ON `users` (`login`,`email`);
CREATE INDEX `c_post_user`   ON `likes` (`post_id`,`user_id`);
CREATE INDEX `c_title`   ON `posts`    (`title`);
CREATE INDEX `c_hashtag` ON `hashtags` (`title`);
