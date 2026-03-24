<?php

class Validators {
    public static function validatePlant(Plant $plant): void
    {
        if (empty($plant->getName())) {
            throw new Exception("Le nom de la plante ne peut pas être vide.");
        }
        if (count($plant->getPlantStages()) === 0) {
            throw new Exception("La plante doit avoir au moins une étape.");
        }
    }

    public static function validatePlantStage(PlantStage $plantStage): void
    {
        if (empty($plantStage->getImage())) {
            throw new Exception("L'image de l'étape de la plante ne peut pas être vide.");
        }
        if ($plantStage->getZ() < 0) {
            throw new Exception("La valeur Z de l'étape de la plante doit être positive.");
        }
    }

    public static function validateCourse(Course $course): void
    {
        if (empty($course->getName())) {
            throw new Exception("Le nom du cours ne peut pas être vide.");
        }
        if (empty($course->getCode())) {
            throw new Exception("Le code du cours ne peut pas être vide.");
        }
     }

    public static function validateSemester(Semester $semester): void
    {
        if (empty($semester->getStartDate()) || empty($semester->getEndDate())) {
            throw new Exception("Les dates de début et de fin du semestre ne peuvent pas être vides.");
        }
        if ($semester->getStartDate() > $semester->getEndDate()) {
            throw new Exception("La date de début du semestre doit être antérieure à la date de fin.");
        }
    }

    public static function validateProject(Project $project): void
    {
        if (empty($project->getName())) {
            throw new Exception("Le nom du projet ne peut pas être vide.");
        }
        if (empty($project->getDueDate())) {
            throw new Exception("La date de remise du projet ne peut pas être vide.");
        }
        // TODO: valider le format de la date
        if ($project->getDueDate() < date('Y-m-d')) {
            throw new Exception("La date de remise du projet doit être dans le futur.");
        }
        if (empty($project->getStatus())) {
            throw new Exception("Le statut du projet ne peut pas être vide.");
        }
        if (!in_array($project->getStatus(), ['not started', 'in progress', 'completed'])) {
            throw new Exception("Le statut du projet doit être 'not started', 'in progress' ou 'completed'.");
        }
         if (count($project->getMilestones()) === 0) {
            throw new Exception("Le projet doit avoir au moins un jalon.");
        }
    }

    public static function validateMilestone(Milestone $milestone): void
    {
        if (empty($milestone->getProjectId())) {
            throw new Exception("L'ID du projet ne peut pas être vide.");
        }
        if (empty($milestone->getName())) {
            throw new Exception("Le nom du jalon ne peut pas être vide.");
        }
        if (empty($milestone->getZ())) {
            throw new Exception("La valeur Z du jalon ne peut pas être vide.");
        }
    }

    
}