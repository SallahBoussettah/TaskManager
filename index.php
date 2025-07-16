<?php
/**
 * Page d'accueil
 */
require_once 'config/config.php';

// Inclusion du header
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-4 mb-4">Bienvenue sur <?= APP_NAME ?></h1>
            <p class="lead mb-4">
                Organisez vos projets et suivez vos tâches facilement avec notre tableau Kanban interactif.
            </p>
            
            <?php if (!estConnecte()): ?>
                <div class="mt-5">
                    <a href="<?= ROUTE_INSCRIPTION ?>" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-user-plus me-2"></i>Inscription
                    </a>
                    <a href="<?= ROUTE_CONNEXION ?>" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                </div>
            <?php else: ?>
                <div class="mt-5">
                    <a href="<?= ROUTE_PROJETS ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-project-diagram me-2"></i>Mes Projets
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row mt-5 pt-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-tasks fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4 mb-3">Gestion de Tâches</h3>
                    <p class="card-text">
                        Créez, organisez et suivez vos tâches avec un système de tableaux Kanban intuitif.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-project-diagram fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4 mb-3">Projets Personnels</h3>
                    <p class="card-text">
                        Gérez plusieurs projets simultanément, chacun avec son propre tableau de tâches.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-sync-alt fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4 mb-3">Mise à Jour en Temps Réel</h3>
                    <p class="card-text">
                        Déplacez vos tâches entre les colonnes avec une mise à jour instantanée sans rechargement de page.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclusion du footer
include 'includes/footer.php';
?> 