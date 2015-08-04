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
use mithra62\BackupPro\Platforms\Controllers\Craft\Storage;

/**
 * Craft - Backup Pro Settings Controller
 *
 * Contains all the actions for dealing with the Settings system with Craft
 *
 * @package 	mithra62\BackupPro
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupPro_StorageController extends CraftController
{     
    use Storage;
    
    public function actionIndex()
    {
        $this->view_storage();
    }
    
    public function actionNew()
    {
        $this->new_storage();
    }
    
    public function actionEdit()
    {
        $this->edit_storage();
    }
    
    public function actionRemove()
    {
        $this->remove_storage();
    }
}