<?php
require_once __DIR__ . '/../config.php';

class AuthController {

    public static function inscription(): void {
        $data      = json_decode(file_get_contents('php://input'), true);
        $prenom    = trim($data['prenom']    ?? '');
        $nom       = trim($data['nom']       ?? '');
        $email     = trim($data['email']     ?? '');
        $mot_passe = trim($data['mot_passe'] ?? '');

        if (!$prenom || !$nom || !$email || !$mot_passe) {
            http_response_code(422);
            echo json_encode(['erreur' => 'Tous les champs sont obligatoires']);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['erreur' => 'Email invalide']);
            return;
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['erreur' => 'Cet email est déjà utilisé']);
            return;
        }

        $hash  = password_hash($mot_passe, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, nom, email, mot_passe, token) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$prenom, $nom, $email, $hash, $token]);
        $id = (int)$pdo->lastInsertId();

        http_response_code(201);
        echo json_encode([
            'token' => $token,
            'utilisateur' => [
                'id' => $id, 'prenom' => $prenom, 'nom' => $nom,
                'email' => $email, 'coins' => 0, 'couleur' => '#6B9F71',
            ]
        ]);
    }

    public static function connexion(): void {
        $data      = json_decode(file_get_contents('php://input'), true);
        $email     = trim($data['email']     ?? '');
        $mot_passe = trim($data['mot_passe'] ?? '');

        if (!$email || !$mot_passe) {
            http_response_code(422);
            echo json_encode(['erreur' => 'Email et mot de passe requis']);
            return;
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($mot_passe, $user['mot_passe'])) {
            http_response_code(401);
            echo json_encode(['erreur' => 'Email ou mot de passe incorrect']);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE utilisateurs SET token = ? WHERE id = ?")->execute([$token, $user['id']]);

        echo json_encode([
            'token' => $token,
            'utilisateur' => [
                'id' => (int)$user['id'], 'prenom' => $user['prenom'],
                'nom' => $user['nom'], 'email' => $user['email'],
                'telephone' => $user['telephone'], 'bio' => $user['bio'],
                'couleur' => $user['couleur'], 'coins' => (int)$user['coins'],
            ]
        ]);
    }
}