<!-- Font Awesome pour les icônes supplémentaires -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<!-- ========================================
     FOOTER PRINCIPAL - Dégradé bleu océan
     ======================================== -->
<footer class="footer-main mt-auto" style="background: linear-gradient(135deg, #0c4a6e 0%, #0ea5e9 100%);">
    <div class="container-fluid px-4">
        <!-- Contenu principal du footer -->
        <div class="row g-4 py-4">

            <!-- Colonne 1: À propos de l'entreprise -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand mb-3">
                    <i class="bi bi-gear-fill me-2"></i> Usine Industriel
                </div>
                <p class="mb-3" style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6;">
                    Système de gestion complet pour votre usine industrielles.
                    Optimisez votre production, votre stock et vos ventes.
                </p>
                <!-- Liens vers les réseaux sociaux -->
                <div class="footer-social">
                    <a href="#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link" title="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Colonne 2: Liens rapides -->
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-heading">Navigation</h6>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>dashboard/admin_dashboard.php"><i
                                class="bi bi-speedometer2"></i>Dashboard</a></li>
                    <li><a href="<?= BASE_URL ?>production/index.php"><i class="bi bi-hammer"></i>Production</a></li>
                    <li><a href="<?= BASE_URL ?>stock/index.php"><i class="bi bi-boxes"></i>Stock</a></li>
                    <li><a href="<?= BASE_URL ?>ventes/commandes.php"><i class="bi bi-cart-check"></i>Ventes</a></li>
                </ul>
            </div>

            <!-- Colonne 3: Ressources -->
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-heading">Ressources</h6>
                <ul class="footer-links">
                    <li><a href="#"><i class="bi bi-book"></i>Documentation</a></li>
                    <li><a href="#"><i class="bi bi-question-circle"></i>Aide</a></li>
                    <li><a href="#"><i class="bi bi-shield-check"></i>Confidentialité</a></li>
                    <li><a href="#"><i class="bi bi-file-text"></i>Conditions</a></li>
                </ul>
            </div>

            <!-- Colonne 4: Contact -->
            <div class="col-lg-4 col-md-6">
                <h6 class="footer-heading">Contact</h6>
                <ul class="footer-links">
                    <li><a href="#"><i class="bi bi-geo-alt"></i>Zone Industrielle, Tunis, Tunisie</a></li>
                    <li><a href="tel:+21612345678"><i class="bi bi-telephone"></i>+216 12 345 678</a></li>
                    <li><a href="mailto:contact@usineindustriel.com"><i
                                class="bi bi-envelope"></i>contact@usineindustriel.com</a></li>
                </ul>
            </div>
        </div>

        <!-- Barre du bas - Copyright -->
        <div class="footer-bottom py-3">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">
                        © 2024 <strong>Usine Industriel</strong>. Tous droits réservés.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span style="color: #64748b; font-size: 0.85rem;">
                        Propulsé par <a href="#"
                            style="color: var(--accent-color, #00d9ff); text-decoration: none;">Usine Industriel</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>

<!-- ========================================
     SCRIPTS JAVASCRIPT
     ======================================== -->

<!-- Bootstrap JS - Nécessaire pour la navbar et les dropdowns -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script pour l'horloge en temps réel -->
<script>
// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {

    // Fonction pour mettre à jour l'heure
    function updateTime() {
        // Créer un objet Date avec l'heure actuelle
        const now = new Date();

        // Formater l'heure en français (HH:MM:SS)
        const options = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        const timeString = now.toLocaleTimeString('fr-FR', options);

        // Rechercher l'élément avec l'ID 'header-time'
        const timeElement = document.getElementById('header-time');

        // Si l'élément existe, mettre à jour son contenu
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }

    // Mettre à jour l'heure immédiatement au chargement
    updateTime();

    // Mettre à jour l'heure toutes les secondes (1000ms)
    setInterval(updateTime, 1000);
});
</script>

</body>

</html>