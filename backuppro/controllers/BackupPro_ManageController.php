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
use mithra62\BackupPro\Platforms\Controllers\Craft\Manage;

/**
 * Craft - Backup Pro Backup Controller
 *
 * Contains all the actions for manually executing a backup
 *
 * @package 	mithra62\BackupPro
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupPro_ManageController extends CraftController
{   
    use Manage;
    
    /**
     * Download a backup action
     */
    public function actionDownload()
    {
        $this->download();
    }    
    
    /**
     * AJAX Action for updating a backup note
     */
    public function actionNote()
    {
        $this->update_backup_note();
    }
    
    /**
     * Delete Backup Confirmation Action
     */
    public function actionDeleteConfirm()
    {
        $this->delete_backup_confirm(); 
    }
    
    /**
     * Delete Backup Action
     */
    public function actionDeleteBackups()
    {
        $this->delete_backups();
    }
    
    /**
     * Validates the POST'd backup data and returns the clean array
     * @param array $delete_backups
     * @param string $type
     * @return multitype:array
     */
    private function validateBackups($delete_backups, $type)
    {
        if(!$delete_backups || count($delete_backups) == 0)
        {
            craft()->userSession->setFlash('error', $this->services['lang']->__('backups_not_found'));
            $this->redirect('backuppro');
        }
        
        $encrypt = $this->services['encrypt'];
        $backups = array();
        
        $locations = $this->settings['storage_details'];
        $drivers = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
        foreach($delete_backups AS $file_name)
        {
            $file_name = $encrypt->decode(urldecode($file_name));
            if( $file_name != '' )
            {
                $path = rtrim($this->settings['working_directory'], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$type;
                $file_data = $this->services['backup']->getDetails()->getDetails($file_name, $path);
                $file_data = $this->services['backups']->getBackupStorageData($file_data, $locations, $drivers);
                $backups[] = $file_data;
            }
        }
        
        if(count($backups) == 0)
        {
            craft()->userSession->setFlash('error', $this->services['lang']->__('backups_not_found'));
            $this->redirect('backuppro');
        }
        
        return $backups;
    }
}