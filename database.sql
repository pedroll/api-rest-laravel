ALTER TABLE `api-laravel`.`posts` DROP FOREIGN KEY `fk_posts_user`;
ALTER TABLE `api-laravel`.`posts` DROP FOREIGN KEY `fk_posts_categories`;

DROP TABLE `api-laravel`.`users`;
DROP TABLE `api-laravel`.`posts`;
DROP TABLE `api-laravel`.`categories`;

CREATE TABLE `api-laravel`.`users` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`surname` varchar(255) NULL,
`email` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
`role` varchar(255) NULL,
`description` text NULL,
`image` varchar(255) NULL,
`created_at` datetime NULL DEFAULT NULL,
`updated_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`remember_token` varchar(255) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `api-laravel`.`posts` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`category_id` int(11) NOT NULL,
`title` varchar(255) NOT NULL,
`content` text NOT NULL,
`image` varchar(255) NULL,
`created_at` datetime NULL DEFAULT NULL,
`updated_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`) 
);
CREATE TABLE `api-laravel`.`categories` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NULL,
`created_at` datetime NULL DEFAULT NULL,
`updated_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`) 
);

ALTER TABLE `api-laravel`.`posts` ADD CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `api-laravel`.`users` (`id`);
ALTER TABLE `api-laravel`.`posts` ADD CONSTRAINT `fk_posts_categories` FOREIGN KEY (`category_id`) REFERENCES `api-laravel`.`categories` (`id`);

