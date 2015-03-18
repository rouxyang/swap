DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `value` TEXT NOT NULL DEFAULT ''
);
INSERT INTO `setting` (`id`, `value`) VALUES (1, '博客名称');
INSERT INTO `setting` (`id`, `value`) VALUES (2, '博客描述。');
INSERT INTO `setting` (`id`, `value`) VALUES (3, '0');
INSERT INTO `setting` (`id`, `value`) VALUES (4, '0');
INSERT INTO `setting` (`id`, `value`) VALUES (5, '0');
INSERT INTO `setting` (`id`, `value`) VALUES (6, '0');
INSERT INTO `setting` (`id`, `value`) VALUES (7, '这是“关于”页面');
INSERT INTO `setting` (`id`, `value`) VALUES (8, 'Copyright (c) 2009-2015 Jingcheng Zhang. All rights reserved.');
INSERT INTO `setting` (`id`, `value`) VALUES (9, '六加三等于几？填阿拉伯数字');
INSERT INTO `setting` (`id`, `value`) VALUES (10, '9');
INSERT INTO `setting` (`id`, `value`) VALUES (11, 'swap, blog');




DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL DEFAULT '',
  `pass` TEXT NOT NULL DEFAULT '',
  `salt` TEXT NOT NULL DEFAULT ''
);
CREATE INDEX `member_name` ON `member` (`name`);
INSERT INTO `member` (`id`, `name`, `pass`, `salt`) VALUES (NULL, 'admin', '5a4ed32d81e1321546b10cacc19bad3af1af8c97', '784a821f90f3a83f641135067e8a4d26bfe143a8');




DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `author` TEXT NOT NULL DEFAULT '',
  `email` TEXT NOT NULL DEFAULT '',
  `site` TEXT NOT NULL DEFAULT '',
  `pub_time` INTEGER NOT NULL DEFAULT 0,
  `content` TEXT NOT NULL DEFAULT ''
);
INSERT INTO `message` (`id`, `author`, `email`, `site`, `pub_time`, `content`) VALUES (NULL, '匿名', 'anonymous@anonymous.com', 'http://www.anonymous.com', 0, '这是条留言');




DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL DEFAULT '',
  `post_count` INTEGER NOT NULL DEFAULT 0
);
INSERT INTO `category` (`id`, `name`, `post_count`) VALUES (NULL, '默认分类', 0);
INSERT INTO `category` (`id`, `name`, `post_count`) VALUES (NULL, 'swap框架', 0);
UPDATE `setting` SET `value` = `value` + 2 WHERE `id` = 4;




DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `member_id` INTEGER NOT NULL DEFAULT 0,
  `category_id` INTEGER NOT NULL DEFAULT 0,
  `pub_time` INTEGER NOT NULL DEFAULT 0,
  `title` TEXT NOT NULL DEFAULT '',
  `content` TEXT NOT NULL DEFAULT '',
  `read_count` INTEGER NOT NULL DEFAULT 0,
  `comment_count` INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX `post_member_id` ON `post` (`member_id`);
CREATE INDEX `post_category_id` ON `post` (`category_id`);
INSERT INTO `post` (`id`, `member_id`, `category_id`, `pub_time`, `title`, `content`, `read_count`, `comment_count`) VALUES (NULL, 1, 1, 0, '这是标题', '这是内容', 0, 0);
UPDATE `category` SET `post_count` = `post_count` + 1 WHERE `id` = 1;
UPDATE `setting` SET `value` = `value` + 1 WHERE `id` = 3;




DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `post_id` INTEGER NOT NULL DEFAULT 0,
  `author` TEXT NOT NULL DEFAULT '',
  `email` TEXT NOT NULL DEFAULT '',
  `site` TEXT NOT NULL DEFAULT '',
  `pub_time` INTEGER NOT NULL DEFAULT 0,
  `content` TEXT NOT NULL DEFAULT ''
);
CREATE INDEX `comment_post_id` ON `comment` (`post_id`);
INSERT INTO `comment` (`id`, `post_id`, `author`, `email`, `site`, `pub_time`, `content`) VALUES (NULL, 1, '匿名', 'anonymous@anonymous.com', 'http://www.anonymous.com', 0, '这是评论');
UPDATE `post` SET `comment_count` = `comment_count` + 1 WHERE `id` = 1;
UPDATE `setting` SET `value` = `value` + 1 WHERE `id` = 6;




DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL DEFAULT '',
  `refer_count` INTEGER NOT NULL DEFAULT '1'
);
CREATE UNIQUE INDEX `tag_name` ON `tag` (`name`);
CREATE INDEX `tag_refer_count` ON `tag` (`refer_count`);
INSERT INTO `tag` (`id`, `name`, `refer_count`) VALUES (NULL, '标签一', 1);
UPDATE `setting` SET `value` = `value` + 1 WHERE `id` = 5;




DROP TABLE IF EXISTS `post_tag`;
CREATE TABLE `post_tag` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `post_id` INTEGER NOT NULL,
  `tag_id` INTEGER NOT NULL
);
CREATE INDEX `post_tag_post_id` ON `post_tag` (`post_id`);
CREATE INDEX `post_tag_tag_id` ON `post_tag` (`tag_id`);
INSERT INTO `post_tag` (`id`, `post_id`, `tag_id`) VALUES (NULL, 1, 1);




DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL DEFAULT '',
  `url` TEXT NOT NULL DEFAULT ''
);
CREATE UNIQUE INDEX `link_name` ON `link` (`name`);
INSERT INTO `link` (`id`, `name`, `url`) VALUES (NULL, 'diogin的网站', 'http://www.diogin.com/');
INSERT INTO `link` (`id`, `name`, `url`) VALUES (NULL, 'swap框架官方网站', 'https://github.com/diogin/swap');



