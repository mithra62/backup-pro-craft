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
    use Log;
    
    /**
     * Manually execute a database backup
     */
    public function actionDatabase()
    {
        $error = $this->services['errors'];
        $backup = $this->services['backup']->setStoragePath($this->settings['working_directory']);
        $errors = $error->clearErrors()->checkStorageLocations($this->settings['storage_details'])->checkBackupDirs($backup->getStorage())->getErrors();
        if( $error->totalErrors() == '0' )
        {
            @session_write_close();
            ini_set('memory_limit', -1);
            set_time_limit(0);
        
            $db_info = $this->platform->getDbCredentials();
            if( $backup->setDbInfo($db_info)->database($db_info['database'], $this->settings, $this->services['shell']) )
            {
                $backups = $this->services['backups']->setBackupPath($this->settings['working_directory'])
                                ->getAllBackups($this->settings['storage_details']);
        
                $backup->getStorage()->getCleanup()->setStorageDetails($this->settings['storage_details'])
                       ->setBackups($backups)
                       ->setDetails($this->services['backups']->getDetails())
                       ->autoThreshold($this->settings['auto_threshold'])
                       ->counts($this->settings['max_db_backups'])
                       ->duplicates($this->settings['allow_duplicates']);
        
                \Craft\craft()->userSession->setFlash('notice', 'Backup Complete!');
                $this->redirect('backuppro/database_backups');
            }
        }
        else
        {
            \Craft\craft()->userSession->setFlash('notice', $error->getError());
            $this->redirect('backuppro');
        }
        
        exit;
    }     
    
    /**
     * Manually execute a file backup
     */
    public function actionFile()
    {
        $error = $this->services['errors'];
        $backup = $this->services['backup']->setStoragePath($this->settings['working_directory']);
        $errors = $error->clearErrors()->checkStorageLocations($this->settings['storage_details'])->checkBackupDirs($backup->getStorage())->checkFileBackupLocations($this->settings['backup_file_location'])->getErrors();
        if( $error->totalErrors() == '0' )
        {
            @session_write_close();
            ini_set('memory_limit', -1);
            set_time_limit(0);
            if( $backup->files($this->settings, $this->services['files'], $this->services['regex']) )
            {
                $backups = $this->services['backups']->setBackupPath($this->settings['working_directory'])
                ->getAllBackups($this->settings['storage_details']);
        
                $backup->getStorage()->getCleanup()->setStorageDetails($this->settings['storage_details'])
                ->setBackups($backups)
                ->setDetails($this->services['backups']->getDetails())
                ->autoThreshold($this->settings['auto_threshold'])
                ->counts($this->settings['max_file_backups'], 'files')
                ->duplicates($this->settings['allow_duplicates']);
        
                \Craft\craft()->userSession->setFlash('notice', 'Backup Complete!');
                $this->redirect('backuppro/file_backups');
                exit;
            }
        }
        else
        {
            \Craft\craft()->userSession->setFlash('notice', $error->getError());
            $this->redirect('backuppro');
            exit;
        }
        
    } 
    
    public function actionConfirm()
    {
        $type = \Craft\craft()->request->getParam('type');
        $proc_url = false;
        switch($type)
        {
            case 'database':
                $proc_url = 'backuppro/backup/exec/db';
                $selectedTab = 'backup_db';
                break;
            case 'files':
                $proc_url = 'backuppro/backup/exec/file';
                $selectedTab = 'backup_files';
                break;
        }
        
        if(!$proc_url)
        {
            \Craft\craft()->userSession->setFlash('notice', $this->services['lang']->__('can_not_backup'));
            $this->redirect('backuppro');
            exit;
        }
        
        $variables = array(
            'proc_url' => $proc_url,
            'errors' => $this->errors,
            'backup_type' => $type,
            'selectedTab' => $selectedTab
        );
        $template = 'backuppro/backup';
        $this->renderTemplate($template, $variables);
    }
}