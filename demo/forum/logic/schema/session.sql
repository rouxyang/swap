DROP TABLE IF EXISTS `user_session`;
CREATE TABLE `user_session` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `sid` TEXT NOT NULL DEFAULT '',
  `expire_time` INTEGER NOT NULL DEFAULT 0,
  `last_active` INTEGER NOT NULL DEFAULT 0,
  `role_id` INTEGER NOT NULL DEFAULT 0,
  `role_secret` TEXT NOT NULL DEFAULT '',
  `data` TEXT NOT NULL DEFAULT '',
  UNIQUE (`sid`)
);
CREATE INDEX `user_session_role_id` ON `user_session` (`role_id`);
CREATE INDEX `user_session_expire_time` ON `user_session` (`expire_time`);




DROP TABLE IF EXISTS `admin_session`;
CREATE TABLE `admin_session` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `sid` TEXT NOT NULL DEFAULT '',
  `expire_time` INTEGER NOT NULL DEFAULT 0,
  `last_active` INTEGER NOT NULL DEFAULT 0,
  `role_id` INTEGER NOT NULL DEFAULT 0,
  `role_secret` TEXT NOT NULL DEFAULT '',
  `data` TEXT NOT NULL DEFAULT '',
  UNIQUE (`sid`)
);
CREATE INDEX `admin_session_role_id` ON `admin_session` (`role_id`);
CREATE INDEX `admin_session_expire_time` ON `admin_session` (`expire_time`);



