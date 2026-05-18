<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor', 'super_admin', 'approver']);
require_once __DIR__ . '/../../../classes/EntryRequest.php';

$entryModel = new EntryRequest();
$id = $_GET['id'] ?? null;

if (!$id) { header('Location: list.php'); exit; }

$request = $entryModel->getById($id);
if (!$request) { header('Location: list.php'); exit; }

$pageTitle = 'View Entry Permit';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
?>

<div class="main-content fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div class="d-flex gap-2">
            <a href="list.php" class="btn btn-outline-cyan"><i class="bi bi-arrow-left"></i> Back to List</a>
            <?php if ($request['status'] === 'draft' || $request['status'] === 'pending'): ?>
                <a href="add.php?id=<?= $request['id'] ?>" class="btn btn-outline-warning"><i class="bi bi-pencil"></i> Edit Request</a>
            <?php endif; ?>
        </div>
        <button onclick="window.print()" class="btn btn-cyan px-4"><i class="bi bi-printer"></i> Print Document</button>
    </div>

    <!-- Document Layout -->
    <div class="document-wrapper shadow-lg mx-auto" style="max-width: 900px; background: #fff; color: #000; padding: 50px; font-family: 'Arial', sans-serif;">
        
        <!-- Header Section -->
        <div class="header-container mb-4">
            <div class="row align-items-center">
                <div class="col-8">
                    <div style="font-size: 3rem; font-weight: 900; color: #1a3c6c; text-transform: uppercase; line-height: 1;">
                        <?= htmlspecialchars($request['company_name']) ?>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div style="font-size: 1.2rem; font-weight: bold;">
                        <i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($request['primary_contact_phone'] ?? '0321-6814000') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-4">
            <h4 style="text-decoration: underline; font-weight: bold; margin-bottom: 5px; font-size: 1.4rem;">REQUEST FOR ENTRY PERMISSION FOR LABOURS</h4>
            <div style="font-weight: bold; font-size: 1.1rem;">PART-I</div>
        </div>

        <!-- Info Section -->
        <div class="info-section mb-4" style="font-size: 1.05rem;">
            <div class="mb-3 d-flex align-items-center gap-4">
                <div style="font-weight: bold; min-width: 220px;">Type of Firm/ Contractor:</div>
                <div class="d-flex gap-4">
                    <span>Registered <span style="display:inline-block; border-bottom:1px solid #000; width:40px; text-align:center; vertical-align: bottom;"><?= $request['verification_status'] === 'verified' ? '✓' : '' ?></span></span>
                    <span>Unregistered <span style="display:inline-block; border-bottom:1px solid #000; width:40px; text-align:center; vertical-align: bottom;"></span></span>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-end">
                <div style="font-weight: bold; min-width: 140px;">Type of Worker:</div>
                <div style="border-bottom: 1px dotted #000; flex-grow: 1; padding-left: 10px; padding-bottom: 2px;">
                    <?= htmlspecialchars($request['type_of_worker']) ?>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-end">
                <div style="font-weight: bold; min-width: 280px;">Place of Work/ Site/ Nature of Work:</div>
                <div style="border-bottom: 1px dotted #000; flex-grow: 1; padding-left: 10px; padding-bottom: 2px;">
                    <?= htmlspecialchars($request['place_of_work']) ?>
                </div>
            </div>
        </div>

        <!-- Undertaking -->
        <div class="text-center mb-4">
            <h5 style="text-decoration: underline; font-weight: bold; margin-bottom: 15px;">Undertaking</h5>
            <p style="font-size: 1rem; text-align: justify; line-height: 1.5; margin: 0 auto;">
                We are 100% responsible for security and safety related concerns of the below mentioned. Therefore, you are requested to kindly allow the under mentioned staff and vehicle.
            </p>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">S.No</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Veh No</th>
                        <th>Mob No</th>
                        <th>CNIC No</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $workers = $request['workers'];
                    for ($i = 0; $i < 10; $i++): 
                        $w = $workers[$i] ?? null;
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td style="text-align: left;"><?= $w ? htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) : '' ?></td>
                        <td><?= $w ? htmlspecialchars($w['designation']) : '' ?></td>
                        <td><?= $w ? htmlspecialchars($w['worker_vehicle'] ?: ($request['vehicle_no'] ?: '-')) : '' ?></td>
                        <td><?= $w ? htmlspecialchars($w['phone'] ?: '-') : '' ?></td>
                        <td><?= $w ? htmlspecialchars($w['cnic']) : '' ?></td>
                        <td style="text-align: left; font-size: 0.8rem;"><?= $w ? htmlspecialchars($w['address'] ?: '-') : '' ?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Section -->
        <div class="footer-signatures mt-5 pt-4">
            <div class="row text-center" style="font-weight: bold; font-size: 1rem;">
                <div class="col-4">
                    <div style="border-bottom: 2px solid #000; margin: 0 10px 10px 10px; height: 30px;"></div>
                    <div>Contractor/ Supervisor</div>
                </div>
                <div class="col-4">
                    <div style="margin: 0 10px 10px 10px; height: 30px;"></div>
                    <div>Concern SDO</div>
                </div>
                <div class="col-4">
                    <div style="margin: 0 10px 10px 10px; height: 30px;"></div>
                    <div>GE (Navy)</div>
                </div>
            </div>
        </div>

        <div class="mt-5" style="font-size: 1.1rem;">
            <div style="font-weight: bold;">PART-II</div>
            <div class="mt-3">
                <?php if ($request['status'] === 'approved'): ?>
                    <strong>Permitted</strong> Date: <u><?= date('d M Y', strtotime($request['updated_at'])) ?></u>
                <?php elseif ($request['status'] === 'rejected'): ?>
                    <strong>Not Permitted</strong> Date: <u><?= date('d M Y', strtotime($request['updated_at'])) ?></u>
                <?php else: ?>
                    Permitted/ Not Permitted Date: ____________________________________
                <?php endif; ?>
            </div>
        </div>

        <!-- Stamp Area -->
        <div class="stamp-container text-end mt-4">
            <div style="display: inline-block; border: 3px solid #1a3c6c; padding: 15px; color: #1a3c6c; font-weight: 900; transform: rotate(-8deg); text-align: center; line-height: 1.1;">
                M/S <?= strtoupper(htmlspecialchars($request['company_name'])) ?><br>
                <span style="font-size: 0.8rem; font-weight: bold;">Govt. Contractor</span>
            </div>
        </div>

    </div>
