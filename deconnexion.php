<?php
/**
 * Page de déconnexion
 */
require_once 'config/config.php';

// Redirection vers le contrôleur de déconnexion
$_POST['action'] = 'deconnexion';
include 'controleurs/utilisateurs_controleur.php';
?> 