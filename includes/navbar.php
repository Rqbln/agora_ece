<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="/agora_ece/index.php">
            <img src="/agora_ece/assets/img/logo_agora.png" alt="Logo" style="height: 80px; margin-right: 10px;">
            Agora
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link nav-hover-effect" href="/agora_ece/login.php">Connexion / S'inscrire</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link nav-hover-effect" href="/agora_ece/pages/seller_space.php">Espace Vendeur</a></li>
                <li class="nav-item"><a class="nav-link nav-hover-effect" href="/agora_ece/pages/negotiations.php">Négociations</a></li>
            </ul>
            <form class="d-flex">
                <button class="btn btn-outline-dark me-2" type="button">
                    <a class="nav-link" href="/agora_ece/pages/cart.php">
                        <i class="bi-cart-fill me-1"></i>
                        Panier
                        <span class="badge bg-dark text-white ms-1 rounded-pill">0</span>
                    </a>
                </button>
                <button class="btn btn-outline-dark" type="button">
                    <a class="nav-link" href="/agora_ece/pages/profile.php">
                        <i class="bi-person-fill me-1"></i>
                        Profil
                    </a>
                </button>
            </form>
        </div>
    </div>
</nav>
