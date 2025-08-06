-- Add updated_at column to settings table
ALTER TABLE `settings` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `field_type`;

-- Update existing records to have updated_at timestamp
UPDATE `settings` SET `updated_at` = CURRENT_TIMESTAMP WHERE `updated_at` IS NULL; 