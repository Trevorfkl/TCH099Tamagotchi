-- ====================================================================
-- Schéma de base de données pour l'application Growchi
-- Un suivi académique des tâches avec un jardin virtuel
-- ====================================================================

-- Table 1: Utilisateur
-- Stocke les informations des utilisateurs et leur progression
-- Chaque utilisateur a une identité unique, un email et un mot de passe
CREATE TABLE Utilisateur (
  id INT PRIMARY KEY AUTO_INCREMENT,
  prenom VARCHAR(50) NOT NULL,
  nom VARCHAR(50) NOT NULL,
  courriel VARCHAR(100) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  coins INT DEFAULT 0,
  biographie TEXT,
  couleur_profil VARCHAR(7) DEFAULT '#6A9E6F',
  date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table 2: Plante
-- Catalogue des plantes disponibles dans le jardin virtuel
-- Chaque plante a trois stades de croissance avec des images associées
CREATE TABLE Plante (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  description TEXT,
  cout_coins INT DEFAULT 0,
  image_stage1 VARCHAR(255),
  image_stage2 VARCHAR(255),
  image_stage3 VARCHAR(255)
);

-- Table 3: CaseJardin
-- Représente chaque jour du jardin virtuel de l'utilisateur
-- Contient la plante cultivée ce jour-là et l'état de sa croissance
-- Dépend de Utilisateur et Plante
CREATE TABLE CaseJardin (
  id INT PRIMARY KEY AUTO_INCREMENT,
  numero_jour INT NOT NULL,
  stage_croissance INT DEFAULT 0,
  taches_completees INT DEFAULT 0,
  total_taches INT DEFAULT 0,
  id_utilisateur INT NOT NULL,
  id_plante INT,
  FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id) ON DELETE CASCADE,
  FOREIGN KEY (id_plante) REFERENCES Plante(id) ON DELETE SET NULL
);

-- Table 4: Tache
-- Liste des tâches académiques assignées aux utilisateurs
-- Les tâches sont liées à un utilisateur et optionnellement à une case du jardin
-- Dépend de Utilisateur et CaseJardin
CREATE TABLE Tache (
  id INT PRIMARY KEY AUTO_INCREMENT,
  titre VARCHAR(150) NOT NULL,
  date_limite DATE,
  statut ENUM('en_attente', 'completee') DEFAULT 'en_attente',
  date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_utilisateur INT NOT NULL,
  id_case_jardin INT,
  FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id) ON DELETE CASCADE,
  FOREIGN KEY (id_case_jardin) REFERENCES CaseJardin(id) ON DELETE SET NULL
);

-- Table 5: Amis
-- Gère les relations d'amitié entre utilisateurs
-- Les amis peuvent être en attente, acceptés ou refusés
-- Dépend de Utilisateur
CREATE TABLE Amis (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_utilisateur1 INT NOT NULL,
  id_utilisateur2 INT NOT NULL,
  statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
  date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_utilisateur1) REFERENCES Utilisateur(id) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur2) REFERENCES Utilisateur(id) ON DELETE CASCADE
);

-- ====================================================================
-- MODIFICATIONS DE SCHÉMA
-- ====================================================================

-- Ajouter les colonnes description et type à la table Tache
ALTER TABLE Tache
ADD COLUMN description TEXT NULL,
ADD COLUMN type ENUM('etude', 'travail', 'sante', 'personnel', 'projet') DEFAULT 'etude';

-- ====================================================================
-- DONNÉES D'EXEMPLE
-- ====================================================================

-- Insertion de 2 utilisateurs
INSERT INTO Utilisateur (prenom, nom, courriel, mot_de_passe, coins, biographie, couleur_profil) VALUES
('Sophie', 'Leclerc', 'sophie.leclerc@email.com', 'hashed_password_123', 150, 'Étudiante en informatique passionnée par les projets académiques', '#6A9E6F'),
('Marc', 'Dupont', 'marc.dupont@email.com', 'hashed_password_456', 200, 'Développeur et étudiant avec un intérêt pour la gamification', '#4A7C59');

-- Insertion de 3 plantes
INSERT INTO Plante (nom, description, cout_coins, image_stage1, image_stage2, image_stage3) VALUES
('Tomate', 'Une plante productive pour récompenser la discipline académique', 50, 'tomate_stage1.png', 'tomate_stage2.png', 'tomate_stage3.png'),
('Fleur Sauvage', 'Une fleur colorée qui symbolise la croissance personnelle', 30, 'fleur_stage1.png', 'fleur_stage2.png', 'fleur_stage3.png'),
('Cactus Résistant', 'Un cactus robuste représentant la persévérance', 75, 'cactus_stage1.png', 'cactus_stage2.png', 'cactus_stage3.png');

-- Insertion de 2 cases de jardin
INSERT INTO CaseJardin (numero_jour, stage_croissance, taches_completees, total_taches, id_utilisateur, id_plante) VALUES
(1, 2, 3, 4, 1, 1),
(1, 1, 2, 3, 2, 2);

-- Insertion de 3 tâches
INSERT INTO Tache (titre, date_limite, statut, id_utilisateur, id_case_jardin) VALUES
('Compléter le devoir de mathématiques', '2026-04-05', 'en_attente', 1, 1),
('Lire le chapitre 3 du manuel', '2026-04-03', 'completee', 1, 1),
('Préparer la présentation du projet', '2026-04-10', 'en_attente', 2, 2);

-- Insertion de 1 amitié
INSERT INTO Amis (id_utilisateur1, id_utilisateur2, statut) VALUES
(1, 2, 'accepte');

-- ====================================================================
-- FIN DU SCHÉMA
-- ====================================================================