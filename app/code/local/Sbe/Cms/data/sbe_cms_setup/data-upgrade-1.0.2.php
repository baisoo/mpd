<?php


$cmsContent="Contenu du bloc test Thomas";

$installer->run("
    
    INSERT INTO `{$this->getTable('cms_page')}`
        (`title`,
        `root_template`,
        `meta_keywords`,
        `meta_description`,
        `identifier`,
        `content_heading`,
        `content`,
            `creation_time`,
            `update_time`,
            `is_active`,
            `sort_order`,
            `layout_update_xml`,
            `custom_theme`,
            `custom_theme_from`,
            `custom_theme_to`)
    VALUES (
    'block_product_tab_thomas',
    'two_columns_right',
    'block_product_tab_thomas'
    'block_product_tab_thomas',
    'block_product_tab_thomas',
    'block_product_tab_thomas',
     $cmsContent,
     now(),
     now(),
     1,
     0,
     NULL,
     NULL,
     NULL,
     NULL);

    INSERT INTO `{$this->getTable('cms/page_store')}` (`page_id`,
`store_id`) VALUES
    (LAST_INSERT_ID(), ".$storeId.");
    ");
}
?>