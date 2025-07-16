<?php
/**
 * Modèle pour la gestion des projets
 */
class Projet {
    private $pdo;
    
    /**
     * Constructeur
     * 
     * @param PDO $pdo Instance de PDO
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Crée un nouveau projet
     * 
     * @param string $titre Titre du projet
     * @param string $description Description du projet
     * @param int $utilisateur_id ID de l'utilisateur propriétaire
     * @return int|bool ID du projet créé ou false en cas d'échec
     */
    public function creer($titre, $description, $utilisateur_id) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO projets (titre, description, utilisateur_id)
                VALUES (:titre, :description, :utilisateur_id)
            ");
            
            $stmt->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':utilisateur_id' => $utilisateur_id
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du projet: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère un projet par son ID
     * 
     * @param int $id ID du projet
     * @param int $utilisateur_id ID de l'utilisateur (pour vérification d'accès)
     * @return array|bool Données du projet ou false en cas d'échec
     */
    public function obtenirParId($id, $utilisateur_id = null) {
        try {
            $sql = "
                SELECT id, titre, description, utilisateur_id, date_creation, date_modification
                FROM projets
                WHERE id = :id
            ";
            
            // Si un utilisateur_id est fourni, on vérifie qu'il est bien le propriétaire
            if ($utilisateur_id !== null) {
                $sql .= " AND utilisateur_id = :utilisateur_id";
            }
            
            $stmt = $this->pdo->prepare($sql);
            
            $params = [':id' => $id];
            if ($utilisateur_id !== null) {
                $params[':utilisateur_id'] = $utilisateur_id;
            }
            
            $stmt->execute($params);
            
            if ($stmt->rowCount() === 0) {
                return false;
            }
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du projet: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère tous les projets d'un utilisateur
     * 
     * @param int $utilisateur_id ID de l'utilisateur
     * @return array Liste des projets
     */
    public function obtenirParUtilisateur($utilisateur_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, titre, description, date_creation, date_modification
                FROM projets
                WHERE utilisateur_id = :utilisateur_id
                ORDER BY date_modification DESC
            ");
            
            $stmt->execute([':utilisateur_id' => $utilisateur_id]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des projets: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Met à jour un projet
     * 
     * @param int $id ID du projet
     * @param array $donnees Données à mettre à jour
     * @param int $utilisateur_id ID de l'utilisateur (pour vérification d'accès)
     * @return bool True en cas de succès, sinon False
     */
    public function mettreAJour($id, $donnees, $utilisateur_id) {
        try {
            // Vérification que l'utilisateur est bien le propriétaire du projet
            $projet = $this->obtenirParId($id, $utilisateur_id);
            if (!$projet) {
                return false;
            }
            
            $champs = [];
            $params = [':id' => $id];
            
            // Construction dynamique des champs à mettre à jour
            foreach ($donnees as $champ => $valeur) {
                if (in_array($champ, ['titre', 'description'])) {
                    $champs[] = "$champ = :$champ";
                    $params[":$champ"] = $valeur;
                }
            }
            
            if (empty($champs)) {
                return false;
            }
            
            $sql = "UPDATE projets SET " . implode(', ', $champs) . " WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du projet: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un projet
     * 
     * @param int $id ID du projet
     * @param int $utilisateur_id ID de l'utilisateur (pour vérification d'accès)
     * @return bool True en cas de succès, sinon False
     */
    public function supprimer($id, $utilisateur_id) {
        try {
            // Vérification que l'utilisateur est bien le propriétaire du projet
            $projet = $this->obtenirParId($id, $utilisateur_id);
            if (!$projet) {
                return false;
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM projets WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du projet: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compte le nombre de tâches par statut pour un projet
     * 
     * @param int $projet_id ID du projet
     * @return array Tableau associatif avec le nombre de tâches par statut
     */
    public function compterTachesParStatut($projet_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT statut, COUNT(*) as nombre
                FROM taches
                WHERE projet_id = :projet_id
                GROUP BY statut
            ");
            
            $stmt->execute([':projet_id' => $projet_id]);
            
            $resultats = $stmt->fetchAll();
            
            // Initialisation du tableau de résultats avec des valeurs par défaut
            $compteurs = [
                'a_faire' => 0,
                'en_cours' => 0,
                'termine' => 0
            ];
            
            // Remplissage avec les valeurs réelles
            foreach ($resultats as $resultat) {
                $compteurs[$resultat['statut']] = (int) $resultat['nombre'];
            }
            
            return $compteurs;
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des tâches: " . $e->getMessage());
            return [
                'a_faire' => 0,
                'en_cours' => 0,
                'termine' => 0
            ];
        }
    }
} 