<?php require_once 'config/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= APP_URL ?>">
    <title><?= APP_NAME ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Dragula CSS pour le drag and drop -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="<?= ROUTE_ACCUEIL ?>">
                    <i class="fas fa-tasks me-2"></i><?= APP_NAME ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROUTE_ACCUEIL ?>">Accueil</a>
                        </li>
                        <?php if (estConnecte()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= ROUTE_PROJETS ?>">Mes Projets</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= ROUTE_DECONNEXION ?>">Déconnexion</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= ROUTE_CONNEXION ?>">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= ROUTE_INSCRIPTION ?>">Inscription</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="container py-4">
        <?= afficherMessages() ?> 