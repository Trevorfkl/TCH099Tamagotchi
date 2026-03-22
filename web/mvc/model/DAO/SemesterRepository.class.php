<?php

class SemesterRepository {


    public static function findAllSemestersByUserId(int $userId): array 
    {
        return $semesters = SemesterDAO::findAllById($userId);
    }


    public static function findFullSemester(int $semesterId) : Semester 
    {
        $semester = SemesterDAO::findById($semesterId);

        // Batch tous les cours, crée un mapping id => course
        $courses = CourseDAO::findAllById($semesterId);
        $courseIds = self::map_ids($courses);
        
        // batch tous les projets, crée un mapping id => project
        $projects = ProjectDAO::findAllByIds($courseIds);
        $projectIds = self::map_ids($projects);

        // cree un mapping entre projet et plante
        $plantIds = array_map(fn($p) => $p->getPlantId(), $projects);
        $plants = PlantDAO::findAllByIds($plantIds);

        $plantStages = MilestoneDAO::findAllByIds($plantIds);

        // batch tous les milestones
        $milestones = MilestoneDAO::findAllByIds($projectIds);
        
        $semester->setCourses($courses);

        foreach ($plants as $plant) {
            assert($plant instanceof Plant);
            $plantPlantStages = array_filter($plantStages, fn($ps) => $ps->getPlantId() === $plant->getId());
            $plant->setPlantStages($plantPlantStages);
        }

        foreach ($courses as $course) {
            assert($course instanceof Course);
            $courseProjects = array_filter($projects, fn($project) => $project->getCourseId() === $course->getId());
            $course->setProjects($courseProjects);
        }

        foreach ($projects as $project) {
            assert($project instanceof Project);
            $projectPlant = array_filter($plants, fn($plant) => $plant->getId() === $project->getPlantId())[0] ?? null;
            $project->setPlant($projectPlant);

            $projectMilestones = array_filter($milestones, fn($milestone) => $milestone->getProjectId() === $project->getId());
            $project->setMilestones($projectMilestones);
        }

        return $semester;
    }

    /**
     * Maps an array of idable objects to an array of their IDs.
     *
     * @param Idable[] array $idableArray The array of idable objects.
     * @return array The array of IDs.
     */
    private static function map_ids(array $idableArray) : array {
        
        return array_map(fn($i) => $i->getId(), $idableArray);
    }
}

?>