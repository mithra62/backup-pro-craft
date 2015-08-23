<?php  
/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		3.0
 * @filesource 	./backuppro/consolecommands/BackupCommand.php
 */
 
namespace Craft;

use mithra62\BackupPro\Platforms\Console\Craft AS CraftCommand;

/**
 * Craft - Backup Pro Cron Console Commands
 *
 * Contains the methods for controlling Backup Pro on the Console
 *
 * @package 	mithra62\BackupPro
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupCommand extends CraftCommand
{   
    /**
     * Backups up the database
     * @param string $notify Set to "yes" to send an email on backup completion
     */
    public function actionDatabase( $notify="no" )
    {
        $error = $this->services['errors'];
        $backup = $this->services['backup']->setStoragePath($this->settings['working_directory']);
        $errors = $error->clearErrors()->checkStorageLocations($this->settings['storage_details'])->checkBackupDirs($backup->getStorage())->getErrors();
        
        $db_info = $this->platform->getDbCredentials();
        $backup_paths = array();
        $backup_paths['database'] = $backup->setDbInfo($db_info)->database($db_info['database'], $this->settings, $this->services['shell']);

        $this->cleanup($backup)->notify($notify, $backup_paths, $backup);
    }
    
    /**
     * Backups up the files
     * @param string $notify Set to "yes" to send an email on backup completion
     */    
    public function actionFile( $notify="no" )
    {
        $error = $this->services['errors'];
        $backup = $this->services['backup']->setStoragePath($this->settings['working_directory']);
        $errors = $error->clearErrors()->checkStorageLocations($this->settings['storage_details'])->checkBackupDirs($backup->getStorage())->getErrors();
        
        $backup_paths = array();
        $backup_paths['files'] = $backup->files($this->settings, $this->services['files'], $this->services['regex']); 
        $this->cleanup($backup)->notify($notify, $backup_paths, $backup);
    } 
    
    public function actionIntegrity()
    {
        @ini_set('memory_limit', -1);
        @set_time_limit(0); //limit the time to 1 hours
        
        //grab the backup and storage objects and set them up
        $backup = $this->services['backups']->setLocations($this->settings['storage_details'])
                       ->setBackupPath($this->settings['working_directory']);
        $storage = $this->services['backup']->setStoragePath($this->settings['working_directory']);
        $backup->getIntegrity()->setFile($this->services['files'])->setStorage($storage->getStorage());
        //first, check the backup state
        //ee()->integrity_agent->monitor_backup_state();
        
        //now check the backups and ensure they're all valid
        $backups = $backup->getAllBackups($this->settings['storage_details']);
        $type = ($this->settings['last_verification_type'] == 'database' ? 'files' : 'database') ;
        
        //ok, this is a little bash over the head to FUCING ENSURE we're NOT using the production db for database testing!
        //THAT WOULD BE BAD. So... bad... uh... coooooodddddddde...
        if( $type == 'database' && empty($this->settings['db_verification_db_name']) )
        {
            $type = 'files';
        }
        
        $total = 0;
        foreach($backups[$type] AS $details)
        {
            if( empty($details['verified']) || $details['verified'] == '0')
            {
                if($type == 'files')
                {
                    $file = $storage->getStorage()->getFileBackupNamePath($details['details_file_name']);
                }
                else
                {
                    $file = $storage->getStorage()->getDbBackupNamePath($details['details_file_name']);
                    $backup->getIntegrity()->setDbConf($this->platform->getDbCredentials())
                           ->setTestDbName($this->settings['db_verification_db_name'])
                           ->setShell($this->services['shell'])
                           ->setSettings($this->settings)
                           ->setBackupInfo($details)
                           ->setRestore($this->services['restore']);
        
                }
                 
                $backup_info = $this->services['backups']->setLocations($this->settings['storage_details'])->getBackupData($file);
                if( $backup->getIntegrity()->checkBackup($backup_info, $type) )
                {
                    $status = 'success';
                    $total++;
                }
                else
                {
                    $status = 'fail';
                }
        
                $path = rtrim($this->settings['working_directory'], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$type;
                $this->services['backup']->getDetails()->addDetails($details['file_name'], $path, array('verified' => $status));
            }
            	
            if( $total >= $this->settings['total_verifications_per_execution'])
            {
                break;
            }
        }
        
        $data = array(
            'last_verification_type' => $type,
            'last_verification_time' => time()
        );
        $this->services['settings']->update($data);
        
        exit;
    }
    
    /**
     * Runs the Cleanup routines
     * @param \mithra62\BackupPro\Backup $backup
     * @return \Craft\BackupCommand
     */
    private function cleanup($backup)
    {
        $backups = $this->services['backups']->setBackupPath($this->settings['working_directory'])
                                             ->getAllBackups($this->settings['storage_details']);
        $backup->getStorage()->getCleanup()->setStorageDetails($this->settings['storage_details'])
                                           ->setBackups($backups)
                                           ->setDetails($this->services['backups']->getDetails())
                                           ->autoThreshold($this->settings['auto_threshold'])
                                           ->counts($this->settings['max_file_backups'], 'files')
                                           ->duplicates($this->settings['allow_duplicates']);      
        
        return $this;
    }
    
    /**
     * Runs the notify routines
     * @param string $notify
     * @param array $backup_paths
     * @param \mithra62\BackupPro\Backup $backup
     * @return \Craft\BackupCommand
     */
    private function notify($notify, array $backup_paths, $backup)
    {
        if( count($this->settings['cron_notify_emails']) >= 1 ) 
        {
            $notify = $this->services['notify'];
            $notify->getMail()->setConfig($this->platform->getEmailConfig());
            foreach($backup_paths As $type => $path)
            {
                $cron = array($type => $path);
                $notify->setBackup($backup)->sendBackupCronNotification($this->settings['cron_notify_emails'], $cron, $type);
            }
        }
        return $this;
    }
}