<?php

class SemesterRepository {


    // public static function findAllSemestersByUserId(int $userId): array 
    // {
    //     return $semesters = SemesterDAO::findAllById($userId);
    // }

    /**
     * Maps an array of idable objects to an array of their IDs.
     *
     * @param Idable[] array $idableArray The array of idable objects.
     * @return array The array of IDs.
     */
    private static function mapIds(array $idableArray) : array {
        
        return array_map(fn($i) => $i->getId(), $idableArray);
    }

    public static function findFullSemester(int $semesterId) : ?Semester 
    {
        Transaction::run(function() use ($semesterId) {
            $semester = SemesterDAO::findbyId($semesterId);

            if ($semester === null) {
                throw new Exception("Pas de semestre associe au semesterId.");
            }
            assert($semester instanceof Semester);
            Transaction::addQueryResult("semester", $semester);
            
            $courses = CourseDAO::findBySemesterId($semester->getId());
            if (empty($courses)) {
                return;
            }
            Transaction::addQueryResult("courses", $courses);

            $courseIds = self::mapIds($courses);
            $projects = ProjectDAO::findByCourseIds($courseIds);
            if (empty($projects)) {
                return;
            }
            Transaction::addQueryResult("projects", $projects);

            $projectIds = self::mapIds($projects);

            $plantIds = array_map(fn($project) => $project->getPlantId(), $projects);
            $plants = PlantDAO::findByIds($plantIds);
            Transaction::addQueryResult("plants", $plants);

            $plantStages = PlantStageDAO::findByPlantIds($plantIds);
            Transaction::addQueryResult("plantStages", $plantStages);
            
            $milestones = MilestoneDAO::findByProjectIds($projectIds);
            Transaction::addQueryResult("milestones", $milestones);
        });

        if (!Transaction::isSuccess()) {
            return null;
        }

        $transactionResults = Transaction::getResult()->getQueryResults();
        $semester = $transactionResults["semester"];
        $courses = $transactionResults["courses"] ?? [];
        $projects = $transactionResults["projects"] ?? [];
        $milestones = $transactionResults["milestones"] ?? [];
        $plants = $transactionResults["plants"] ?? [];
        $plantStages = $transactionResults["plantStages"] ?? [];
        
        $semester->setCourses($courses);
        
        foreach ($courses as $course) {
            $courseProjects = empty($projects) ? [] : array_filter($projects, fn($project) => $project->getCourseId() === $course->getId());
            $course->setProjects($courseProjects);
        }
        
        foreach ($plants as $plant) {
            assert($plant instanceof Plant);
            $plantPlantStages = array_filter($plantStages, fn($ps) => $ps->getPlantId() === $plant->getId());
            $plant->setPlantStages($plantPlantStages);
        }

        foreach ($projects as $project) {
            assert($project instanceof Project);
            $projectPlant = empty($plants) ? null : array_filter($plants, fn($plant) => $plant->getId() === $project->getPlantId())[0] ?? null;
            $project->setPlant($projectPlant);

            $projectMilestones = empty($milestones) ? [] : array_filter($milestones, fn($milestone) => $milestone->getProjectId() === $project->getId());
            $project->setMilestones($projectMilestones);
        }
        return $semester;
    }




}

?>