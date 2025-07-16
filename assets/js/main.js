/**
 * Script principal pour la gestion des tâches
 */
document.addEventListener('DOMContentLoaded', function() {
    // Base URL for AJAX requests
    const baseUrl = document.querySelector('meta[name="base-url"]') ? 
                    document.querySelector('meta[name="base-url"]').getAttribute('content') : '';
    
    // Initialisation du drag and drop pour le tableau Kanban
    initKanban(baseUrl);
    
    // Initialisation des modals et tooltips de Bootstrap
    initBootstrapComponents();
    
    // Gestion des formulaires AJAX
    initAjaxForms(baseUrl);
    
    // Vérifier les scrollbars au chargement
    initScrollbars();
});

/**
 * Initialise le système de drag and drop pour le tableau Kanban
 */
function initKanban(baseUrl) {
    const kanbanContainer = document.querySelector('.kanban-container');
    
    if (kanbanContainer) {
        const columns = document.querySelectorAll('.kanban-column-body');
        
        if (columns.length > 0) {
            // Initialisation de Dragula pour le drag and drop
            const drake = dragula(Array.from(columns), {
                moves: function(el, container, handle) {
                    return el.classList.contains('tache-item');
                }
            });
            
            // Événement déclenché lorsqu'une tâche est déposée dans une colonne
            drake.on('drop', function(el, target, source, sibling) {
                // Récupération des informations nécessaires
                const tacheId = el.getAttribute('data-id');
                const nouveauStatut = target.getAttribute('data-statut');
                const projetId = kanbanContainer.getAttribute('data-projet-id');
                
                // Mise à jour du statut de la tâche via AJAX
                updateTacheStatus(tacheId, nouveauStatut, projetId, baseUrl);
            });
        }
    }
}

/**
 * Met à jour le statut d'une tâche via une requête AJAX
 */
function updateTacheStatus(tacheId, nouveauStatut, projetId, baseUrl) {
    // Affichage d'un indicateur de chargement
    const tacheElement = document.querySelector(`.tache-item[data-id="${tacheId}"]`);
    const originalContent = tacheElement.innerHTML;
    tacheElement.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Mise à jour...</div>';
    
    // Récupération du statut précédent (colonne source)
    const sourceColumn = tacheElement.closest('.kanban-column-body');
    const ancienStatut = sourceColumn ? sourceColumn.getAttribute('data-statut') : null;
    
    // Création des données à envoyer
    const formData = new FormData();
    formData.append('tache_id', tacheId);
    formData.append('statut', nouveauStatut);
    formData.append('projet_id', projetId);
    formData.append('action', 'update_statut');
    
    // Envoi de la requête AJAX
    fetch(baseUrl + '/controleurs/taches_controleur.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mise à jour réussie
            tacheElement.innerHTML = originalContent;
            
            // Force refresh all counters
            refreshAllCounters();
            
            // Affichage d'une notification de succès
            afficherNotification('Statut mis à jour avec succès!');
        } else {
            // Erreur lors de la mise à jour
            tacheElement.innerHTML = originalContent;
            alert('Erreur lors de la mise à jour du statut: ' + data.message);
        }
    })
    .catch(error => {
        // Erreur lors de la requête
        tacheElement.innerHTML = originalContent;
        alert('Une erreur est survenue lors de la mise à jour du statut.');
    });
}

/**
 * Rafraîchit tous les compteurs en comptant les tâches dans chaque colonne
 */
function refreshAllCounters() {
    // Liste des statuts possibles
    const statuts = ['a_faire', 'en_cours', 'termine'];
    
    // Pour chaque statut, compter les tâches et mettre à jour le compteur
    statuts.forEach(statut => {
        const colonneBody = document.querySelector(`.kanban-column-body[data-statut="${statut}"]`);
        const compteur = document.querySelector(`[data-counter="${statut}"]`);
        
        if (colonneBody && compteur) {
            const nombreTaches = colonneBody.querySelectorAll('.tache-item').length;
            compteur.textContent = nombreTaches;
            
            // Vérifier si la colonne a besoin d'une scrollbar
            checkColumnScrollbar(colonneBody, nombreTaches);
        }
    });
}

/**
 * Vérifie si une colonne a besoin d'une scrollbar (plus de 7 tâches)
 * @param {HTMLElement} colonneBody - L'élément DOM de la colonne
 * @param {number} nombreTaches - Le nombre de tâches dans la colonne
 */
function checkColumnScrollbar(colonneBody, nombreTaches) {
    // Ajouter ou supprimer la classe scrollable selon le nombre de tâches
    if (nombreTaches > 7) {
        colonneBody.classList.add('scrollable');
    } else {
        colonneBody.classList.remove('scrollable');
    }
}

/**
 * Initialise les composants Bootstrap
 */
