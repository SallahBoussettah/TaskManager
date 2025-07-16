<?php
/**
 * Configuration générale de l'application
 */

// Définition des constantes de l'application
define('APP_NAME', 'Tableau de Gestion de Projets');
define('APP_URL', 'http://localhost/GestionTaches');
define('APP_ROOT', dirname(__DIR__));

// Paramètres de session
session_start();

// Inclusion des fichiers requis
require_once APP_ROOT . '/config/database.php';
require_once APP_ROOT . '/includes/fonctions.php';

// Définition des constantes pour les routes
define('ROUTE_ACCUEIL', APP_URL . '/index.php');
define('ROUTE_CONNEXION', APP_URL . '/connexion.php');
define('ROUTE_INSCRIPTION', APP_URL . '/inscription.php');
define('ROUTE_DECONNEXION', APP_URL . '/deconnexion.php');
define('ROUTE_TABLEAU', APP_URL . '/tableau.php');
define('ROUTE_PROJETS', APP_URL . '/projets.php'); 