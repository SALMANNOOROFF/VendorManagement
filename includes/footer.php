<script src="<?= $appUrl ?? '/VendorM/public' ?>/assets/js/jquery.min.js"></script>
<script src="<?= $appUrl ?? '/VendorM/public' ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= $appUrl ?? '/VendorM/public' ?>/assets/js/main.js"></script>
<script>
// Mobile sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebarNav');
    var overlay = document.getElementById('sidebarOverlay');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('open');
        });
    }
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        });
    }
    // Close sidebar on link click (mobile)
    if (sidebar) {
        sidebar.querySelectorAll('.nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('open');
                    if (overlay) overlay.classList.remove('open');
                }
            });
        });
    }
});
</script>
</body>
</html>
