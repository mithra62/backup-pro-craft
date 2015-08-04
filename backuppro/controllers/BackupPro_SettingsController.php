<?php  
/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		3.0
 * @filesource 	./backuppro/controllers/BackupPro_SettingsController.php
 */

namespace Craft;

use mithra62\BackupPro\Platforms\Controllers\Craft AS CraftController;
use mithra62\BackupPro\Platforms\Controllers\Craft\Settings;

/**
 * Craft - Backup Pro Settings Controller
 *
 * Contains all the actions for dealing with the Settings system with Craft
 *
 * @package 	mithra62\BackupPro
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupPro_SettingsController extends CraftController
{
    use Settings;
    
    /**
     * The options that're array data
     * @var array
     */
    protected $multi = array(
        'db_backup_ignore_tables' => '', 
        'db_backup_ignore_table_data' => ''
    );
    
    /**
     * The Edit Settings view
     */
    public function actionEdit()
    {
        $this->settings();        
    }    
}