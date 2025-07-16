<?php
/**
 * Page des projets
 */
require_once 'config/config.php';

// Vérification que l'utilisateur est connecté
exigerConnexion();

// Chargement du contrôleur des projets
include 'controleurs/projets_controleur.php';

// Inclusion du header
include 'includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Mes Projets</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-ajout-projet">
            <i class="fas fa-plus me-2"></i>Nouveau Projet
        </button>
    </div>
    
    <?php if (empty($projets)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Vous n'avez pas encore de projets. Créez votre premier projet pour commencer !
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($projets as $projet): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card projet-card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($projet['titre']) ?></h5>
                            <p class="card-text text-muted small">
                                <i class="far fa-calendar-alt me-1"></i>
                                <?= date('d/m/Y', strtotime($projet['date_creation'])) ?>
                            </p>
                            <?php if (!empty($projet['description'])): ?>
                                <p class="card-text"><?= htmlspecialchars($projet['description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="task-stats">
                                    <span class="badge bg-light text-dark me-1" title="À faire">
                                        <i class="far fa-circle text-secondary me-1"></i><?= $projet['statistiques']['a_faire'] ?>
                                    </span>
                                    <span class="badge bg-light text-dark me-1" title="En cours">
                                        <i class="fas fa-spinner text-primary me-1"></i><?= $projet['statistiques']['en_cours'] ?>
                                    </span>
                                    <span class="badge bg-light text-dark" title="Terminé">
                                        <i class="fas fa-check-circle text-success me-1"></i><?= $projet['statistiques']['termine'] ?>
                                    </span>
                                </div>
                                
                                <div class="btn-group">
                                    <a href="tableau.php?id=<?= $projet['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-tasks me-1"></i>Tableau
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modal-edit-projet-<?= $projet['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-delete-projet-<?= $projet['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal de modification du projet -->
                    <div class="modal fade" id="modal-edit-projet-<?= $projet['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Modifier le projet</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <form action="<?= APP_URL ?>/controleurs/projets_controleur.php" method="post">
                                    <input type="hidden" name="action" value="modifier_projet">
                                    <input type="hidden" name="projet_id" value="<?= $projet['id'] ?>">
                                    
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="titre-<?= $projet['id'] ?>" class="form-label">Titre</label>
                                            <input type="text" class="form-control" id="titre-<?= $projet['id'] ?>" name="titre" value="<?= htmlspecialchars($projet['titre']) ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description-<?= $projet['id'] ?>" class="form-label">Description</label>
                                            <textarea class="form-control" id="description-<?= $projet['id'] ?>" name="description" rows="3"><?= htmlspecialchars($projet['description']) ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal de suppression du projet -->
                    <div class="modal fade" id="modal-delete-projet-<?= $projet['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmer la suppression</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Êtes-vous sûr de vouloir supprimer le projet "<?= htmlspecialchars($projet['titre']) ?>" ?</p>
                                    <p class="text-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Cette action est irréversible et supprimera également toutes les tâches associées.
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <form action="<?= APP_URL ?>/controleurs/projets_controleur.php" method="post">
                                        <input type="hidden" name="action" value="supprimer_projet">
                                        <input type="hidden" name="projet_id" value="<?= $projet['id'] ?>">
                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal d'ajout de projet -->
<div class="modal fade" id="modal-ajout-projet" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="<?= APP_URL ?>/controleurs/projets_controleur.php" method="post">
                <input type="hidden" name="action" value="creer_projet">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Inclusion du footer
include 'includes/footer.php';
?> 