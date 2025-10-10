SET @table_name := 'PREFIX_art_puzzle_customization';

SET @table_exists := (
    SELECT COUNT(*)
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = @table_name
);

SET @index_exists := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = @table_name
      AND INDEX_NAME = 'idx_id_product'
);
SET @sql := IF(
    @table_exists > 0 AND @index_exists = 0,
    CONCAT('ALTER TABLE `', @table_name, '` ADD INDEX `idx_id_product` (`id_product`)'),
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = @table_name
      AND INDEX_NAME = 'idx_id_cart'
);
SET @sql := IF(
    @table_exists > 0 AND @index_exists = 0,
    CONCAT('ALTER TABLE `', @table_name, '` ADD INDEX `idx_id_cart` (`id_cart`)'),
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = @table_name
      AND INDEX_NAME = 'idx_id_order'
);
SET @sql := IF(
    @table_exists > 0 AND @index_exists = 0,
    CONCAT('ALTER TABLE `', @table_name, '` ADD INDEX `idx_id_order` (`id_order`)'),
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
