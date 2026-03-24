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

    private function validateProject($data) {
        return !empty($data['name']) && 
            !empty($data['dueDate']) && 
            !empty($data['status']) && 
            !empty($data['plantId']) &&
            !empty($data['currentMilestoneIndex']) &&
            isset($data['price']) && is_numeric($data['price']) && $data['price'] > 0 &&
            (!isset($data['quantity']) || is_int($data['quantity']));
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
        
        if ($this->validateProject($json)) {
            $newProject = new Project(
                $json['id'] ?? null,
                $json['courseId'] ?? null,
                $json['name'],
                $json['dueDate'],
                $json['status'],
                $json['plantId'],
                $json['currentMilestoneIndex'],
                null, // plant object will be set in the repository
                [] // milestones will be set in the repository
            );
        



            $saveSuccess = ProjectDAO::save($newProject);
            if ($saveSuccess) {
                return $this->responseJson(201, $json['id']);
            }    
        }
        return $this->notFoundResponse();   
    }
}

?>