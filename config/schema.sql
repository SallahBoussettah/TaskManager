-- Suppression des tables si elles existent déjà
DROP TABLE IF EXISTS taches;
DROP TABLE IF EXISTS projets;
DROP TABLE IF EXISTS utilisateurs;

-- Création de la table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table des projets
CREATE TABLE projets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    utilisateur_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table des tâches
CREATE TABLE taches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    statut ENUM('a_faire', 'en_cours', 'termine') DEFAULT 'a_faire',
    projet_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (projet_id) REFERENCES projets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de données de test (optionnel)
-- Utilisateur de test (mot de passe: test123)
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES
('test', 'test', 'test@example.com', '$2y$10$GmwGQBXdHXrUlr.MWOQOAu2Lp0jJoRDYPLcLAjNwbvXTxPvmAXlHy');

-- Projets de test
INSERT INTO projets (titre, description, utilisateur_id) VALUES
('Refonte du site web', 'Projet de refonte complète du site web de l\'entreprise', 1),
('Application mobile', 'Développement d\'une application mobile pour les clients', 1);

-- Tâches de test
INSERT INTO taches (titre, description, statut, projet_id) VALUES
('Maquettes', 'Créer les maquettes des pages principales', 'a_faire', 1),
('Base de données', 'Concevoir la structure de la base de données', 'en_cours', 1),
('Intégration HTML', 'Intégrer les maquettes en HTML/CSS', 'termine', 1),
('Spécifications', 'Rédiger les spécifications fonctionnelles', 'a_faire', 2),
('Prototype', 'Développer un prototype fonctionnel', 'en_cours', 2); 