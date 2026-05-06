<?php
require_once __DIR__ . '/../config/database.php';

class Vendor {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function register(int $userId, array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO vendors (
                user_id, company_name, company_registration_no, ntn_number, strn_number,
                company_type_id, company_subtype_id, years_in_business, number_of_employees,
                annual_revenue, primary_contact_name, primary_contact_phone, primary_contact_email,
                primary_contact_cnic, secondary_contact_name, secondary_contact_phone,
                secondary_contact_email, address_line1, address_line2, city, state_province,
                postal_code, country, bank_name, bank_account_title, bank_account_no,
                bank_branch, iban, business_description, registration_certificate,
                ntn_certificate, tax_certificate, bank_statement, company_profile_doc,
                verification_status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending'
            )
        ");
        $stmt->execute([
            $userId,
            $data['company_name'] ?? '',
            $data['company_registration_no'] ?? null,
            $data['ntn_number'] ?? null,
            $data['strn_number'] ?? null,
            $data['company_type_id'] ?? null,
            $data['company_subtype_id'] ?? null,
            $data['years_in_business'] ?? null,
            $data['number_of_employees'] ?? null,
            $data['annual_revenue'] ?? null,
            $data['primary_contact_name'] ?? '',
            $data['primary_contact_phone'] ?? '',
            $data['primary_contact_email'] ?? '',
            $data['primary_contact_cnic'] ?? null,
            $data['secondary_contact_name'] ?? null,
            $data['secondary_contact_phone'] ?? null,
            $data['secondary_contact_email'] ?? null,
            $data['address_line1'] ?? '',
            $data['address_line2'] ?? null,
            $data['city'] ?? '',
            $data['state_province'] ?? null,
            $data['postal_code'] ?? null,
            $data['country'] ?? 'Pakistan',
            $data['bank_name'] ?? null,
            $data['bank_account_title'] ?? null,
            $data['bank_account_no'] ?? null,
            $data['bank_branch'] ?? null,
            $data['iban'] ?? null,
            $data['business_description'] ?? null,
            $data['registration_certificate'] ?? null,
            $data['ntn_certificate'] ?? null,
            $data['tax_certificate'] ?? null,
            $data['bank_statement'] ?? null,
            $data['company_profile_doc'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT v.*, ct.type_name, cs.subtype_name, u.email, u.username, u.status as account_status
            FROM vendors v
            JOIN users u ON v.user_id = u.id
            JOIN company_types ct ON v.company_type_id = ct.id
            LEFT JOIN company_subtypes cs ON v.company_subtype_id = cs.id
            WHERE v.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT v.*, ct.type_name, cs.subtype_name, u.email, u.username, u.status as account_status
            FROM vendors v
            JOIN users u ON v.user_id = u.id
            JOIN company_types ct ON v.company_type_id = ct.id
            LEFT JOIN company_subtypes cs ON v.company_subtype_id = cs.id
            WHERE v.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = []): array {
        $sql = "
            SELECT v.*, ct.type_name, cs.subtype_name, u.email, u.status as account_status, u.created_at as registered_on
            FROM vendors v
            JOIN users u ON v.user_id = u.id
            JOIN company_types ct ON v.company_type_id = ct.id
            LEFT JOIN company_subtypes cs ON v.company_subtype_id = cs.id
            WHERE 1=1
        ";
        $params = [];
        if (!empty($filters['verification_status'])) {
            $sql .= " AND v.verification_status = ?";
            $params[] = $filters['verification_status'];
        }
        if (!empty($filters['company_type_id'])) {
            $sql .= " AND v.company_type_id = ?";
            $params[] = $filters['company_type_id'];
        }
        $sql .= " ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function approve(int $vendorId, int $approverId, ?string $notes = null): bool {
        $this->db->beginTransaction();
        try {
            // Update vendor
            $stmt = $this->db->prepare("
                UPDATE vendors SET verification_status = 'verified', reviewer_notes = ?, updated_at = NOW() WHERE id = ?
            ");
            $stmt->execute([$notes, $vendorId]);

            // Get user_id
            $vendor = $this->getById($vendorId);
            if ($vendor) {
                // Update user status
                $this->db->prepare("UPDATE users SET status = 'active', approved_by = ?, approved_at = NOW() WHERE id = ?")
                         ->execute([$approverId, $vendor['user_id']]);

                // Update workflow
                $this->db->prepare("
                    UPDATE approval_workflow SET status = 'approved', approver_id = ?, comments = ?, reviewed_at = NOW()
                    WHERE vendor_user_id = ? AND status IN ('pending','under_review')
                ")->execute([$approverId, $notes, $vendor['user_id']]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function reject(int $vendorId, int $approverId, string $reason, ?string $notes = null): bool {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                UPDATE vendors SET verification_status = 'rejected', rejection_reason = ?, reviewer_notes = ?, updated_at = NOW() WHERE id = ?
            ");
            $stmt->execute([$reason, $notes, $vendorId]);

            $vendor = $this->getById($vendorId);
            if ($vendor) {
                $this->db->prepare("UPDATE users SET status = 'rejected' WHERE id = ?")
                         ->execute([$vendor['user_id']]);

                $this->db->prepare("
                    UPDATE approval_workflow SET status = 'rejected', approver_id = ?, rejection_reason = ?, comments = ?, reviewed_at = NOW()
                    WHERE vendor_user_id = ? AND status IN ('pending','under_review')
                ")->execute([$approverId, $reason, $notes, $vendor['user_id']]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE vendors SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        return $this->db->prepare($sql)->execute($params);
    }

    public function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM vendors WHERE 1=1";
        $params = [];
        if (!empty($filters['verification_status'])) {
            $sql .= " AND verification_status = ?";
            $params[] = $filters['verification_status'];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function createWorkflow(int $vendorUserId): int {
        $stmt = $this->db->prepare("INSERT INTO approval_workflow (vendor_user_id, status) VALUES (?, 'pending')");
        $stmt->execute([$vendorUserId]);
        return (int)$this->db->lastInsertId();
    }
}
