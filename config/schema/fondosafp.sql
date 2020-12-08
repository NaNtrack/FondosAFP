SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema fondosafp
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `fondosafp` ;
CREATE SCHEMA IF NOT EXISTS `fondosafp` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `fondosafp` ;

-- -----------------------------------------------------
-- Table `fondosafp`.`afps`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fondosafp`.`afps` ;

CREATE TABLE IF NOT EXISTS `fondosafp`.`afps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `description` VARCHAR(100) NULL,
  `api_name` VARCHAR(30) NOT NULL,
  `country` VARCHAR(3) NOT NULL DEFAULT 'CL',
  `status` INT(1) UNSIGNED NULL DEFAULT 1,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE INDEX `idx_country` ON `fondosafp`.`afps` (`country` ASC);


-- -----------------------------------------------------
-- Table `fondosafp`.`fondos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fondosafp`.`fondos` ;

CREATE TABLE IF NOT EXISTS `fondosafp`.`fondos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` CHAR(1) NOT NULL,
  `description` VARCHAR(100) NULL,
  `api_name` CHAR(1) NULL,
  `country` VARCHAR(3) NULL,
  `status` INT(1) UNSIGNED NULL DEFAULT 1,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fondosafp`.`cuotas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fondosafp`.`cuotas` ;

CREATE TABLE IF NOT EXISTS `fondosafp`.`cuotas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` DATE NOT NULL,
  `afp_id` INT UNSIGNED NOT NULL,
  `fondo_id` INT UNSIGNED NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `patrimonio` DOUBLE NULL,
  `variacion_val` DECIMAL(10,2) NULL,
  `varacion_por` DECIMAL(5,2) NULL,
  `created` DATETIME,
  `modified` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_afp_id`
    FOREIGN KEY (`afp_id`)
    REFERENCES `fondosafp`.`afps` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_fondo_id`
    FOREIGN KEY (`fondo_id`)
    REFERENCES `fondosafp`.`fondos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_afp_id_idx` ON `fondosafp`.`cuotas` (`afp_id` ASC);

CREATE INDEX `fk_fondo_id_idx` ON `fondosafp`.`cuotas` (`fondo_id` ASC);


-- -----------------------------------------------------
-- Table `fondosafp`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fondosafp`.`users` ;

CREATE TABLE IF NOT EXISTS `fondosafp`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(60) DEFAULT NULL,
  `last_name` varchar(60) DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NULL,
  `social_id` varchar(45) DEFAULT NULL,
  `social_source` varchar(45) DEFAULT NULL,
  `picture` varchar(100) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `role` VARCHAR(20) NULL DEFAULT 'usuario',
  `verified` INT(1) NOT NULL DEFAULT 0,
  `created` DATETIME,
  `modified` DATETIME,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fondosafp`.`email_queue`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fondosafp`.`email_queue` ;

CREATE TABLE IF NOT EXISTS `fondosafp`.`email_queue` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created` DATETIME NOT NULL,
  `status` VARCHAR(12) NOT NULL DEFAULT 'none',
  `sent` DATETIME NULL,
  `email_from` VARCHAR(100) NOT NULL,
  `email_to` VARCHAR(100) NOT NULL,
  `email_subject` VARCHAR(100) NOT NULL,
  `body` TEXT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `fondosafp`.`changes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fondosafp`.`changes` ;

CREATE TABLE IF NOT EXISTS `fondosafp`.`changes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `afp_id` INT UNSIGNED NOT NULL,
  `from_fondo_id` INT UNSIGNED NOT NULL,
  `to_fondo_id` INT UNSIGNED NOT NULL,
  `from_value` DECIMAL(10,4) NULL,
  `to_value` DECIMAL(10,4) NULL,
  `monto` DOUBLE NULL,
  `profits_loss` DECIMAL(10,4) NULL,
  `change_dt` DATE NULL,
  `cuota_dt` DATE NULL,
  `created` DATETIME,
  `modified` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_cha_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `fondosafp`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cha_ffid`
    FOREIGN KEY (`from_fondo_id`)
    REFERENCES `fondosafp`.`fondos` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cha_tfid`
    FOREIGN KEY (`to_fondo_id`)
    REFERENCES `fondosafp`.`fondos` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cha_afp_id`
    FOREIGN KEY (`afp_id`)
    REFERENCES `fondosafp`.`afps` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_cha_user_id_idx` ON `fondosafp`.`changes` (`user_id` ASC);

CREATE INDEX `fk_cha_ffid_idx` ON `fondosafp`.`changes` (`from_fondo_id` ASC);

CREATE INDEX `fk_cha_tfid_idx` ON `fondosafp`.`changes` (`to_fondo_id` ASC);

CREATE INDEX `fk_cha_afp_id_idx` ON `fondosafp`.`changes` (`afp_id` ASC);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
