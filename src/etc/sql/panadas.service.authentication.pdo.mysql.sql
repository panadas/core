CREATE TABLE `authentication` (
    `token` VARCHAR(40) COLLATE utf8_unicode_ci NOT NULL,
    `user_id` INTEGER UNSIGNED NOT NULL,
    `lifetime` INTEGER UNSIGNED,
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`token`),
    KEY `idx_modified` (`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
