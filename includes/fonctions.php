<?php
/**
 * Fichier contenant les fonctions utilitaires de l'application
 */

/**
 * Nettoie les données d'entrée
 * 
 * @param string $data Données à nettoyer
 * @return string Données nettoyées
 */
function nettoyer($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Redirige vers une URL spécifique
 * 
 * @param string $url URL de redirection
 * @return void
 */
function rediriger($url) {
    header("Location: $url");
    exit;
}

/**
 * Vérifie si l'utilisateur est connecté
 * 
 * @return bool True si l'utilisateur est connecté, sinon False
 */
function estConnecte() {
    return isset($_SESSION['utilisateur_id']);
}

/**
 * Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
 * 
 * @return void
 */
function exigerConnexion() {
    if (!estConnecte()) {
        $_SESSION['message_erreur'] = "Vous devez être connecté pour accéder à cette page.";
        rediriger(ROUTE_CONNEXION);
    }
}

/**
 * Génère un message d'alerte
 * 
 * @param string $message Message à afficher
 * @param string $type Type d'alerte (success, danger, warning, info)
 * @return string Code HTML de l'alerte
 */
function alerte($message, $type = 'info') {
    return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$message}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Fermer'></button>
            </div>";
}

/**
 * Affiche les messages d'erreur ou de succès stockés en session
 * 
 * @return string Code HTML des messages
 */
function afficherMessages() {
    $output = '';
    
    if (isset($_SESSION['message_succes'])) {
        $output .= alerte($_SESSION['message_succes'], 'success');
        unset($_SESSION['message_succes']);
    }
    
    if (isset($_SESSION['message_erreur'])) {
        $output .= alerte($_SESSION['message_erreur'], 'danger');
        unset($_SESSION['message_erreur']);
    }
    
    if (isset($_SESSION['message_info'])) {
        $output .= alerte($_SESSION['message_info'], 'info');
        unset($_SESSION['message_info']);
    }
    
    return $output;
} 