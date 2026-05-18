ALTER TABLE `entry_requests` MODIFY COLUMN `status` ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft';
