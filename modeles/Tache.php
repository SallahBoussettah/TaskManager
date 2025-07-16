<?php
/**
 * Modèle pour la gestion des tâches
 */
class Tache {
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
     * Crée une nouvelle tâche
     * 
     * @param string $titre Titre de la tâche
     * @param string $description Description de la tâche
     * @param string $statut Statut de la tâche
     * @param int $projet_id ID du projet associé
     * @return int|bool ID de la tâche créée ou false en cas d'échec
     */
    public function creer($titre, $description, $statut, $projet_id) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO taches (titre, description, statut, projet_id)
                VALUES (:titre, :description, :statut, :projet_id)
            ");
            
            $stmt->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':statut' => $statut,
                ':projet_id' => $projet_id
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de la tâche: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère une tâche par son ID
     * 
     * @param int $id ID de la tâche
     * @return array|bool Données de la tâche ou false en cas d'échec
     */
    public function obtenirParId($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, titre, description, statut, projet_id, date_creation, date_modification
                FROM taches
                WHERE id = :id
            ");
            
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() === 0) {
                return false;
            }
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la tâche: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère toutes les tâches d'un projet
     * 
     * @param int $projet_id ID du projet
     * @return array Liste des tâches
     */
    public function obtenirParProjet($projet_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, titre, description, statut, date_creation, date_modification
                FROM taches
                WHERE projet_id = :projet_id
                ORDER BY date_modification DESC
            ");
            
            $stmt->execute([':projet_id' => $projet_id]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des tâches: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère toutes les tâches d'un projet groupées par statut
     * 
     * @param int $projet_id ID du projet
     * @return array Tableau associatif des tâches groupées par statut
     */
    public function obtenirParProjetGroupeesParStatut($projet_id) {
        try {
            $taches = $this->obtenirParProjet($projet_id);
            
            // Initialisation du tableau de résultats avec des tableaux vides par statut
            $tachesParStatut = [
                'a_faire' => [],
                'en_cours' => [],
                'termine' => []
            ];
            
            // Groupement des tâches par statut
            foreach ($taches as $tache) {
                $tachesParStatut[$tache['statut']][] = $tache;
            }
            
            return $tachesParStatut;
        } catch (Exception $e) {
            error_log("Erreur lors du groupement des tâches: " . $e->getMessage());
            return [
                'a_faire' => [],
                'en_cours' => [],
                'termine' => []
            ];
        }
    }
    
    /**
     * Met à jour une tâche
     * 
     * @param int $id ID de la tâche
     * @param array $donnees Données à mettre à jour
     * @return bool True en cas de succès, sinon False
     */
    public function mettreAJour($id, $donnees) {
        try {
            $champs = [];
            $params = [':id' => $id];
            
            // Construction dynamique des champs à mettre à jour
            foreach ($donnees as $champ => $valeur) {
                if (in_array($champ, ['titre', 'description', 'statut'])) {
                    $champs[] = "$champ = :$champ";
                    $params[":$champ"] = $valeur;
                }
            }
            
            if (empty($champs)) {
                return false;
            }
            
            $sql = "UPDATE taches SET " . implode(', ', $champs) . " WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de la tâche: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour le statut d'une tâche
     * 
     * @param int $id ID de la tâche
     * @param string $statut Nouveau statut
     * @return bool True en cas de succès, sinon False
     */
    public function mettreAJourStatut($id, $statut) {
        try {
            $stmt = $this->pdo->prepare("UPDATE taches SET statut = :statut WHERE id = :id");
            
            return $stmt->execute([
                ':id' => $id,
                ':statut' => $statut
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime une tâche
     * 
     * @param int $id ID de la tâche
     * @return bool True en cas de succès, sinon False
     */
    public function supprimer($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM taches WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de la tâche: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifie si une tâche appartient à un projet spécifique
     * 
     * @param int $tache_id ID de la tâche
     * @param int $projet_id ID du projet
     * @return bool True si la tâche appartient au projet, sinon False
     */
    public function appartientAuProjet($tache_id, $projet_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id
                FROM taches
                WHERE id = :tache_id AND projet_id = :projet_id
            ");
            
            $stmt->execute([
                ':tache_id' => $tache_id,
                ':projet_id' => $projet_id
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification d'appartenance: " . $e->getMessage());
            return false;
        }
    }
} 