</div>

<style>
/* Force high contrast white/black for the document view */
.document-wrapper {
    background-color: #ffffff !important;
    color: #000000 !important;
    box-shadow: 0 0 40px rgba(0,0,0,0.3);
    min-height: 297mm;
    position: relative;
    border: 1px solid #ddd;
    padding: 50px !important;
}

.document-wrapper * {
    color: #000000 !important;
}

.document-wrapper .text-cyan, 
.document-wrapper .bi-telephone-fill,
.document-wrapper .stamp-container div {
    color: #1a3c6c !important; /* Keep the branding color but dark enough */
}

.print-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    background-color: #ffffff !important;
}

.print-table th, 
.print-table td {
    border: 1px solid #000000 !important;
    padding: 10px;
    text-align: center;
    background-color: #ffffff !important;
    color: #000000 !important;
}

.print-table thead th {
    background-color: #f2f2f2 !important;
    font-weight: bold;
}

@media print {
    .no-print { display: none !important; }
    body { background: #ffffff !important; margin: 0 !important; padding: 0 !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    .sidebar, .navbar, .footer { display: none !important; }
    .document-wrapper { 
        box-shadow: none !important; 
        border: none !important; 
        padding: 0 !important; 
        margin: 0 !important; 
        width: 100% !important;
    }
    @page { 
        size: A4;
        margin: 1cm; 
    }
}
</style>

<script>
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('print')) {
        window.print();
    }
}
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
