<footer class="footer-vms">
    <span>© <?= date('Y') ?> Vendor Management System — All rights reserved</span>
</footer>

<!-- Global Action Modal -->
<div class="modal fade" id="globalActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg); overflow: hidden;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="actionModalTitle">Action Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="actionModalMessage" class="text-muted-vms mb-3">Please provide remarks below.</p>
                <textarea id="actionModalRemarks" class="form-control form-control-vms" rows="3" placeholder="Enter your remarks here..."></textarea>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="actionModalConfirmBtn" class="btn rounded-pill px-4">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= $assetUrl ?? '/VendorM' ?>/assets/js/jquery.min.js"></script>
<script src="<?= $assetUrl ?? '/VendorM' ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= $assetUrl ?? '/VendorM' ?>/assets/js/main.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('navToggle');
    var navLinks = document.getElementById('navLinks');
    if (toggle && navLinks) {
        toggle.addEventListener('click', function() {
            navLinks.classList.toggle('open');
        });
        // Close on link click (mobile)
        navLinks.querySelectorAll('.nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) navLinks.classList.remove('open');
            });
        });
        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!toggle.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('open');
            }
        });
    }
});

function markNotificationRead(id) {
    fetch('<?= $assetUrl ?? '/VendorM' ?>/api/notifications.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'mark_read', id: id })
    });
}

function markAllNotificationsAsRead() {
    fetch('<?= $assetUrl ?? '/VendorM' ?>/api/notifications.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'mark_all_read' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update UI: change all items to read style in modal
            document.querySelectorAll('#notificationsModal .notif-item').forEach(item => {
                item.classList.remove('bg-light', 'border-primary');
                item.classList.add('bg-white', 'border-secondary');
                const badge = item.querySelector('.badge');
                if (badge) badge.remove();
            });
            // Update UI: change all items in dashboard list-group
            document.querySelectorAll('.list-group-item').forEach(item => {
                const badge = item.querySelector('.badge');
                if (badge) badge.remove();
            });
            // Update bell badge
            const badge = document.querySelector('.notif-badge');
            if (badge) badge.remove();
            
            showToast('All notifications marked as read', 'success');
        }
    });
}
</script>
</body>
</html>
