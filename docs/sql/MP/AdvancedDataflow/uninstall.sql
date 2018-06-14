UPDATE `eav_attribute` SET `backend_model` = 'eav/entity_attribute_backend_datetime' 
WHERE (`attribute_code` = 'special_to_date') AND (`entity_type_id` = (
    SELECT `entity_type_id` FROM `eav_entity_type` WHERE `entity_type_code` = 'catalog_product'
));

UPDATE `catalog_eav_attribute` SET `is_global` = 1 WHERE `attribute_id` IN (
    SELECT `attribute_id` FROM `eav_attribute` WHERE (`attribute_code` IN (
        'price', 'special_price', 'special_from_date', 'special_to_date', 'tier_price'
    )) AND (`entity_type_id` = (
        SELECT `entity_type_id` FROM `eav_entity_type` WHERE `entity_type_code` = 'catalog_product')
    )
);

UPDATE `core_config_data` SET `value` = '0' WHERE `path`  = 'catalog/price/scope';

DELETE FROM `eav_attribute` WHERE (`attribute_code` = 'base_currency') AND (`entity_type_id` = (
    SELECT `entity_type_id` FROM `eav_entity_type` WHERE `entity_type_code` = 'catalog_product'
));

DELETE FROM `core_resource` WHERE `code` = 'productbasecurrency_setup';
