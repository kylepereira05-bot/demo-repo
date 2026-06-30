-- -----------------------------------------------------
-- 1. Create and Select the Database
-- -----------------------------------------------------
CREATE DATABASE IF NOT EXISTS `resort_2_0` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `resort_2_0`;

-- -----------------------------------------------------
-- 2. Create Table: staff
-- (Created first as it is referenced by reports, payments, reservations)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` INT(11) NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(100) NOT NULL,
  `user_name` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL, -- Increased length for hashed passwords
  `role` ENUM('Admin', 'Staff') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- 3. Create Table: customers
-- (Created next as it is referenced by reports, reservations, reviews)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` INT(11) NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(100) NOT NULL,
  `user_name` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL, -- Increased length for hashed passwords
  `contact_number` VARCHAR(15) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- 4. Create Table: accommodations
-- (Created next as it is referenced by reservations)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `accommodations` (
  `room_id` INT(11) NOT NULL AUTO_INCREMENT,
  `room_type` VARCHAR(100) NOT NULL,
  `price_per_night` DECIMAL(10,2) NOT NULL,
  `description` TEXT,
  `image_path` VARCHAR(255),
  `status` ENUM('Available', 'Occupied', 'Maintenance') NOT NULL DEFAULT 'Available',
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- 5. Create Table: reservations
-- (Central transaction table)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `reservations` (
  `res_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `room_id` INT(11) NOT NULL,
  `check_in` DATE NOT NULL,
  `check_out` DATE NOT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
  `processed_by` INT(11) NULL, -- Staff who processed it
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method` ENUM('Walk-in', 'Online') NOT NULL,
  `payment_status` ENUM('Pending', 'Paid') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`res_id`),
  -- Foreign Key Relationships (Matching Green, Pink, and Dark Blue lines)
  CONSTRAINT `fk_res_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_res_room` FOREIGN KEY (`room_id`) REFERENCES `accommodations` (`room_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_res_staff` FOREIGN KEY (`processed_by`) REFERENCES `staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- 6. Create Table: payments
-- (Matching the light green relationship from reservations)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` INT(11) NOT NULL AUTO_INCREMENT,
  `res_id` INT(11) NOT NULL,
  `staff_id` INT(11) NOT NULL,
  `payment_method` ENUM('Walk-in', 'Online') NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_status` ENUM('Unpaid', 'Paid') NOT NULL DEFAULT 'Unpaid',
  `transaction_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  -- Foreign Key Relationships
  CONSTRAINT `fk_pay_res` FOREIGN KEY (`res_id`) REFERENCES `reservations` (`res_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pay_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- 7. Create Table: reviews
-- (Matching relationships from reservations and customers)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `res_id` INT(11) NOT NULL,
  `rating` INT(11) NOT NULL DEFAULT 5, -- Changed rating to INT for simplicity
  `comment` TEXT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  -- Foreign Key Relationships
  CONSTRAINT `fk_rev_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rev_res` FOREIGN KEY (`res_id`) REFERENCES `reservations` (`res_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- 8. Create Table: reports
-- (Final table matching light blue and dark blue relationships)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `reports` (
  `report_id` INT(11) NOT NULL AUTO_INCREMENT,
  `res_id` INT(11) NULL, -- Made Nullable as not all reports reference one reservation
  `customer_id` INT(11) NULL, -- Made Nullable as not all reports reference one customer
  `generated_by` INT(11) NOT NULL,
  `report_type` ENUM('Booking Receipt', 'Monthly Revenue', 'Guest Summary') NOT NULL,
  `generated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  -- Foreign Key Relationships
  CONSTRAINT `fk_rep_res` FOREIGN KEY (`res_id`) REFERENCES `reservations` (`res_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rep_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rep_staff` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`staff_id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- 9. Add Optional Performance Indexes on Foreign Keys
-- (phpMyAdmin often creates these automatically with FKs)
-- -----------------------------------------------------
ALTER TABLE `reservations` ADD INDEX (`customer_id`), ADD INDEX (`room_id`), ADD INDEX (`processed_by`);
ALTER TABLE `payments` ADD INDEX (`res_id`), ADD INDEX (`staff_id`);
ALTER TABLE `reviews` ADD INDEX (`customer_id`), ADD INDEX (`res_id`);
ALTER TABLE `reports` ADD INDEX (`res_id`), ADD INDEX (`customer_id`), ADD INDEX (`generated_by`);