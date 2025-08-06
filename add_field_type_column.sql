-- Add field_type column to settings table
ALTER TABLE `settings` ADD COLUMN `field_type` ENUM('text', 'image') DEFAULT 'text' AFTER `meta_value`;

-- Add updated_at column to settings table
ALTER TABLE `settings` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Update existing settings to have 'text' as default field_type
UPDATE `settings` SET `field_type` = 'text' WHERE `field_type` IS NULL;

-- Example: Add a sample image setting
INSERT INTO `settings` (`meta_key`, `meta_value`, `field_type`) VALUES 
('site_logo', '', 'image'),
('favicon', '', 'image'),
('banner_image', '', 'image'); 