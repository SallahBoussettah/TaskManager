<?php
/**
 * Page de connexion
 */
require_once 'config/config.php';

// Redirection si l'utilisateur est déjà connecté
if (estConnecte()) {
    rediriger(ROUTE_PROJETS);
}

// Récupération des données du formulaire en cas d'erreur
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// Récupération des erreurs
$erreurs = $_SESSION['erreurs'] ?? [];
unset($_SESSION['erreurs']);

// Inclusion du header
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-container">
                <h1 class="h3 mb-4 text-center">Connexion</h1>
                
                <?php if (!empty($erreurs)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($erreurs as $erreur): ?>
                                <li><?= $erreur ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= APP_URL ?>/controleurs/utilisateurs_controleur.php" method="post">
                    <input type="hidden" name="action" value="connexion">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $form_data['email'] ?? '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </button>
                    </div>
                </form>
                
                <div class="mt-4 text-center">
                    <p>Pas encore de compte ? <a href="<?= ROUTE_INSCRIPTION ?>">Inscrivez-vous</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclusion du footer
include 'includes/footer.php';
?> 