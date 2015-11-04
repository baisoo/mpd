<?php

$this->startSetup();


$cmsContent="Contenu de la page test";

$this->run("
    
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
    'PAGE TEST',
    'two_columns_right',
    'PAGE TEST SET UP'
    'PAGE TEST SET UP',
    'page_test',
    'PAGE TEST',
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

$this->endSetup();

?>