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
  `fullname` VARCHAR(45) NOT NULL default '',
  `password` VARCHAR(40) NOT NULL default '',
  `created` TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) ,
  UNIQUE KEY (`email`)
) ENGINE = InnoDB DEFAULT CHARSET utf8;

INSERT INTO `phalcon-skeletons`.`user` SET email='admin@cod3r.net', nickname='admin', fullname='Admin', password='1234';
INSERT INTO `phalcon-skeletons`.`user` SET email='jdoe@cod3r.net', nickname='jdoe', fullname='John Doe', password='5678';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;