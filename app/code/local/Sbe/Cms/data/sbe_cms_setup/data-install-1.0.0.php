<?php

$this->startSetup();

$cmsContent='Contenu de la page test';

$this->run("
    
    INSERT INTO `cms_page` (
    `page_id`,
     `title`, 
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
     `custom_root_template`, 
     `custom_layout_update_xml`, 
     `custom_theme_from`, 
     `custom_theme_to`, 
     `meta_title`, 
     `mageworx_hreflang_identifier`, 
     `meta_robots`, 
     `exclude_from_sitemap`) 
     VALUES
(1, 
'PAGETEST', 
'two_columns_right', 
'PAGETEST keywords', 
'PAGETEST', 
'no-route', 
NULL,'PAGETEST contenu', now(), now(), 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', 0),


    
    ");

$this->endSetup();

?>