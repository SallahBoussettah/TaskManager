<?php
/**
 * Modèle pour la gestion des utilisateurs
 */
class Utilisateur {
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
     * Crée un nouvel utilisateur
     * 
     * @param string $nom Nom de l'utilisateur
     * @param string $prenom Prénom de l'utilisateur
     * @param string $email Email de l'utilisateur
     * @param string $mot_de_passe Mot de passe de l'utilisateur (non hashé)
     * @return int|bool ID de l'utilisateur créé ou false en cas d'échec
     */
    public function creer($nom, $prenom, $email, $mot_de_passe) {
        try {
            // Vérification si l'email existe déjà
            if ($this->existeParEmail($email)) {
                return false;
            }
            
            // Hashage du mot de passe
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            
            // Préparation de la requête
            $stmt = $this->pdo->prepare("
                INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe)
                VALUES (:nom, :prenom, :email, :mot_de_passe)
            ");
            
            // Exécution de la requête
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':mot_de_passe' => $mot_de_passe_hash
            ]);
            
            // Retour de l'ID de l'utilisateur créé
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifie si un utilisateur existe par son email
     * 
     * @param string $email Email à vérifier
     * @return bool True si l'email existe, sinon False
     */
    public function existeParEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
            $stmt->execute([':email' => $email]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de l'email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Authentifie un utilisateur
     * 
     * @param string $email Email de l'utilisateur
     * @param string $mot_de_passe Mot de passe de l'utilisateur
     * @return array|bool Données de l'utilisateur ou false en cas d'échec
     */
    public function authentifier($email, $mot_de_passe) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, nom, prenom, email, mot_de_passe
                FROM utilisateurs
                WHERE email = :email
            ");
            
            $stmt->execute([':email' => $email]);
            
            if ($stmt->rowCount() === 0) {
                return false;
            }
            
            $utilisateur = $stmt->fetch();
            
            if (password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                // Suppression du mot de passe avant de retourner les données
                unset($utilisateur['mot_de_passe']);
                return $utilisateur;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'authentification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère un utilisateur par son ID
     * 
     * @param int $id ID de l'utilisateur
     * @return array|bool Données de l'utilisateur ou false en cas d'échec
     */
    public function obtenirParId($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, nom, prenom, email, date_creation
                FROM utilisateurs
                WHERE id = :id
            ");
            
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() === 0) {
                return false;
            }
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour les informations d'un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @param array $donnees Données à mettre à jour
     * @return bool True en cas de succès, sinon False
     */
    public function mettreAJour($id, $donnees) {
        try {
            $champs = [];
            $params = [':id' => $id];
            
            // Construction dynamique des champs à mettre à jour
            foreach ($donnees as $champ => $valeur) {
                if (in_array($champ, ['nom', 'prenom', 'email'])) {
                    $champs[] = "$champ = :$champ";
                    $params[":$champ"] = $valeur;
                }
            }
            
            if (empty($champs)) {
                return false;
            }
            
            $sql = "UPDATE utilisateurs SET " . implode(', ', $champs) . " WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Change le mot de passe d'un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @param string $ancien_mot_de_passe Ancien mot de passe
     * @param string $nouveau_mot_de_passe Nouveau mot de passe
     * @return bool True en cas de succès, sinon False
     */
    public function changerMotDePasse($id, $ancien_mot_de_passe, $nouveau_mot_de_passe) {
        try {
            // Récupération du mot de passe actuel
            $stmt = $this->pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() === 0) {
                return false;
            }
            
            $utilisateur = $stmt->fetch();
            
            // Vérification de l'ancien mot de passe
            if (!password_verify($ancien_mot_de_passe, $utilisateur['mot_de_passe'])) {
                return false;
            }
            
            // Hashage du nouveau mot de passe
            $nouveau_mot_de_passe_hash = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
            
            // Mise à jour du mot de passe
            $stmt = $this->pdo->prepare("UPDATE utilisateurs SET mot_de_passe = :mot_de_passe WHERE id = :id");
            
            return $stmt->execute([
                ':mot_de_passe' => $nouveau_mot_de_passe_hash,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors du changement de mot de passe: " . $e->getMessage());
            return false;
        }
    }
} 