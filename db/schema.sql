-- ====================================================================
-- Schéma de base de données pour l'application Growchi
-- Un suivi académique des tâches avec un jardin virtuel
-- ====================================================================

CREATE DATABASE IF NOT EXISTS growchi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE growchi;


-- Table 1: Utilisateur
CREATE TABLE Utilisateur (
  id INT PRIMARY KEY AUTO_INCREMENT,
  prenom VARCHAR(50) NOT NULL,
  nom VARCHAR(50) NOT NULL,
  courriel VARCHAR(100) UNIQUE NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  role ENUM('utilisateur', 'admin') DEFAULT 'utilisateur',
  coins INT DEFAULT 0,
  date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
  couleur_profil VARCHAR(7) DEFAULT '#5a8a6a',
  biographie TEXT,
  

  -- Équipements globaux
  icone_profil VARCHAR(255) DEFAULT 'default_avatar.png',
  theme_equipe VARCHAR(255) DEFAULT 'default_theme.css',
  plante_equipee VARCHAR(255) DEFAULT 'default_plant.png',
  
  -- NOUVEAU : Préférences par type de tâche
  pref_etude VARCHAR(255) DEFAULT '🌱',
  pref_devoir VARCHAR(255) DEFAULT '🪴',
  pref_tp VARCHAR(255) DEFAULT '🪴',
  pref_examen VARCHAR(255) DEFAULT '🌸',
  pref_projet VARCHAR(255) DEFAULT '🛠️'
);

-- Table 2: Plante
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
CREATE TABLE Tache (
  id INT PRIMARY KEY AUTO_INCREMENT,
  titre VARCHAR(150) NOT NULL,
  date_limite DATE,
  statut ENUM('en_attente', 'en_cours', 'completee') DEFAULT 'en_attente',
  date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
  description TEXT NULL,
  type ENUM('etude', 'travail', 'sante', 'personnel', 'projet', 'tp', 'devoir', 'examen') DEFAULT 'etude',
  id_utilisateur INT NOT NULL,
  id_case_jardin INT,
  
  -- NOUVEAU : Icône personnalisée pour la tâche
  icone VARCHAR(255) DEFAULT '🌱',
  
  FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id) ON DELETE CASCADE,
  FOREIGN KEY (id_case_jardin) REFERENCES CaseJardin(id) ON DELETE SET NULL
);

-- Table 5: Amis
CREATE TABLE Amis (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_utilisateur1 INT NOT NULL,
  id_utilisateur2 INT NOT NULL,
  statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
  date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_utilisateur1) REFERENCES Utilisateur(id) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur2) REFERENCES Utilisateur(id) ON DELETE CASCADE
);

-- Table 6: ArticleBoutique
CREATE TABLE ArticleBoutique (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    type ENUM('theme', 'plante', 'icone_profil') NOT NULL,
    prix_coins INT DEFAULT 0,
    condition_taches_completees INT DEFAULT 0, 
    condition_serie_jours INT DEFAULT 0,
    image_url VARCHAR(255) DEFAULT 'default_item.png',
    
    -- LA FAMEUSE COLONNE À AJOUTER :
    est_visible BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 7: InventaireUtilisateur (Qui possède quoi)
CREATE TABLE InventaireUtilisateur (
    id_utilisateur INT,
    id_article INT,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_utilisateur, id_article),
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (id_article) REFERENCES ArticleBoutique(id) ON DELETE CASCADE
);

-- ====================================================================
-- DONNÉES D'EXEMPLE
-- ====================================================================

-- Insertion de plantes (correspond aux plantes Android)
INSERT INTO Plante (nom, description, cout_coins, image_stage1, image_stage2, image_stage3) VALUES
('Plante de base', 'La plante de départ, gratuite pour tous', 0, 'plant1_stage1.png', 'plant1_stage2.png', 'plant1_stage3.png'),
('Tournesol', 'Un joyeux tournesol qui suit le soleil', 75, 'sunflower_stage1.png', 'sunflower_stage2.png', 'sunflower_stage3.png'),
('Cerisier', 'Un magnifique cerisier en fleurs', 100, 'cherry_stage1.png', 'cherry_stage2.png', 'cherry_stage3.png'),
('Champignon', 'Un champignon mystérieux et résistant', 150, 'mushroom_stage1.png', 'mushroom_stage2.png', 'mushroom_stage3.png');

