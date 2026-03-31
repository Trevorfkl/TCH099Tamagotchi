<?php
require_once __DIR__ . '/../config.php';

class ProfilController {

    public static function getProfil(): void {
        $userId = getUtilisateurId();
        $pdo    = getDB();
        $stmt   = $pdo->prepare("SELECT id, prenom, nom, email, telephone, bio, couleur, coins FROM utilisateurs WHERE id = ?");
        $stmt->execute([$userId]);
        $user   = $stmt->fetch();

        if (!$user) {
            http_response_code(404);
            echo json_encode(['erreur' => 'Utilisateur introuvable']);
            return;
        }

        $stmt2      = $pdo->prepare("SELECT COUNT(*) as total, SUM(completee) as completees FROM taches WHERE utilisateur_id = ?");
        $stmt2->execute([$userId]);
        $stats      = $stmt2->fetch();
        $total      = (int)$stats['total'];
        $completees = (int)$stats['completees'];

        echo json_encode([
            'id'         => (int)$user['id'],
            'prenom'     => $user['prenom'],
            'nom'        => $user['nom'],
            'email'      => $user['email'],
            'telephone'  => $user['telephone'],
            'bio'        => $user['bio'],
            'couleur'    => $user['couleur'],
            'coins'      => (int)$user['coins'],
            'stats' => [
                'total'       => $total,
                'completees'  => $completees,
                'progression' => $total > 0 ? round(($completees / $total) * 100) : 0,
            ]
        ]);
    }

    public static function updateProfil(): void {
        $userId    = getUtilisateurId();
        $data      = json_decode(file_get_contents('php://input'), true);
        $prenom    = trim($data['prenom']    ?? '');
        $nom       = trim($data['nom']       ?? '');
        $telephone = trim($data['telephone'] ?? '');
        $bio       = trim($data['bio']       ?? '');
        $couleur   = trim($data['couleur']   ?? '#6B9F71');

        if (!$prenom || !$nom) {
            http_response_code(422);
            echo json_encode(['erreur' => 'Prénom et nom sont obligatoires']);
            return;
        }

        $pdo = getDB();
        $pdo->prepare("UPDATE utilisateurs SET prenom=?, nom=?, telephone=?, bio=?, couleur=? WHERE id=?")
            ->execute([$prenom, $nom, $telephone, $bio, $couleur, $userId]);

        echo json_encode(['message' => 'Profil mis à jour']);
    }
}