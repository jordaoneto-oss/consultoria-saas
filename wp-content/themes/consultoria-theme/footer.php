</main>

<footer class="site-footer bg-dark text-light py-5 mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3"><?php echo get_bloginfo('name'); ?></h5>
                <p class="text-muted">A consultoria especializada que sua empresa merece.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h6 class="fw-bold mb-3">Links</h6>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer',
                    'container'      => false,
                    'menu_class'     => 'list-unstyled',
                    'fallback_cb'    => false,
                    'depth'          => 1,
                ]);
                ?>
            </div>
            <div class="col-md-4 mb-4">
                <h6 class="fw-bold mb-3">Contato</h6>
                <ul class="list-unstyled text-muted">
                    <li><i class="bi bi-envelope me-2"></i> suporte@consultoriasaas.com.br</li>
                    <li><i class="bi bi-whatsapp me-2"></i> (11) 99999-8888</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="text-center text-muted small">
            &copy; <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?>. Todos os direitos reservados.
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
