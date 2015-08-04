<?php  
/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		3.0
 * @filesource 	./backuppro/controllers/BackupPro_RestoreController.php
 */

namespace Craft;

use mithra62\BackupPro\Platforms\Controllers\Craft AS CraftController;
use mithra62\BackupPro\Platforms\Controllers\Craft\Restore;

/**
 * Craft - Backup Pro Restore Controller
 *
 * Contains all the actions for manually executing a backup restore
 *
 * @package 	mithra62\BackupPro\Restore
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupPro_RestoreController extends CraftController
{   
    use Restore;
    
    /**
     * The Backup Cron
     */
    public function actionConfirm()
    {
        $this->restore_confirm();      
    }    
    
    /**
     * The Integrity Agent Cron
     */
    public function actionDatabase()
    {
        $this->restore_database();
    }    
}