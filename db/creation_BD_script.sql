-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `mydb` ;

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Users` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Users` (
  `userId` INT NOT NULL AUTO_INCREMENT,
  `firstName` VARCHAR(45) NOT NULL,
  `lastName` VARCHAR(45) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `hashedPassword` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL UNIQUE,
  PRIMARY KEY (`userId`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Semesters`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Semesters` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Semesters` (
  `semesterId` INT NOT NULL AUTO_INCREMENT,
  `userId` INT NOT NULL,
  `semesterSeason` VARCHAR(10) NOT NULL CHECK (semesterSeason IN ('AUTOMNE', 'HIVER', 'ÉTÉ')),
  `semesterYear` YEAR NOT NULL,
  `semesterStartDate` DATE NOT NULL,
  `semesterEndDate` DATE NOT NULL,
  `createdByTeacher` TINYINT NULL,
  PRIMARY KEY (`semesterId`),
  CONSTRAINT `fk_userSemesters`
    FOREIGN KEY (`userId`)
    REFERENCES `mydb`.`Users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Classes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Classes` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Classes` (
  `classId` INT NOT NULL AUTO_INCREMENT,
  `semesterId` INT NOT NULL,
  `className` VARCHAR(45) NOT NULL,
  `classCode` VARCHAR(10) NULL DEFAULT "NOT SET",
  `classGroup` INT NULL,
  `classSeminarLocation` VARCHAR(10) NOT NULL DEFAULT 'NOT SET',
  `classLabLocation` VARCHAR(10) NOT NULL DEFAULT 'NOT SET',
  `classStatus` VARCHAR(10) NULL CHECK (classStatus IN ('EN COURS', 'COMPLETÉ', 'ANNULÉ')),
  PRIMARY KEY (`classId`),
  CONSTRAINT `fk_semesterClasses`
    FOREIGN KEY (`semesterId`)
    REFERENCES `mydb`.`Semesters` (`semesterId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Plants`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Plants` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Plants` (
  `plantId` INT NOT NULL AUTO_INCREMENT,
  `createdByUserId` INT NULL,
  `plantName` VARCHAR(45) NOT NULL UNIQUE,
  PRIMARY KEY (`plantId`),
  CONSTRAINT `fk_plantsCreatedByUser`
    FOREIGN KEY (`createdByUserId`)
    REFERENCES `mydb`.`Users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Projects` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Projects` (
  `projectId` INT NOT NULL AUTO_INCREMENT,
  `plantId` INT NOT NULL,
  `classId` INT NULL,
  `projectName` VARCHAR(45) NOT NULL,
  `creationDate` DATE NOT NULL,
  `projectStatus` VARCHAR(45) NOT NULL CHECK (projectStatus IN ('EN COURS', 'COMPLETÉ', 'ANNULÉ')),
  `dueDateTime` DATETIME NOT NULL,
  `currentMilestoneZ` INT NULL,
  PRIMARY KEY (`projectId`),
  CONSTRAINT `fk_classProjects`
    FOREIGN KEY (`classId`)
    REFERENCES `mydb`.`Classes` (`classId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plantProjects`
    FOREIGN KEY (`plantId`)
    REFERENCES `mydb`.`Plants` (`plantId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `mydb`.`PlantStages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`PlantStages` ;

CREATE TABLE IF NOT EXISTS `mydb`.`PlantStages` (
  `plantStageId` INT NOT NULL AUTO_INCREMENT,
  `plantId` INT NOT NULL,
  `plantStageZ` INT NOT NULL,
  `image` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`plantStageId`),
  CONSTRAINT `fk_plantPlantStages`
    FOREIGN KEY (`plantId`)
    REFERENCES `mydb`.`Plants` (`plantId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Milestones`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Milestones` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Milestones` (
  `milestoneId` INT NOT NULL AUTO_INCREMENT,
  `projectId` INT NOT NULL,
  `milestoneName` VARCHAR(45) NOT NULL,
  `description` VARCHAR(200) NULL,
  `MilestoneZ` INT NOT NULL,
  PRIMARY KEY (`milestoneId`),
  CONSTRAINT `fk_projectMilestones`
    FOREIGN KEY (`projectId`)
    REFERENCES `mydb`.`Projects` (`projectId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
