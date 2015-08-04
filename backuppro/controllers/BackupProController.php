<?php  
/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		3.0
 * @filesource 	./backuppro/controllers/BackupProController.php
 */
 
namespace Craft;

use mithra62\BackupPro\Platforms\Controllers\Craft AS CraftController;
use mithra62\BackupPro\Platforms\Controllers\Craft\Dashboard;

/**
 * Craft - Backup Pro View Backups Controller
 *
 * Contains all the actions for viewing Backups
 *
 * @package 	mithra62\BackupPro
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupProController extends CraftController
{   
    use Dashboard;
    
    /**
     * The Dashboard view
     */
    public function actionDashboard()
    {
        $this->index();
    }
    
    /**
     * The view database backups view
     */
    public function actionDbBackups()
    {
        $this->db_backups();
    }
    
    /**
     * The view file backups view
     */
    public function actionFileBackups() 
    {
        $this->file_backups();
    }
}