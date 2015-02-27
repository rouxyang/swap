DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `value` TEXT NOT NULL DEFAULT ''
);

INSERT INTO `setting` (`id`, `value`) VALUES (1, '论坛名称');
INSERT INTO `setting` (`id`, `value`) VALUES (2, '0');




DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL DEFAULT '',
  `pass` TEXT NOT NULL DEFAULT '',
  `salt` TEXT NOT NULL DEFAULT ''
);
CREATE INDEX `admin_name` ON `admin` (`name`);

INSERT INTO `admin` (`id`, `name`, `pass`, `salt`) VALUES (NULL, 'admin', '5a4ed32d81e1321546b10cacc19bad3af1af8c97', '784a821f90f3a83f641135067e8a4d26bfe143a8');




DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL DEFAULT '',
  `pass` TEXT NOT NULL DEFAULT '',
  `salt` TEXT NOT NULL DEFAULT '',
  `avatar` TEXT NOT NULL DEFAULT '',
  `register_time` INTEGER NOT NULL DEFAULT 0,
  `topic_count` INTEGER NOT NULL DEFAULT 0,
  `reply_count` INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX `user_name` ON `user` (`name`);

INSERT INTO `user` (`id`, `name`, `pass`, `salt`) VALUES (NULL, 'admin', '5a4ed32d81e1321546b10cacc19bad3af1af8c97', '784a821f90f3a83f641135067e8a4d26bfe143a8');
UPDATE `setting` SET `value` = `value` + 1 WHERE `id` = 2;




DROP TABLE IF EXISTS `board`;
CREATE TABLE `board` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL DEFAULT '',
  `description` TEXT NOT NULL DEFAULT '',
  `topic_count` INTEGER NOT NULL DEFAULT 0,
  `reply_count` INTEGER NOT NULL DEFAULT 0,
  `last_post_user` TEXT NOT NULL DEFAULT '',
  `last_post_time` INTEGER NOT NULL DEFAULT 0
);

INSERT INTO `board` (`id`, `name`, `description`, `topic_count`, `reply_count`, `last_post_user`, `last_post_time`) VALUES (NULL, '默认板块', '板块描述', 0, 0, '', 0);




DROP TABLE IF EXISTS `manager`;
CREATE TABLE `manager` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `board_id` INTEGER NOT NULL DEFAULT 0,
  `level` INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX `manager_board_id` ON `manager` (`board_id`);

INSERT INTO `manager` (`id`, `user_id`, `board_id`, `level`) VALUES (NULL, 1, 1, 99);




DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `board_id` INTEGER NOT NULL DEFAULT 0,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `reply_count` INTEGER NOT NULL DEFAULT 0,
  `read_count` INTEGER NOT NULL DEFAULT 0,
  `last_post_user` TEXT NOT NULL DEFAULT '',
  `last_post_time` INTEGER NOT NULL DEFAULT 0,
  `pub_time` INTEGER NOT NULL DEFAULT 0,
  `is_top` INTEGER NOT NULL DEFAULT 0,
  `title` TEXT NOT NULL DEFAULT '',
  `content` TEXT NOT NULL DEFAULT ''
);
CREATE INDEX `topic_board_id` ON `topic` (`board_id`);
CREATE INDEX `topic_user_id` ON `topic` (`user_id`);

INSERT INTO `topic` (`id`, `board_id`, `user_id`, `reply_count`, `read_count`, `last_post_user`, `last_post_time`, `pub_time`, `is_top`, `title`, `content`) VALUES (NULL, 1, 1, 0, 0, '', 0, 0, 0, '这是一个主题', '这是主题内容');
UPDATE `board` SET `topic_count` = `topic_count` + 1, `last_post_user` = 'admin', `last_post_time` = 0 WHERE `id` = 1;
UPDATE `user` SET `topic_count` = `topic_count` + 1 WHERE `id` = 1;




DROP TABLE IF EXISTS `reply`;
CREATE TABLE `reply` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `board_id` INTEGER NOT NULL DEFAULT 0,
  `topic_id` INTEGER NOT NULL DEFAULT 0,
  `user_id` INTEGER NOT NULL DEFAULT 0,
  `pub_time` INTEGER NOT NULL DEFAULT 0,
  `content` TEXT NOT NULL DEFAULT ''
);
CREATE INDEX `reply_board_id` ON `reply` (`board_id`);
CREATE INDEX `reply_topic_id` ON `reply` (`topic_id`);

INSERT INTO `reply` (`id`, `board_id`, `topic_id`, `user_id`, `pub_time`, `content`) VALUES (NULL, 1, 1, 1, 0, '这是一篇回复');
UPDATE `board` SET `reply_count` = `reply_count` + 1, `last_post_user` = 'admin', `last_post_time` = 0 WHERE `id` = 1;
UPDATE `topic` SET `reply_count` = `reply_count` + 1, `last_post_user` = 'admin', `last_post_time` = 0 WHERE `id` = 1;
UPDATE `user` SET `reply_count` = `reply_count` + 1 WHERE `id` = 1;
