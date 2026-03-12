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
  `email` VARCHAR(45) NOT NULL,
  `hashedPassword` VARCHAR(60) NOT NULL,
  `role` VARCHAR(20) NOT NULL COMMENT 'CHECK IN (\'ÉTUDIANT\', \'PROFESSEUR\', \'ADMIN\')',
  PRIMARY KEY (`userId`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Semesters`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Semesters` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Semesters` (
  `semesterId` INT NOT NULL,
  `userId` INT NOT NULL,
  `semesterSeason` VARCHAR(45) NOT NULL COMMENT 'CHECK IN (\'AUTOMNE\', \'HIVER\', \'ÉTÉ\')',
  `semesterYear` YEAR NOT NULL,
  `semesterStartDate` VARCHAR(45) NULL,
  `semesterEndDate` VARCHAR(45) NULL,
  `createdByTeacher` TINYINT NOT NULL,
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
  `classSeminarLocation` VARCHAR(10) NULL DEFAULT 'NOT SET',
  `classLabLocation` VARCHAR(10) NULL DEFAULT 'NOT SET',
  `classStatus` VARCHAR(45) NULL COMMENT 'CHECK IN (\'EN COURS\', \'COMPLETÉ\', \'ANNULÉ\')',
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
  `plantId` INT NOT NULL,
  `createdByUserId` INT NULL,
  `plantName` VARCHAR(45) NOT NULL,
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
  `projectStatus` VARCHAR(45) NOT NULL COMMENT 'CHECK IN (\'EN COURS\', \'COMPLETÉ\', \'ANNULÉ\')',
  `dueDateTime` DATETIME NULL,
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
  `plantStageId` INT NOT NULL,
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
  `milestoneId` INT NOT NULL,
  `plantStageId` INT NOT NULL,
  `projectId` INT NOT NULL,
  `milestoneName` VARCHAR(45) NOT NULL,
  `description` VARCHAR(200) NULL,
  `MilestoneZ` INT NULL,
  PRIMARY KEY (`milestoneId`),
  CONSTRAINT `fk_plantStageMilestones`
    FOREIGN KEY (`plantStageId`)
    REFERENCES `mydb`.`PlantStages` (`plantStageId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_projectMilestones`
    FOREIGN KEY (`projectId`)
    REFERENCES `mydb`.`Projects` (`projectId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
