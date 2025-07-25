<!-- WhatsApp Flutuante -->
    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" 
       class="whatsapp-float" 
       target="_blank" 
       title="Fale conosco no WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-droplet-fill me-2"></i><?php echo SITE_NAME; ?></h5>
                    <p class="mb-0">Os melhores produtos de limpeza para sua casa e negócio.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6>Contato</h6>
                    <p class="mb-0">
                        <i class="bi bi-whatsapp me-2"></i>
                        <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" class="text-white text-decoration-none">
                            WhatsApp
                        </a>
                    </p>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <small>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos os direitos reservados.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>