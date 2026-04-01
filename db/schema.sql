-- ====================================================================
-- Schéma de base de données pour l'application Growchi
-- Un suivi académique des tâches avec un jardin virtuel
-- ====================================================================

-- Table 1: Utilisateur
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
