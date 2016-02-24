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
        $section = ( $this->platform->getPost('section') != '' ? $this->platform->getPost('section') : 'general' );
        $variables = array('form_data' => $this->settings, 'form_errors' => $this->returnEmpty($this->settings));
        $variables['form_data']['cron_notify_emails'] = implode("\n", $this->settings['cron_notify_emails']);
        $variables['form_data']['exclude_paths'] = implode("\n", $this->settings['exclude_paths']);
        $variables['form_data']['backup_file_location'] = implode("\n", $this->settings['backup_file_location']);
        $variables['form_data']['db_backup_archive_pre_sql'] = implode("\n", $this->settings['db_backup_archive_pre_sql']);
        $variables['form_data']['db_backup_archive_post_sql'] = implode("\n", $this->settings['db_backup_archive_post_sql']);
        $variables['form_data']['db_backup_execute_pre_sql'] = implode("\n", $this->settings['db_backup_execute_pre_sql']);
        $variables['form_data']['db_backup_execute_post_sql'] = implode("\n", $this->settings['db_backup_execute_post_sql']);
        $variables['form_data']['backup_missed_schedule_notify_emails'] = implode("\n", $this->settings['backup_missed_schedule_notify_emails']);
        if( \Craft\craft()->request->getRequestType() == 'POST' )
        {
            $data = \Craft\craft()->request->getPost();
            $variables['form_data'] = array_merge($this->multi, $data);
            $backup = $this->services['backups'];
            $backups = $backup->setBackupPath($this->settings['working_directory'])->getAllBackups($this->settings['storage_details']);
            $data['meta'] = $backup->getBackupMeta($backups);
            $extra = array('db_creds' => $this->platform->getDbCredentials());
            $settings_errors = $this->services['settings']->validate($data, $extra);
            if( !$settings_errors )
            {            
                if( $this->services['settings']->update($data) )
                {
                    \Craft\craft()->userSession->setFlash('notice', $this->services['lang']->__('settings_updated'));
                    $this->redirect($this->platform->getCurrentUrl());
                }
            }
            else
            {
                $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
                \Craft\craft()->userSession->setError(\Craft\Craft::t($this->services['lang']->__('fix_form_errors')));
            }
        }
        
        $variables['section']= $section;
        $variables['tab_set'] = 'settings';
        $variables['selectedSubnavItem'] = 'settings';
        $variables['db_tables'] = $this->services['db']->getTables();
        $variables['backup_cron_commands'] = $this->platform->getBackupCronCommands($this->settings);
        $variables['ia_cron_commands'] = $this->platform->getIaCronCommands($this->settings);
        $variables['errors'] = $this->errors;
        $variables['threshold_options'] = $this->services['settings']->getAutoPruneThresholdOptions();
        $variables['available_db_backup_engines'] = $this->services['backup']->getDataBase()->getAvailableEnginesOptions();
        $variables['rest_api_route_entry'] = $this->platform->getRestApiRouteEntry($this->settings);
        $this->renderTemplate('backuppro/settings', $variables);      
    }    
}