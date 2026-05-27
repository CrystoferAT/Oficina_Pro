<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-dark bg-dark shadow-sm mb-3">
    <div class="container">
        <ul class="navbar-nav d-flex flex-row gap-3">
           <li class="nav-item text-white">
                <i class="bi bi-person-circle me-1"></i>
                <strong><?= explode(' ', $_SESSION['usuario_nome'] ?? 'Usuário')[0] ?></strong> 
                <span class="badge bg-secondary ms-1" style="font-size: 0.7rem;">
                    <?= $_SESSION['usuario_nivel'] ?? '' ?>
                </span>
            </li>
        </ul>
    </div>
</nav>

<main class="container my-5">
    </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>