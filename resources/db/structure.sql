SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP DATABASE IF EXISTS `phalcon-skeletons`;
CREATE SCHEMA IF NOT EXISTS `phalcon-skeletons` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `phalcon-skeletons`;

-- -----------------------------------------------------
-- Table `phalcon-skeletons`.`users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `phalcon-skeletons`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(100) NOT NULL default '',
  `nickname` VARCHAR(200) NOT NULL default '',
  `first_name` VARCHAR(200) NOT NULL default '',
  `last_name` VARCHAR(200) NOT NULL default '',
  `password` VARCHAR(40) NOT NULL default '',
  `banned` TINYINT(1) NOT NULL default 0,
  `deleted` TINYINT(1) NOT NULL default 0,
  `created` TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) ,
  UNIQUE KEY (`email`)
) ENGINE = InnoDB DEFAULT CHARSET utf8;

INSERT INTO `phalcon-skeletons`.`users` SET email='admin@cod3r.net', nickname='admin', first_name='Admin',last_name='Adminski';
INSERT INTO `phalcon-skeletons`.`users` SET email='jdoe@cod3r.net', nickname='jdoe', first_name='John', last_name='Doe';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;