DROP TABLE IF EXISTS `member_session`;
CREATE TABLE `member_session` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `sid` TEXT NOT NULL DEFAULT '',
  `expire_time` INTEGER NOT NULL DEFAULT 0,
  `last_active` INTEGER NOT NULL DEFAULT 0,
  `role_id` INTEGER NOT NULL DEFAULT 0,
  `role_secret` TEXT NOT NULL DEFAULT '',
  `data` TEXT NOT NULL DEFAULT '',
  UNIQUE (`sid`)
);
CREATE INDEX `member_session_role_id` ON `member_session` (`role_id`);
CREATE INDEX `member_session_expire_time` ON `member_session` (`expire_time`);



