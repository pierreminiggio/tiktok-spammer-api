Migration :
```sql
CREATE TABLE `tiktok_spammer`.`author` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `tiktok_spammer`.`video` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `link` TEXT NOT NULL,
    `author_id` INT NOT NULL,
    `tiktok_id` VARCHAR(255) NOT NULL,
    `caption` VARCHAR(100) NOT NULL,
    `like_count` INT NOT NULL,
    `comment_count` INT NOT NULL,
    `share_count` INT NOT NULL,
    `expected_comment_locale` VARCHAR(5) NULL,
    `comment` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `tiktok_spammer`.`comment` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `video_id` INT NOT NULL,
    `author_id` INT NOT NULL,
    `content` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `tiktok_spammer`.`random_comment` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `content` TEXT NOT NULL,
    `locales` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
ALTER TABLE `random_comment` CHANGE `content` `content` VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
```
