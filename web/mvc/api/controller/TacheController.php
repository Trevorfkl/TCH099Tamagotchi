<?php
require_once __DIR__ . '/../config.php';

class TacheController {

    public static function getTaches(): void {
        $userId = getUtilisateurId();
        $pdo    = getDB();
        $stmt   = $pdo->prepare("SELECT * FROM taches WHERE utilisateur_id = ? ORDER BY date ASC");
        $stmt->execute([$userId]);
        $taches = $stmt->fetchAll();
        foreach ($taches as &$t) {
            $t['id']        = (int)$t['id'];
            $t['completee'] = (bool)$t['completee'];
        }
        echo json_encode($taches);
    }

    public static function creerTache(): void {
        $userId = getUtilisateurId();
        $data   = json_decode(file_get_contents('php://input'), true);
        $nom    = trim($data['nom']  ?? '');
        $type   = trim($data['type'] ?? '');
        $date   = trim($data['date'] ?? '');

        if (!$nom || !$type || !$date) {
            http_response_code(422);
            echo json_encode(['erreur' => 'Tous les champs sont obligatoires']);
            return;
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare("INSERT INTO taches (utilisateur_id, nom, type, date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $nom, $type, $date]);
        $id = (int)$pdo->lastInsertId();

        http_response_code(201);
        echo json_encode(['id' => $id, 'nom' => $nom, 'type' => $type, 'date' => $date, 'completee' => false]);
    }

    public static function modifierTache(int $id): void {
        $userId = getUtilisateurId();
        $data   = json_decode(file_get_contents('php://input'), true);
        $nom    = trim($data['nom']  ?? '');
        $type   = trim($data['type'] ?? '');
        $date   = trim($data['date'] ?? '');

        if (!$nom || !$type || !$date) {
            http_response_code(422);
            echo json_encode(['erreur' => 'Tous les champs sont obligatoires']);
            return;
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id FROM taches WHERE id = ? AND utilisateur_id = ?");
        $stmt->execute([$id, $userId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['erreur' => 'Tâche introuvable']);
            return;
        }

        $pdo->prepare("UPDATE taches SET nom = ?, type = ?, date = ? WHERE id = ?")->execute([$nom, $type, $date, $id]);
        echo json_encode(['message' => 'Tâche modifiée', 'id' => $id, 'nom' => $nom, 'type' => $type, 'date' => $date]);
    }

    public static function supprimerTache(int $id): void {
        $userId = getUtilisateurId();
        $pdo    = getDB();
        $stmt   = $pdo->prepare("SELECT id FROM taches WHERE id = ? AND utilisateur_id = ?");
        $stmt->execute([$id, $userId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['erreur' => 'Tâche introuvable']);
            return;
        }
        $pdo->prepare("DELETE FROM taches WHERE id = ?")->execute([$id]);
        echo json_encode(['message' => 'Tâche supprimée']);
    }

    public static function completerTache(int $id): void {
        $userId = getUtilisateurId();
        $pdo    = getDB();
        $stmt   = $pdo->prepare("SELECT * FROM taches WHERE id = ? AND utilisateur_id = ?");
        $stmt->execute([$id, $userId]);
        $tache  = $stmt->fetch();

        if (!$tache) {
            http_response_code(404);
            echo json_encode(['erreur' => 'Tâche introuvable']);
            return;
        }

        $nouvelEtat = $tache['completee'] ? 0 : 1;
        $pdo->prepare("UPDATE taches SET completee = ? WHERE id = ?")->execute([$nouvelEtat, $id]);

        $nouveauxCoins = null;
        if ($nouvelEtat === 1) {
            $pdo->prepare("UPDATE utilisateurs SET coins = coins + 10 WHERE id = ?")->execute([$userId]);
            $stmt2 = $pdo->prepare("SELECT coins FROM utilisateurs WHERE id = ?");
            $stmt2->execute([$userId]);
            $nouveauxCoins = (int)$stmt2->fetch()['coins'];
        }

        echo json_encode(['id' => $id, 'completee' => (bool)$nouvelEtat, 'coins' => $nouveauxCoins]);
    }
}