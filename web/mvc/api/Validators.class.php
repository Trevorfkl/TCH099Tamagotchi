<?php

class Validators {


    public static function validateOrderKeys(array $firstArray, ?array $secondArray = null, ?callable $getter = null): void
    {
        // TODO: redo this, very annoying to use.
        if ($getter === null) {
            $getter = fn(int $key) => $key;
        }
        $firstArrayKeys = array_map(fn($item) => $getter($item), $firstArray);
        $secondArrayKeys = [];

        if ($secondArray !== null) {
            $secondArrayKeys = array_map(fn($item) => $getter($item), $secondArray);
        }
        $mergedKeys = [...array_values($firstArrayKeys), ...array_values($secondArrayKeys)];
        sort($mergedKeys);
        for ($i = 0; $i < count($mergedKeys); $i++) {
            if ($mergedKeys[$i] !== $i) {
                throw new Exception("Sequence de clé d'ordre invalide.");
            }
        }
    }

    public static function validatePlant(Plant $plant): void
    {
        if (empty($plant->getName())) {
            throw new Exception("Nom de plante est vide.");
        }
        if (count($plant->getPlantStages()) === 0) {
            throw new Exception("");
        }
        return true;
    }

    public static function validatePlantStage(PlantStage $plantStage): void
    {
        if (empty($plantStage->getImage())) {
            return false;
        }
        if ($plantStage->getZ() < 0) {
            return false;
        }
        return true;
    }
    

    public static function validateCourse(Course $course): void
    {
        if (empty($course->getName())) {
            return false;
        }
        if (empty($course->getCode())) {
            return false;
        }
        return true;
    }

    public static function validateSemester(Semester $semester): void
    {
        if (empty($semester->getStartDate()) || empty($semester->getEndDate())) {
            return false;
        }
        if ($semester->getStartDate() > $semester->getEndDate()) {
            return false;
        }
        return true;
    }

    public static function validateProject(Project $project): void
    {
        if (empty($project->getName())) {
            return false;
        }
        if (empty($project->getDueDateTime())) {
            return false;
        }
        // TODO: valider le format de la date
        if ($project->getDueDateTime() < date('Y-m-d H:i:s')) {
            return false;
        }
        if (empty($project->getStatus())) {
            return false;
        }
        if (!in_array($project->getStatus(), ['not started', 'in progress', 'completed'])) {
            return false;
        }
         if (count($project->getMilestones()) === 0) {
            return false;
        }
        return true;
    }

    public static function validateMilestone(Milestone $milestone): void
    {
        // Milestones existe sans projet avant que le projet soit sauvegardé a 
        // la base de données, donc on peut pas valider la presence d'un projectId
        
        // if (empty($milestone->getProjectId())) {
        //     return false;
        // }
        if (empty($milestone->getName())) {
            return false;
        }
        if (empty($milestone->getZ())) {
            return false;
        }
        return true;
    }

    public static function validateUser(User $user): void
    {
        if (empty($user->getFirstName())) {
            return false;
        }
        if (empty($user->getLastName())) {
            return false;
        }
        if (empty($user->getEmail())) {
            return false;
        }
        if (empty($user->getHashedPassword())) {
            return false;
        }
        //TODO: valider le role 
        return true;
    }
}