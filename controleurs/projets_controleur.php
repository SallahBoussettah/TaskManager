<?php
/**
 * Contrôleur pour la gestion des projets
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../modeles/Projet.php';
require_once __DIR__ . '/../modeles/Tache.php';

// Vérification que l'utilisateur est connecté
exigerConnexion();

// Initialisation des modèles
$projetModel = new Projet($pdo);
$tacheModel = new Tache($pdo);

// Traitement des actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'creer_projet':
            // Récupération des données du formulaire
            $titre = nettoyer($_POST['titre']);
            $description = nettoyer($_POST['description']);
            $utilisateur_id = $_SESSION['utilisateur_id'];
            
            // Validation des données
            $erreurs = [];
            
            if (empty($titre)) {
                $erreurs[] = "Le titre est requis.";
            }
            
            // Si aucune erreur, on crée le projet
            if (empty($erreurs)) {
                $projet_id = $projetModel->creer($titre, $description, $utilisateur_id);
                
                if ($projet_id) {
                    $_SESSION['message_succes'] = "Projet créé avec succès !";
                    rediriger(ROUTE_PROJETS);
                } else {
                    $_SESSION['message_erreur'] = "Une erreur est survenue lors de la création du projet.";
                    rediriger(ROUTE_PROJETS);
                }
            } else {
                // Stockage des erreurs en session
                $_SESSION['erreurs'] = $erreurs;
                $_SESSION['form_data'] = [
                    'titre' => $titre,
                    'description' => $description
                ];
                
                rediriger(ROUTE_PROJETS);
            }
            break;
            
        case 'modifier_projet':
            // Récupération des données du formulaire
            $projet_id = (int) $_POST['projet_id'];
            $titre = nettoyer($_POST['titre']);
            $description = nettoyer($_POST['description']);
            $utilisateur_id = $_SESSION['utilisateur_id'];
            
            // Validation des données
            $erreurs = [];
            
            if (empty($titre)) {
                $erreurs[] = "Le titre est requis.";
            }
            
            // Vérification que le projet appartient bien à l'utilisateur
            $projet = $projetModel->obtenirParId($projet_id, $utilisateur_id);
            if (!$projet) {
                $_SESSION['message_erreur'] = "Vous n'avez pas accès à ce projet.";
                rediriger(ROUTE_PROJETS);
            }
            
            // Si aucune erreur, on met à jour le projet
            if (empty($erreurs)) {
                $resultat = $projetModel->mettreAJour($projet_id, [
                    'titre' => $titre,
                    'description' => $description
                ], $utilisateur_id);
                
                if ($resultat) {
                    $_SESSION['message_succes'] = "Projet mis à jour avec succès !";
                    rediriger(ROUTE_PROJETS);
                } else {
                    $_SESSION['message_erreur'] = "Une erreur est survenue lors de la mise à jour du projet.";
                    rediriger(ROUTE_PROJETS);
                }
            } else {
                // Stockage des erreurs en session
                $_SESSION['erreurs'] = $erreurs;
                $_SESSION['form_data'] = [
                    'titre' => $titre,
                    'description' => $description
                ];
                
                rediriger(ROUTE_PROJETS);
            }
            break;
            
        case 'supprimer_projet':
            // Récupération de l'ID du projet
            $projet_id = (int) $_POST['projet_id'];
            $utilisateur_id = $_SESSION['utilisateur_id'];
            
            // Vérification que le projet appartient bien à l'utilisateur
            $projet = $projetModel->obtenirParId($projet_id, $utilisateur_id);
            if (!$projet) {
                $_SESSION['message_erreur'] = "Vous n'avez pas accès à ce projet.";
                rediriger(ROUTE_PROJETS);
            }
            
            // Suppression du projet
            $resultat = $projetModel->supprimer($projet_id, $utilisateur_id);
            
            if ($resultat) {
                $_SESSION['message_succes'] = "Projet supprimé avec succès !";
            } else {
                $_SESSION['message_erreur'] = "Une erreur est survenue lors de la suppression du projet.";
            }
            
            rediriger(ROUTE_PROJETS);
            break;
    }
}

// Récupération des projets de l'utilisateur pour l'affichage
$utilisateur_id = $_SESSION['utilisateur_id'];
$projets = $projetModel->obtenirParUtilisateur($utilisateur_id);

// Pour chaque projet, on récupère le nombre de tâches par statut
foreach ($projets as &$projet) {
    $projet['statistiques'] = $projetModel->compterTachesParStatut($projet['id']);
}

// Si on a un ID de projet dans l'URL, on récupère les détails du projet
$projet_actuel = null;
if (isset($_GET['id'])) {
    $projet_id = (int) $_GET['id'];
    $projet_actuel = $projetModel->obtenirParId($projet_id, $utilisateur_id);
    
    if ($projet_actuel) {
        // Récupération des tâches du projet groupées par statut
        $taches = $tacheModel->obtenirParProjetGroupeesParStatut($projet_id);
        $projet_actuel['taches'] = $taches;
    } else {
        $_SESSION['message_erreur'] = "Vous n'avez pas accès à ce projet.";
        rediriger(ROUTE_PROJETS);
    }
} 