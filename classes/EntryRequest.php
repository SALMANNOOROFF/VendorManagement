<?php
require_once __DIR__ . '/../config/database.php';

class EntryRequest {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO entry_requests (vendor_id, type_of_worker, place_of_work, vehicle_no, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['vendor_id'],
                $data['type_of_worker'],
                $data['place_of_work'],
                $data['vehicle_no'] ?? null,
                $data['status'] ?? 'draft'
            ]);

            $requestId = $this->db->lastInsertId();

            if (!empty($data['workers'])) {
                $stmtWorker = $this->db->prepare("INSERT INTO entry_request_workers (request_id, worker_id, vehicle_no) VALUES (?, ?, ?)");
                foreach ($data['workers'] as $worker) {
                    $stmtWorker->execute([
                        $requestId,
                        $worker['id'],
                        $worker['vehicle_no'] ?? null
                    ]);
                }
            }

            $this->db->commit();
            return $requestId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("UPDATE entry_requests SET type_of_worker = ?, place_of_work = ?, vehicle_no = ?, status = ? WHERE id = ?");
            $stmt->execute([
                $data['type_of_worker'],
                $data['place_of_work'],
                $data['vehicle_no'] ?? null,
                $data['status'] ?? 'draft',
                $id
            ]);

            // Clear and re-add workers
            $this->db->prepare("DELETE FROM entry_request_workers WHERE request_id = ?")->execute([$id]);

            if (!empty($data['workers'])) {
                $stmtWorker = $this->db->prepare("INSERT INTO entry_request_workers (request_id, worker_id, vehicle_no) VALUES (?, ?, ?)");
                foreach ($data['workers'] as $worker) {
                    $stmtWorker->execute([
                        $id,
                        $worker['id'],
                        $worker['vehicle_no'] ?? null
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getByVendor($vendorId) {
        $stmt = $this->db->prepare("SELECT * FROM entry_requests WHERE vendor_id = ? ORDER BY created_at DESC");
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT er.*, v.company_name, v.primary_contact_phone, v.verification_status, v.user_id as vendor_user_id
            FROM entry_requests er
            JOIN vendors v ON er.vendor_id = v.id
            WHERE er.id = ?
        ");
        $stmt->execute([$id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            $stmtWorkers = $this->db->prepare("
                SELECT w.*, erw.vehicle_no as worker_vehicle
                FROM entry_request_workers erw
                JOIN workers w ON erw.worker_id = w.id
                WHERE erw.request_id = ?
            ");
            $stmtWorkers->execute([$id]);
            $request['workers'] = $stmtWorkers->fetchAll(PDO::FETCH_ASSOC);
        }

        return $request;
    }
    public function getAllPending() {
        $stmt = $this->db->prepare("
            SELECT er.*, v.company_name 
            FROM entry_requests er
            JOIN vendors v ON er.vendor_id = v.id
            WHERE er.status = 'pending'
            ORDER BY er.created_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHistory($status = null) {
        $sql = "
            SELECT er.*, v.company_name 
            FROM entry_requests er
            JOIN vendors v ON er.vendor_id = v.id
            WHERE 1=1
        ";
        $params = [];
        if ($status) {
            $sql .= " AND er.status = ?";
            $params[] = $status;
        } else {
            $sql .= " AND er.status IN ('approved', 'rejected')";
        }
        $sql .= " ORDER BY er.updated_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approve($id, $remarks = null) {
        $stmt = $this->db->prepare("UPDATE entry_requests SET status = 'approved', remarks = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$remarks, $id]);
    }

    public function reject($id, $remarks = null) {
        $stmt = $this->db->prepare("UPDATE entry_requests SET status = 'rejected', remarks = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$remarks, $id]);
    }
}
?>
