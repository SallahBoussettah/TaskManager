<?php
/**
 * Contrôleur pour la gestion des utilisateurs
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../modeles/Utilisateur.php';

// Initialisation du modèle
$utilisateurModel = new Utilisateur($pdo);

// Traitement des actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'inscription':
            // Récupération des données du formulaire
            $nom = nettoyer($_POST['nom']);
            $prenom = nettoyer($_POST['prenom']);
            $email = nettoyer($_POST['email']);
            $mot_de_passe = $_POST['mot_de_passe'];
            $confirmation_mot_de_passe = $_POST['confirmation_mot_de_passe'];
            
            // Validation des données
            $erreurs = [];
            
            if (empty($nom)) {
                $erreurs[] = "Le nom est requis.";
            }
            
            if (empty($prenom)) {
                $erreurs[] = "Le prénom est requis.";
            }
            
            if (empty($email)) {
                $erreurs[] = "L'email est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "L'email n'est pas valide.";
            } elseif ($utilisateurModel->existeParEmail($email)) {
                $erreurs[] = "Cet email est déjà utilisé.";
            }
            
            if (empty($mot_de_passe)) {
                $erreurs[] = "Le mot de passe est requis.";
            } elseif (strlen($mot_de_passe) < 6) {
                $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
            }
            
            if ($mot_de_passe !== $confirmation_mot_de_passe) {
                $erreurs[] = "Les mots de passe ne correspondent pas.";
            }
            
            // Si aucune erreur, on crée l'utilisateur
            if (empty($erreurs)) {
                $utilisateur_id = $utilisateurModel->creer($nom, $prenom, $email, $mot_de_passe);
                
                if ($utilisateur_id) {
                    // Connexion automatique après inscription
                    $_SESSION['utilisateur_id'] = $utilisateur_id;
                    $_SESSION['utilisateur_nom'] = $nom;
                    $_SESSION['utilisateur_prenom'] = $prenom;
                    $_SESSION['utilisateur_email'] = $email;
                    
                    $_SESSION['message_succes'] = "Inscription réussie ! Bienvenue, $prenom $nom.";
                    
                    // Redirection vers la page des projets
                    rediriger(ROUTE_PROJETS);
                } else {
                    $_SESSION['message_erreur'] = "Une erreur est survenue lors de l'inscription.";
                    rediriger(ROUTE_INSCRIPTION);
                }
            } else {
                // Stockage des erreurs en session
                $_SESSION['erreurs'] = $erreurs;
                $_SESSION['form_data'] = [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email
                ];
                
                rediriger(ROUTE_INSCRIPTION);
            }
            break;
            
        case 'connexion':
            // Récupération des données du formulaire
            $email = nettoyer($_POST['email']);
            $mot_de_passe = $_POST['mot_de_passe'];
            
            // Validation des données
            $erreurs = [];
            
            if (empty($email)) {
                $erreurs[] = "L'email est requis.";
            }
            
            if (empty($mot_de_passe)) {
                $erreurs[] = "Le mot de passe est requis.";
            }
            
            // Si aucune erreur, on tente l'authentification
            if (empty($erreurs)) {
                $utilisateur = $utilisateurModel->authentifier($email, $mot_de_passe);
                
                if ($utilisateur) {
                    // Stockage des informations de l'utilisateur en session
                    $_SESSION['utilisateur_id'] = $utilisateur['id'];
                    $_SESSION['utilisateur_nom'] = $utilisateur['nom'];
                    $_SESSION['utilisateur_prenom'] = $utilisateur['prenom'];
                    $_SESSION['utilisateur_email'] = $utilisateur['email'];
                    
                    $_SESSION['message_succes'] = "Connexion réussie ! Bienvenue, {$utilisateur['prenom']} {$utilisateur['nom']}.";
                    
                    // Redirection vers la page des projets
                    rediriger(ROUTE_PROJETS);
                } else {
                    $_SESSION['message_erreur'] = "Email ou mot de passe incorrect.";
                    $_SESSION['form_data'] = ['email' => $email];
                    rediriger(ROUTE_CONNEXION);
                }
            } else {
                // Stockage des erreurs en session
                $_SESSION['erreurs'] = $erreurs;
                $_SESSION['form_data'] = ['email' => $email];
                rediriger(ROUTE_CONNEXION);
            }
            break;
            
        case 'deconnexion':
            // Suppression des informations de l'utilisateur en session
            unset($_SESSION['utilisateur_id']);
            unset($_SESSION['utilisateur_nom']);
            unset($_SESSION['utilisateur_prenom']);
            unset($_SESSION['utilisateur_email']);
            
            // Destruction de la session
            session_destroy();
            
            // Redirection vers la page d'accueil
            rediriger(ROUTE_ACCUEIL);
            break;
    }
} 