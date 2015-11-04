<?php

$this->startSetup();


$this->run("
    
    INSERT INTO `cms_page` (
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
     `exclude_from_sitemap`) VALUES
('block_product_tab_thomas', 
'two_columns_right', 
'block_product_tab_thomas keywords', 
'block_product_tab_thomas', 
'no-route', 
NULL,'block_product_tab_thomas contenu', now(), now(), 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', 0)
");

$this->endSetup();



?>