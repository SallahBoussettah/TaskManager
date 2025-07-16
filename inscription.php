<?php
/**
 * Page d'inscription
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
                <h1 class="h3 mb-4 text-center">Inscription</h1>
                
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
                    <input type="hidden" name="action" value="inscription">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?= $form_data['nom'] ?? '' ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= $form_data['prenom'] ?? '' ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $form_data['email'] ?? '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmation_mot_de_passe" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>S'inscrire
                        </button>
                    </div>
                </form>
                
                <div class="mt-4 text-center">
                    <p>Déjà inscrit ? <a href="<?= ROUTE_CONNEXION ?>">Connectez-vous</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclusion du footer
include 'includes/footer.php';
?> 