<?php
/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */

$this->startSetup();

$this->run("
    DROP TABLE IF EXISTS {$this->getTable('productflow')};
    CREATE TABLE {$this->getTable('productflow')} (
        `flow_id` int(11) unsigned NOT NULL auto_increment,
        `title` varchar(255) NOT NULL default '',
        `note` text,        
        `json_data` text,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        `deleted_at` datetime,        
        PRIMARY KEY (`flow_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
");

$this->endSetup(); 