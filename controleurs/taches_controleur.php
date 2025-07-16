<?php
/**
 * Contrôleur pour la gestion des tâches (principalement via AJAX)
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../modeles/Tache.php';
require_once __DIR__ . '/../modeles/Projet.php';

// Vérification que l'utilisateur est connecté
if (!estConnecte()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Initialisation des modèles
$tacheModel = new Tache($pdo);
$projetModel = new Projet($pdo);

// Traitement des actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $utilisateur_id = $_SESSION['utilisateur_id'];
    
    switch ($action) {
        case 'ajouter_tache':
            // Récupération des données du formulaire
            $titre = nettoyer($_POST['titre']);
            $description = nettoyer($_POST['description']);
            $statut = nettoyer($_POST['statut'] ?? 'a_faire');
            $projet_id = (int) $_POST['projet_id'];
            
            // Vérification que le projet appartient bien à l'utilisateur
            $projet = $projetModel->obtenirParId($projet_id, $utilisateur_id);
            if (!$projet) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas accès à ce projet']);
                exit;
            }
            
            // Validation des données
            $erreurs = [];
            
            if (empty($titre)) {
                $erreurs[] = "Le titre est requis.";
            }
            
            if (!in_array($statut, ['a_faire', 'en_cours', 'termine'])) {
                $statut = 'a_faire';
            }
            
            // Si aucune erreur, on crée la tâche
            if (empty($erreurs)) {
                $tache_id = $tacheModel->creer($titre, $description, $statut, $projet_id);
                
                if ($tache_id) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tâche ajoutée avec succès',
                        'tache_id' => $tache_id,
                        'titre' => $titre,
                        'description' => $description,
                        'statut' => $statut
                    ]);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'ajout de la tâche']);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => implode(' ', $erreurs)]);
            }
            break;
            
        case 'modifier_tache':
            // Récupération des données du formulaire
            $tache_id = (int) $_POST['tache_id'];
            $titre = nettoyer($_POST['titre']);
            $description = nettoyer($_POST['description']);
            $statut = nettoyer($_POST['statut'] ?? null);
            
            // Récupération de la tâche
            $tache = $tacheModel->obtenirParId($tache_id);
            if (!$tache) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tâche non trouvée']);
                exit;
            }
            
            // Vérification que le projet associé appartient bien à l'utilisateur
            $projet = $projetModel->obtenirParId($tache['projet_id'], $utilisateur_id);
            if (!$projet) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas accès à cette tâche']);
                exit;
            }
            
            // Validation des données
            $erreurs = [];
            
            if (empty($titre)) {
                $erreurs[] = "Le titre est requis.";
            }
            
            // Préparation des données à mettre à jour
            $donnees = ['titre' => $titre, 'description' => $description];
            if ($statut !== null && in_array($statut, ['a_faire', 'en_cours', 'termine'])) {
                $donnees['statut'] = $statut;
            }
            
            // Si aucune erreur, on met à jour la tâche
            if (empty($erreurs)) {
                $resultat = $tacheModel->mettreAJour($tache_id, $donnees);
                
                if ($resultat) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tâche mise à jour avec succès',
                        'tache_id' => $tache_id,
                        'titre' => $titre,
                        'description' => $description,
                        'statut' => $statut ?? $tache['statut']
                    ]);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour de la tâche']);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => implode(' ', $erreurs)]);
            }
            break;
            
        case 'update_statut':
            // Récupération des données
            $tache_id = (int) $_POST['tache_id'];
            $statut = nettoyer($_POST['statut']);
            $projet_id = (int) $_POST['projet_id'];
            
            // Vérification que le projet appartient bien à l'utilisateur
            $projet = $projetModel->obtenirParId($projet_id, $utilisateur_id);
            if (!$projet) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas accès à ce projet']);
                exit;
            }
            
            // Vérification que la tâche appartient bien au projet
            if (!$tacheModel->appartientAuProjet($tache_id, $projet_id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Cette tâche n\'appartient pas à ce projet']);
                exit;
            }
            
            // Validation du statut
            if (!in_array($statut, ['a_faire', 'en_cours', 'termine'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Statut invalide']);
                exit;
            }
            
            // Mise à jour du statut
            $resultat = $tacheModel->mettreAJourStatut($tache_id, $statut);
            
            if ($resultat) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Statut mis à jour avec succès',
                    'tache_id' => $tache_id,
                    'statut' => $statut
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour du statut']);
            }
            break;
            
        case 'supprimer_tache':
            // Récupération de l'ID de la tâche
            $tache_id = (int) $_POST['tache_id'];
            
            // Récupération de la tâche
            $tache = $tacheModel->obtenirParId($tache_id);
            if (!$tache) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tâche non trouvée']);
                exit;
            }
            
            // Vérification que le projet associé appartient bien à l'utilisateur
            $projet = $projetModel->obtenirParId($tache['projet_id'], $utilisateur_id);
            if (!$projet) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas accès à cette tâche']);
                exit;
            }
            
            // Suppression de la tâche
            $resultat = $tacheModel->supprimer($tache_id);
            
            if ($resultat) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Tâche supprimée avec succès',
                    'tache_id' => $tache_id
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression de la tâche']);
            }
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
            break;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Aucune action spécifiée']);
} 