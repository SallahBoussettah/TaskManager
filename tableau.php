<?php
/**
 * Page du tableau Kanban
 */
require_once 'config/config.php';

// Vérification que l'utilisateur est connecté
exigerConnexion();

// Vérification qu'un ID de projet est fourni
if (!isset($_GET['id'])) {
    $_SESSION['message_erreur'] = "Aucun projet spécifié.";
    rediriger(ROUTE_PROJETS);
}

// Chargement du contrôleur des projets
include 'controleurs/projets_controleur.php';

// Vérification que le projet a été trouvé
if (!$projet_actuel) {
    $_SESSION['message_erreur'] = "Projet non trouvé.";
    rediriger(ROUTE_PROJETS);
}

// Inclusion du header
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= ROUTE_PROJETS ?>" class="btn btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left me-2"></i>Retour aux projets
            </a>
            <h1 class="h2 mb-0"><?= htmlspecialchars($projet_actuel['titre']) ?></h1>
            <?php if (!empty($projet_actuel['description'])): ?>
                <p class="text-muted"><?= htmlspecialchars($projet_actuel['description']) ?></p>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-ajout-tache">
            <i class="fas fa-plus me-2"></i>Nouvelle Tâche
        </button>
    </div>
    
    <div class="kanban-container" data-projet-id="<?= $projet_actuel['id'] ?>">
        <!-- Colonne "À faire" -->
        <div class="kanban-column column-a-faire">
            <div class="kanban-column-header bg-light">
                <i class="far fa-circle me-2"></i>À faire
                <span class="badge bg-secondary ms-2" data-counter="a_faire"><?= count($projet_actuel['taches']['a_faire']) ?></span>
            </div>
            <div class="kanban-column-body" data-statut="a_faire">
                <?php foreach ($projet_actuel['taches']['a_faire'] as $tache): ?>
                    <div class="tache-item" data-id="<?= $tache['id'] ?>">
                        <div class="tache-titre"><?= htmlspecialchars($tache['titre']) ?></div>
                        <?php if (!empty($tache['description'])): ?>
                            <div class="tache-description"><?= htmlspecialchars($tache['description']) ?></div>
                        <?php endif; ?>
                        <div class="tache-actions">
                            <button class="btn btn-sm btn-outline-primary btn-action" data-bs-toggle="modal" data-bs-target="#modal-edit-tache-<?= $tache['id'] ?>" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-action btn-delete-tache" data-id="<?= $tache['id'] ?>" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Colonne "En cours" -->
        <div class="kanban-column column-en-cours">
            <div class="kanban-column-header bg-light">
                <i class="fas fa-spinner me-2"></i>En cours
                <span class="badge bg-primary ms-2" data-counter="en_cours"><?= count($projet_actuel['taches']['en_cours']) ?></span>
            </div>
            <div class="kanban-column-body" data-statut="en_cours">
                <?php foreach ($projet_actuel['taches']['en_cours'] as $tache): ?>
                    <div class="tache-item" data-id="<?= $tache['id'] ?>">
                        <div class="tache-titre"><?= htmlspecialchars($tache['titre']) ?></div>
                        <?php if (!empty($tache['description'])): ?>
                            <div class="tache-description"><?= htmlspecialchars($tache['description']) ?></div>
                        <?php endif; ?>
                        <div class="tache-actions">
                            <button class="btn btn-sm btn-outline-primary btn-action" data-bs-toggle="modal" data-bs-target="#modal-edit-tache-<?= $tache['id'] ?>" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-action btn-delete-tache" data-id="<?= $tache['id'] ?>" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Colonne "Terminé" -->
        <div class="kanban-column column-termine">
            <div class="kanban-column-header bg-light">
                <i class="fas fa-check-circle me-2"></i>Terminé
                <span class="badge bg-success ms-2" data-counter="termine"><?= count($projet_actuel['taches']['termine']) ?></span>
            </div>
            <div class="kanban-column-body" data-statut="termine">
                <?php foreach ($projet_actuel['taches']['termine'] as $tache): ?>
                    <div class="tache-item" data-id="<?= $tache['id'] ?>">
                        <div class="tache-titre"><?= htmlspecialchars($tache['titre']) ?></div>
                        <?php if (!empty($tache['description'])): ?>
                            <div class="tache-description"><?= htmlspecialchars($tache['description']) ?></div>
                        <?php endif; ?>
                        <div class="tache-actions">
                            <button class="btn btn-sm btn-outline-primary btn-action" data-bs-toggle="modal" data-bs-target="#modal-edit-tache-<?= $tache['id'] ?>" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-action btn-delete-tache" data-id="<?= $tache['id'] ?>" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout de tâche -->
<div class="modal fade" id="modal-ajout-tache" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="form-ajout-tache">
                <input type="hidden" name="projet_id" value="<?= $projet_actuel['id'] ?>">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titre-tache" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="titre-tache" name="titre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description-tache" class="form-label">Description</label>
                        <textarea class="form-control" id="description-tache" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="statut-tache" class="form-label">Statut</label>
                        <select class="form-select" id="statut-tache" name="statut">
                            <option value="a_faire">À faire</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                        </select>
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

<!-- Modals de modification des tâches -->
<?php foreach ($projet_actuel['taches'] as $statut => $taches): ?>
    <?php foreach ($taches as $tache): ?>
        <div class="modal fade" id="modal-edit-tache-<?= $tache['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier la tâche</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <form class="form-edit-tache" data-tache-id="<?= $tache['id'] ?>">
                        <input type="hidden" name="tache_id" value="<?= $tache['id'] ?>">
                        
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="titre-tache-<?= $tache['id'] ?>" class="form-label">Titre</label>
                                <input type="text" class="form-control" id="titre-tache-<?= $tache['id'] ?>" name="titre" value="<?= htmlspecialchars($tache['titre']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description-tache-<?= $tache['id'] ?>" class="form-label">Description</label>
                                <textarea class="form-control" id="description-tache-<?= $tache['id'] ?>" name="description" rows="3"><?= htmlspecialchars($tache['description']) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="statut-tache-<?= $tache['id'] ?>" class="form-label">Statut</label>
                                <select class="form-select" id="statut-tache-<?= $tache['id'] ?>" name="statut">
                                    <option value="a_faire" <?= $tache['statut'] === 'a_faire' ? 'selected' : '' ?>>À faire</option>
                                    <option value="en_cours" <?= $tache['statut'] === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                                    <option value="termine" <?= $tache['statut'] === 'termine' ? 'selected' : '' ?>>Terminé</option>
                                </select>
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
    <?php endforeach; ?>
<?php endforeach; ?>

<!-- Modal de confirmation de suppression de tâche -->
<div class="modal fade" id="modal-delete-tache" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette tâche ?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-tache">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<?php
// Inclusion du footer
include 'includes/footer.php';
?> 