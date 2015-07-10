<?php include("connect.php"); ?>

<?php // VERSION 0.2.0
$sql = "ALTER TABLE `user` ADD `lastseen` INT NOT NULL ; ";
$sql .= "ALTER TABLE `backup` ADD `deleted` BOOLEAN NOT NULL DEFAULT FALSE ; ";
$sql .= "CREATE TABLE `char`.`albums` ( `id` INT NOT NULL AUTO_INCREMENT , `creator` INT NOT NULL , `title` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `permissions` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'group:permissions (eg 1:110) seperated by ,' , `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `path` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , INDEX (`id`) ) ENGINE = InnoDB; ";
$sql .= "ALTER TABLE `permissions` ADD `gallery_new_album` BOOLEAN NOT NULL AFTER `gallery_add`; ";
$sql .= "UPDATE `permissions` SET `gallery_new_album`=1 ;";
$sql .= "CREATE TABLE `char`.`images` ( `id` INT NOT NULL AUTO_INCREMENT , `album` INT NOT NULL , `title` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `filename` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `uploader` INT NOT NULL , `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`) ) ENGINE = InnoDB; ";
mysqli_multi_query($mysqli, $sql) or die("ERROR 999: multi query failed: $sql".mysqli_error());

// CREATE TABLE `char`.`file_upload` ( `id` INT NOT NULL AUTO_INCREMENT, `job` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `original_name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `album` INT NOT NULL , `uploader` INT NOT NULL , `path` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , PRIMARY KEY (`id`) ) ENGINE = InnoDB;
?>