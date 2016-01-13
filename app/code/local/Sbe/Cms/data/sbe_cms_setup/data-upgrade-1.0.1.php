<?php

$this->startSetup();


$this->run("

INSERT INTO `cms_block` (`title`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`) 
VALUES
('block_product_tab_thomas', 'block_product_tab_thomas', 'block_product_tab_thomas contenu', now(), now(), 1);


");

$this->endSetup();



?>