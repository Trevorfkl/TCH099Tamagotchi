<?php

include_once("./model/Idable.interface.php");

class MilestoneRepository
{
    public static function findAllByIds(array $projectIds) : array {
        if (empty($projectIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
        $sql = "SELECT * FROM milestone WHERE project_id IN ($placeholders)";
        $stmt = Database::getConnection()->prepare($sql);
        foreach ($projectIds as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $milestoneData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(fn($data) => new Milestone(
            $data['id'],
            $data['project_id'],
            $data['name'],
            $data['description'],
            $data['due_date']
        ), $milestoneData);
    }
}

?>