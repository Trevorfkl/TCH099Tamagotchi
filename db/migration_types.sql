-- Migration: Mise à jour des ENUMs de la table Tache
-- À exécuter UNE SEULE FOIS dans phpMyAdmin ou MySQL Workbench

-- 1) Ajouter les nouveaux types web (tp, devoir, examen) à la colonne type
ALTER TABLE Tache 
  MODIFY COLUMN type ENUM('etude', 'travail', 'sante', 'personnel', 'projet', 'tp', 'devoir', 'examen') 
  DEFAULT 'etude';

-- 2) Ajouter le statut en_cours utilisé par le mobile
ALTER TABLE Tache 
  MODIFY COLUMN statut ENUM('en_attente', 'en_cours', 'completee') 
  DEFAULT 'en_attente';