function initBootstrapComponents() {
    // Initialisation des tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialisation des popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

/**
 * Initialise les formulaires AJAX
 */
function initAjaxForms(baseUrl) {
    // Formulaire d'ajout de tâche
    const formAjoutTache = document.getElementById('form-ajout-tache');
    
    if (formAjoutTache) {
        formAjoutTache.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'ajouter_tache');
            
            // Récupération du statut sélectionné
            const statutSelectionne = formData.get('statut') || 'a_faire';
            
            // Envoi du formulaire via AJAX
            fetch(baseUrl + '/controleurs/taches_controleur.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ajout réussi, on ajoute la tâche au tableau sans rechargement
                    const colonneStatut = document.querySelector(`.kanban-column-body[data-statut="${statutSelectionne}"]`);
                    
                    if (colonneStatut) {
                        const nouvelleTache = document.createElement('div');
                        nouvelleTache.className = 'tache-item';
                        nouvelleTache.setAttribute('data-id', data.tache_id);
                        nouvelleTache.innerHTML = `
                            <div class="tache-titre">${data.titre}</div>
                            <div class="tache-description">${data.description || ''}</div>
                            <div class="tache-actions">
                                <button class="btn btn-sm btn-outline-primary btn-action" data-bs-toggle="modal" data-bs-target="#modal-edit-tache-${data.tache_id}" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-action btn-delete-tache" data-id="${data.tache_id}" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        
                        colonneStatut.appendChild(nouvelleTache);
                        
                        // Mise à jour des compteurs
                        refreshAllCounters();
                        
                        // Réinitialisation du formulaire
                        formAjoutTache.reset();
                        
                        // Fermeture du modal
                        const modalElement = document.getElementById('modal-ajout-tache');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        modalInstance.hide();
                        
                        // Affichage d'une notification de succès
                        afficherNotification('Tâche ajoutée avec succès!');
                    }
                } else {
                    // Erreur lors de l'ajout
                    alert('Erreur lors de l\'ajout de la tâche: ' + data.message);
                }
            })
            .catch(error => {
                alert('Une erreur est survenue lors de l\'ajout de la tâche.');
            });
        });
    }
    
    // Variables pour stocker les informations de la tâche à supprimer
    let tacheIdASupprimer = null;
    let tacheElementASupprimer = null;
    
    // Gestion du clic sur les boutons de suppression de tâche
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete-tache')) {
            const button = e.target.closest('.btn-delete-tache');
            tacheIdASupprimer = button.getAttribute('data-id');
            tacheElementASupprimer = document.querySelector(`.tache-item[data-id="${tacheIdASupprimer}"]`);
            
            // Afficher le modal de confirmation
            const deleteModal = new bootstrap.Modal(document.getElementById('modal-delete-tache'));
            deleteModal.show();
        }
    });
    
    // Gestion du clic sur le bouton de confirmation de suppression
    const confirmDeleteBtn = document.getElementById('confirm-delete-tache');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (tacheIdASupprimer) {
                // Création des données à envoyer
                const formData = new FormData();
                formData.append('tache_id', tacheIdASupprimer);
                formData.append('action', 'supprimer_tache');
                
                // Envoi de la requête AJAX
                fetch(baseUrl + '/controleurs/taches_controleur.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Suppression réussie, on retire la tâche du DOM
                        if (tacheElementASupprimer) {
                            tacheElementASupprimer.remove();
                            
                            // Mise à jour des compteurs
                            refreshAllCounters();
                            
                            // Fermer le modal
                            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('modal-delete-tache'));
                            deleteModal.hide();
                            
                            // Affichage d'une notification de succès
                            afficherNotification('Tâche supprimée avec succès!');
                        }
                    } else {
                        // Erreur lors de la suppression
                        alert('Erreur lors de la suppression de la tâche: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Une erreur est survenue lors de la suppression de la tâche.');
                });
            }
        });
    }
}

/**
 * Affiche une notification de succès
 * @param {string} message - Le message à afficher
 */
function afficherNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'position-fixed bottom-0 end-0 p-3';
    notification.style.zIndex = '5';
    notification.innerHTML = `
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    document.body.appendChild(notification);
    
    // Affichage de la notification
    const toast = new bootstrap.Toast(notification.querySelector('.toast'));
    toast.show();
    
    // Suppression de la notification après 3 secondes
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

/**
 * Initialise les scrollbars pour les colonnes qui ont plus de 7 tâches
 */
function initScrollbars() {
    // Liste des statuts possibles
    const statuts = ['a_faire', 'en_cours', 'termine'];
    
    // Pour chaque statut, vérifier si la colonne a besoin d'une scrollbar
    statuts.forEach(statut => {
        const colonneBody = document.querySelector(`.kanban-column-body[data-statut="${statut}"]`);
        
        if (colonneBody) {
            const nombreTaches = colonneBody.querySelectorAll('.tache-item').length;
            checkColumnScrollbar(colonneBody, nombreTaches);
        }
    });
} 