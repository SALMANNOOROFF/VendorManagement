CREATE TABLE `entry_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `request_title` varchar(255) DEFAULT 'REQUEST FOR ENTRY PERMISSION FOR LABOURS',
  `type_of_worker` varchar(255) DEFAULT NULL,
  `place_of_work` varchar(255) DEFAULT NULL,
  `vehicle_no` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `entry_request_workers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `vehicle_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`),
  KEY `worker_id` (`worker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `entry_requests`
  ADD CONSTRAINT `entry_requests_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

ALTER TABLE `entry_request_workers`
  ADD CONSTRAINT `entry_request_workers_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `entry_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entry_request_workers_ibfk_2` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE;
