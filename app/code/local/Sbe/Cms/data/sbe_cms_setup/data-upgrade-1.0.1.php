<?php


$installer->run("


SELECT * from `{$this->getTable('cms_page')}`
WHERE id = LAST_INSERT_ID()



UPDATE `{$this->getTable('cms_page')}`
SET content="<a href="">"
WHERE id = 2


");

?>