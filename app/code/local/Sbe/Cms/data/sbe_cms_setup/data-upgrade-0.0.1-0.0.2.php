<?php

$content1 = 'blabla1';
$content2 = 'blabla2';
$content3 = 'blabla3';

$cmsBlocks = array(
    array(
    'title'         => 'test1',
    'identifier'    => 'test1',
    'content'       => $content1,
    'is_active'     => 1,
    'stores'        => array(0)
),
array(
    'title'         => 'test2',
    'identifier'    => 'test2',
    'content'       => $content2,
    'is_active'     => 1,
    'stores'        => array(0)
),
    array(
    'title'         => 'test3',
    'identifier'    => 'test3',
    'content'       => $content3,
    'is_active'     => 1,
    'stores'        => array(0)
)
        );


/**
 * Insert block cms 
 */
foreach ($cmsBlocks as $cmsBlock) {
    Mage::getModel('cms/block')->setData($cmsBlock)->save();
}