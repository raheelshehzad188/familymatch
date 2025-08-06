-- =====================================================
-- COMPLETE SETTINGS TABLE UPDATE
-- =====================================================

-- Step 1: Add field_type column (if not exists)
ALTER TABLE `settings` ADD COLUMN IF NOT EXISTS `field_type` ENUM('text', 'image', 'textarea', 'select', 'checkbox') DEFAULT 'text' AFTER `meta_value`;

-- Step 2: Add created_at column (if not exists)
ALTER TABLE `settings` ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `field_type`;

-- Step 3: Add updated_at column (if not exists)
ALTER TABLE `settings` ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Step 4: Update existing records to have proper field_type
UPDATE `settings` SET `field_type` = 'text' WHERE `field_type` IS NULL OR `field_type` = '';

-- Step 5: Update existing records to have created_at timestamp
UPDATE `settings` SET `created_at` = CURRENT_TIMESTAMP WHERE `created_at` IS NULL;

-- Step 6: Update existing records to have updated_at timestamp
UPDATE `settings` SET `updated_at` = CURRENT_TIMESTAMP WHERE `updated_at` IS NULL;

-- Step 7: Show final table structure
DESCRIBE `settings`;

-- Step 8: Show all current settings
SELECT * FROM `settings`;

-- =====================================================
-- OPTIONAL: Add sample settings for testing
-- =====================================================

-- Add sample settings (uncomment if needed)
/*
INSERT INTO `settings` (`meta_key`, `meta_value`, `field_type`, `created_at`, `updated_at`) VALUES 
('site_logo', '', 'image', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('site_title', 'FamilyMatch', 'text', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('admin_email', 'admin@familymatch.com', 'text', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('contact_phone', '+1234567890', 'text', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('about_us', 'FamilyMatch is a leading matrimonial platform...', 'textarea', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('maintenance_mode', '0', 'checkbox', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
*/ 