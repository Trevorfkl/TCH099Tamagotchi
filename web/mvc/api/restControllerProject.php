<?php

class RestControllerProject
{
    private $requestMethod;
    private $projectId;

    public function __construct($requestMethod, $projectId)
    {
        $this->requestMethod = $requestMethod;
        $this->projectId = $projectId;
    }

    private function responseJson($statusCode, $data)
    {
        return [
            "status_code_header" => "HTTP/1.1 $statusCode" . $this->getStatusMessage($statusCode),
            "body" => json_encode($data)
        ];
        
    }

    // Réponse 404 : Ressource non trouvée
    private function notFoundResponse() {
        return $this->responseJson(404, ["message" => "Resource not found"]);
    }

    // Réponse 422 : Données invalides
    private function unprocessableEntityResponse() {
        return $this->responseJson(422, ["message" => "Invalid input"]);
    }

    // Réponse 500 : Erreur serveur
    private function serverErrorResponse() {
        return $this->responseJson(500, ["message" => "Internal server error"]);
    }

    // Correspondance des codes d'état HTTP avec leurs messages
    private function getStatusMessage($code) {
        $statusMessages = [
            200 => "OK",
            201 => "Created",
            404 => "Not Found",
            422 => "Unprocessable Entity",
            500 => "Internal Server Error"
        ];
        return $statusMessages[$code] ?? "Unknown Status";
    }

    private function createProjectFromRequest() {
        $data = file_get_contents('php://input');
        $json = json_decode($data, true);

        Transaction::run(function() use ($json) {
            try {
                $plantId = $json['plantId'];
                $plant = PlantDAO::findById($plantId);
                if (!$plant) {
                    throw new Exception("Plant with id $plantId not found");
                }

                $project = new Project(
                    $json['id'] ?? null,
                    $json['courseId'] ?? null,
                    $json['plantId'],
                    $json['name'],
                    $json['dueDateTime'],
                    $json['status'],
                    $json['currentMilestoneIndex'],
                    null, // plant object will be set in the repository
                    [] // milestones will be set in the repository
                );
                Validators::validateProject($project);

                $projectSaveSuccess = ProjectDAO::save($project);
                if (!$projectSaveSuccess) {
                    throw new Exception("Erreur lors de la sauvegarde du projet");
                }

                $project->setPlant($plant);

                $JsonMilestones = $json["milestones"];
                foreach ($JsonMilestones as $milestoneJson) {
                    $index = 0;
                    $milestone = new Milestone(
                        $milestoneJson["id"] ?? null,
                        $project->getId(),
                        $milestoneJson["name"],
                        $index++,
                    );
                    Validators::validateMilestone($milestone);
                    $project->addMilestone($milestone);
                }
                $milestonesSaveSuccess = MilestoneDAO::saveAll($project->getMilestones());
                if (!$milestonesSaveSuccess) {
                    throw new Exception("Erreur lors de la sauvegarde des milestones");
                }
            } catch (Exception $e) {
                $err = $e->getMessage();
                throw new Exception("Projet invalide:  $err");
            }
        });
        
        if (Transaction::isSuccess()) {
            return $this->responseJson(201, $json['id']);
        }
        return $this->notFoundResponse();   
    }

    /**
     * S'attend a recevoir des Zs pour chaques milestone
     * @throws Exception
     * @return void
     */
    private function addMilestonesFromRequest() {
        $data = file_get_contents('php://input');
        $json = json_decode($data, true);

        Transaction::run(function() use ($json) {
            try {
                if (empty($json)) {
                    throw new Exception('Pas de nouveaux milestones en argument.');
                }
                $currentProject = ProjectDAO::findById($json['projectId']);
                if (!$currentProject) {
                    throw new Exception('Pas de projet associe au projectId');
                }
                $currentMilestones = MilestoneDAO::findByProjectId($json['projectId']);
                
                
                $currentMilestoneZ = $currentProject->getCurrentMilestoneIndex();

                foreach ($json as $milestoneJson) {
                    $index = 0;
                    $milestone = new Milestone(
                        $milestoneJson["id"] ?? null,
                        $milestoneJson["projectId"],
                        $milestoneJson["name"],
                        $milestoneJson["Z"],
                    );
                    Validators::validateMilestone($milestone);
                    $newMilestones[] = $milestone;
                }
                if (!empty($currentMilestones)) {
                    
                }
                while (true) {

                }

                $milestonesSaveSuccess = MilestoneDAO::saveAll($project->getMilestones());
                if (!$milestonesSaveSuccess) {
                    throw new Exception("Erreur lors de la sauvegarde des milestones");
                }
            }
        });
    }
}

?>