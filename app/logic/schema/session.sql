/**
 * 会话表结构定义放在本文件中
 */

/**
 * MySQL 数据库
 */
DROP EVENT IF EXISTS `user_session_cleaner`;
DROP TABLE IF EXISTS `user_session`;
CREATE TABLE `user_session` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sid` char(40) NOT NULL DEFAULT '',
  `expire_time` int(11) NOT NULL DEFAULT '0',
  `last_active` int(11) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL DEFAULT '0',
  `role_secret` char(40) NOT NULL DEFAULT '',
  `role_vars` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid` (`sid`),
  KEY `role_id` (`role_id`),
  KEY `expire_time` (`expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DELIMITER ;;
CREATE EVENT `user_session_cleaner` ON SCHEDULE EVERY 1 HOUR DO BEGIN
  DELETE FROM `user_session` WHERE `expire_time` < UNIX_TIMESTAMP();
END;
;;
DELIMITER ;

/**
 * SQLite 数据库
 */
DROP TABLE IF EXISTS `user_session`;
CREATE TABLE `user_session` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `sid` TEXT NOT NULL DEFAULT '',
  `expire_time` INTEGER NOT NULL DEFAULT 0,
  `last_active` INTEGER NOT NULL DEFAULT 0,
  `role_id` INTEGER NOT NULL DEFAULT 0,
  `role_secret` TEXT NOT NULL DEFAULT '',
  `role_vars` TEXT NOT NULL DEFAULT '',
  UNIQUE (`sid`)
);
CREATE INDEX `role_id` ON `user_session` (`role_id`);
CREATE INDEX `expire_time` ON `user_session` (`expire_time`);