-- ====================================================================
-- FIN DU SCHÉMA
-- ====================================================================

-- ====================================================================
-- DONNÉES DE DÉMONSTRATION POUR LA PRÉSENTATION
-- ====================================================================

-- 1. Création des comptes (Admin + Étudiants) avec mots de passe fonctionnels
INSERT INTO Utilisateur (id, prenom, nom, courriel, mot_de_passe, role, coins, couleur_profil, biographie) VALUES
(101, 'Alice', 'Tremblay', 'alice@ets.ca', 'LE_HASH_DE_123456', 'utilisateur', 150, '#B07BAC', 'Toujours prête pour les TP de programmation!'),
(102, 'Karim', 'Haddad', 'karim@ets.ca', 'LE_HASH_DE_123456', 'utilisateur', 45, '#4F7CAC', 'En route vers le baccalauréat en génie logiciel.'),
(103, 'Clara', 'Lefebvre', 'clara@ets.ca', 'LE_HASH_DE_123456', 'utilisateur', 320, '#D98E73', 'J''adore travailler sur des microcontrôleurs et Raspberry Pi!'),
(104, 'David', 'Bouchard', 'david@ets.ca', 'LE_HASH_DE_123456', 'utilisateur', 0, '#4a8a55', 'Un peu en retard sur les révisions de physique...'),
(105, 'Eva', 'Pelletier', 'eva@ets.ca', 'LE_HASH_DE_123456', 'utilisateur', 210, '#B07BAC', 'Objectif : 100% dans tous mes examens.');

-- 2. Création de tâches réalistes pour alimenter les statistiques et le Leaderboard
INSERT INTO Tache (titre, statut, type, icone, id_utilisateur, date_limite) VALUES
-- Tâches pour Alice
('Étudier la loi de Malus et Fresnel', 'completee', 'etude', '🌱', 101, '2026-04-10'),
('TP Programmation Orientée Objet', 'completee', 'tp', '🪴', 101, '2026-04-12'),
('Devoir de Mathématiques', 'completee', 'devoir', '🪴', 101, '2026-04-15'),
('Préparation Intra TCH099', 'en_attente', 'examen', '🌸', 101, '2026-04-25'),

-- Tâches pour Karim
('Lecture des notes de cours', 'completee', 'etude', '🌱', 102, '2026-04-14'),
('Exercices d''électromagnétisme', 'en_attente', 'devoir', '🪴', 102, '2026-04-20'),

-- Tâches pour Clara (Elle sera #1 du classement)
('Projet intégrateur Growchi', 'completee', 'projet', '🛠️', 103, '2026-04-05'),
('Examen final de Physique', 'completee', 'examen', '🌸', 103, '2026-04-08'),
('TP Base de données (SQL)', 'completee', 'tp', '🪴', 103, '2026-04-10'),
('Devoir d''anglais', 'completee', 'devoir', '🪴', 103, '2026-04-15'),
('Étude pour le quiz sur les circuits', 'completee', 'etude', '🌱', 103, '2026-04-18'),

-- Tâches pour David (Dernier du classement)
('Remise du TP1', 'en_attente', 'tp', '🪴', 104, '2026-04-22'),
('Étudier l''architecture MVC', 'en_attente', 'etude', '🌱', 104, '2026-04-23'),

-- Tâches pour Eva
('Labo d''électronique I2C', 'completee', 'tp', '🪴', 105, '2026-04-11'),
('Devoir de conception Figma', 'completee', 'devoir', '🪴', 105, '2026-04-16'),
('Préparation présentation finale', 'en_attente', 'projet', '🛠️', 105, '2026-04-24');