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
        });
        
        if (Transaction::isSuccess()) {
            return $this->responseJson(201, $json['id']);
        }
        return $this->notFoundResponse();   
    }

    /**
     * S'attend a recevoir des Zs pour chaques milestone
     * @throws Exception
     */
    private function addMilestonesFromRequest() {
        $data = file_get_contents('php://input');
        $json = json_decode($data, true);

        Transaction::run(function() use ($json) {
            $project = ProjectDAO::findById($json['projectId']);
            if (!$project) {
                throw new Exception('Pas de projet associe au projectId');
            }
            $oldMilestones = MilestoneDAO::findByProjectId($json['projectId']);
            
            $oldMilestoneZ = $project->getCurrentMilestoneIndex();
            
            foreach ($json as $milestoneJson) {
                $milestone = new Milestone(
                    $milestoneJson["id"] ?? null,
                    $milestoneJson["projectId"],
                    $milestoneJson["name"],
                    $milestoneJson["Z"],
                );
                Validators::validateMilestone($milestone);
                $newMilestones[] = $milestone;
            }
            if (!empty($oldMilestones)) {
                $activeProjectMilestone = $oldMilestones[$oldMilestoneZ];

                $oldOldOrderKeys = array_map(fn($milestone) => $milestone->getZ(), $oldMilestones);

                Helper::updateSortableObjectAttribute(
                    $oldMilestones, $newMilestones, 
                    fn($milestone) => $milestone->getZ(), fn($milestone) => $milestone->setZ()
                    );
                
                $newOldOrderKeys = array_map(fn($milestone) => $milestone->getZ(), $oldMilestones);
                Validators::validateOrderKeys($oldMilestones, $newMilestones, fn($item) => $item->getZ());

                $project->setCurrentMilestoneIndex($activeProjectMilestone->getZ());
                ProjectDAO::update($project);
                // Trouve les indices où il y a eu des mutations.
                $indicesWhereModified = array_diff_assoc($newOldOrderKeys, $oldOldOrderKeys);
                foreach (array_keys($indicesWhereModified) as $indexWhereModified) {
                    MilestoneDAO::update($oldMilestones[$indexWhereModified]);
                }   
            } 
            Validators::validateOrderKeys($oldMilestones, $newMilestones, fn($item) => $item->getZ());
            
            MilestoneDAO::saveAll($newMilestones);
        });

        // TODO: faire quelque chose avec le resultat de la transaction 
        if (Transaction::isSuccess()) {
            // TODO: valeur de retour pour l'operation.
            return $this->responseJson(201, "PLACERHOLDER PLEASE CHANGES");
        }
        return $this->notFoundResponse(); 
    }

    private function incrementActiveMilestoneFromRequest() {
        // TODO: PUT request, je penses qu'on n'accede pas aux donnees de cette facon.
        $data = file_get_contents('php://input');
        $json = json_decode($data, true);

        Transaction::run(function() use ($json) {
            $project = ProjectDAO::findById($json['projectId']);
            if ($project === null) {
                throw new Exception('Pas de projet associe au projectId');
            }
            assert($project instanceof Project);
            
            $updatedActiveMilestoneIndex = $json['activeMilestoneIndex'];
            $currentMilestones = MilestoneDAO::findByProjectId($json['projectId']);

            if ($updatedActiveMilestoneIndex >= count($currentMilestones) || $updatedActiveMilestoneIndex < 0) {
                throw new Exception("Paramètre invalide: la clé ordonnée est out of bounds.");
            }

            $oldActiveMilestoneIndex = $project->getCurrentMilestoneIndex();
            if ($updatedActiveMilestoneIndex !== $oldActiveMilestoneIndex + 1) {
                throw new Exception("Demande d'incrémenter la clé ordonnée pour plus que 1.");
            }

            ProjectDAO::update($project);
        });

        // TODO: faire quelque chose avec le resultat de la transaction 
        if (Transaction::isSuccess()) {
            // TODO: valeur de retour pour l'operation.
            return $this->responseJson(200, $json["projectId"]);
        }
        return $this->notFoundResponse(); 
    }

    private function deleteFromRequest() {
        $data = file_get_contents('php://input');
        $json = json_decode($data, true);

        Transaction::run(function() use ($json) {
            $project = ProjectDAO::findById($json['projectId']);
            if ($project === null) {
                throw new Exception("Pas de projet associe au projectId");
            }
            ProjectDAO::delete($project);

            $milestones = MilestoneDAO::findByProjectId($json["projectId"]);
            foreach ($milestones as $milestone) {
                MilestoneDAO::delete($milestone);
            }
        });

        // TODO: faire quelque chose avec le resultat de la transaction 
        if (Transaction::isSuccess()) {
            // TODO: valeur de retour pour l'operation.
            return $this->responseJson(200, $json["projectId"]);
        }
        return $this->notFoundResponse(); 
    }    
}

?>