<?php  
/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		3.0
 * @filesource 	./backuppro/controllers/BackupPro_BackupController.php
 */

namespace Craft;

use mithra62\BackupPro\Platforms\Controllers\Craft AS CraftController;
use mithra62\Traits\Log;
use mithra62\BackupPro\Platforms\Controllers\Craft\Backup;

/**
 * Craft - Backup Pro Backup Controller
 *
 * Contains all the actions for manually executing a backup
 *
 * @package 	mithra62\BackupPro
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupPro_BackupController extends CraftController
{   
    use Log, Backup;
    
    /**
     * Manually execute a database backup
     */
    public function actionDatabase()
    {
        $this->backup_database();
    }     
    
    /**
     * Manually execute a file backup
     */
    public function actionFile()
    {
        $this->backup_files();
    } 
    
    public function actionConfirm()
    {
        $this->backup(); 
    }
}