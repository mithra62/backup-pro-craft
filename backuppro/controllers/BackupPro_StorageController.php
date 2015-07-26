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
class BackupPro_StorageController extends CraftController
{     
    public $form_data_defaults = array(
        'storage_location_name' => '',
        'storage_location_file_use' => '1',
        'storage_location_status' => '1',
        'storage_location_db_use' => '1',
        'storage_location_include_prune' => '1',
    );
    
    public function actionIndex()
    {
        $variables = array();
        $variables['can_remove'] = true;
        if( count($this->settings['storage_details']) <= 1 )
        {
            $variables['can_remove'] = false;
        }
        
		$variables['errors'] = $this->errors;
		$variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
		$variables['storage_details'] = $this->settings['storage_details'];
        $this->renderTemplate('backuppro/storage', $variables);
    }
    
    public function actionNew()
    {
        $engine = craft()->request->getParam('engine');
        $variables = array();
		$variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
		
		if( !isset($variables['available_storage_engines'][$engine]) )
		{
		    $engine = 'local';
		}
		
		$variables['storage_details'] = $this->settings['storage_details']; 

		$variables['storage_engine'] = $variables['available_storage_engines'][$engine];
		$variables['form_data'] = array_merge($this->settings, $variables['storage_engine']['settings'], $this->form_data_defaults);
		$variables['form_errors'] = array_merge($this->returnEmpty($this->settings), $this->returnEmpty($variables['storage_engine']['settings']), $this->form_data_defaults);
        
        if(craft()->request->getRequestType() == 'POST')
        {
            $data = craft()->request->getPost();
            $variables['form_data'] = $data;
            $settings_errors = $this->services['backup']->getStorage()->validateDriver($this->services['validate'], $engine, $data, $this->settings['storage_details']);
            if( !$settings_errors )
            {
                if( $this->services['backup']->getStorage()->getLocations()->setSetting($this->services['settings'])->create($engine, $variables['form_data']) )
                {
                    craft()->userSession->setFlash('notice', $this->services['lang']->__('storage_location_added'));
                    $this->redirect('backuppro/settings/storage');
                }
            }
            else
            {
                $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
                craft()->userSession->setError(Craft::t($this->services['lang']->__('fix_form_errors')));
            }
        }        
        
		$variables['errors'] = $this->errors;
		$variables['_form_template'] = false;
		if( $variables['storage_engine']['obj']->hasSettingsView() )
		{
            $variables['_form_template'] = 'backuppro/storage/drivers/_'.$engine;
		}
        $this->renderTemplate('backuppro/storage/new', $variables);
    }
    
    public function actionEdit()
    {
        $storage_id = craft()->request->getParam('id');
        if( empty($this->settings['storage_details'][$storage_id]) )
        {
            craft()->userSession->setFlash('error', $this->services['lang']->__('invalid_storage_id'));
            $this->redirect('backuppro/settings/storage');
        }
        
        $storage_details = $this->settings['storage_details'][$storage_id];

        $variables = array();
        $variables['storage_details'] = $storage_details;
		$variables['form_data'] = array_merge($this->form_data_defaults, $storage_details);
		$variables['form_errors'] = $this->returnEmpty($storage_details); //array_merge($storage_details, $this->form_data_defaults);
        $variables['errors'] = $this->errors;
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageOptions();
		$variables['storage_engine'] = $variables['available_storage_engines'][$storage_details['storage_location_driver']];
		$variables['_form_template'] = 'backuppro/storage/drivers/_'.$storage_details['storage_location_driver'];
		
		if(craft()->request->getRequestType() == 'POST')
		{
		    $data = craft()->request->getPost();
		    $variables['form_data'] = $data;
		    $settings_errors = $this->services['backup']->getStorage()->validateDriver($this->services['validate'], $storage_details['storage_location_driver'], $data, $this->settings['storage_details']);
		    if( !$settings_errors )
		    {
		        if( $this->services['backup']->getStorage()->getLocations()->setSetting($this->services['settings'])->update($storage_id, $variables['form_data']) )
		        {
		            craft()->userSession->setFlash('notice', $this->services['lang']->__('storage_location_updated'));
		            $this->redirect('backuppro/settings/storage');
		        }
		    }
            else
            {
                $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
                craft()->userSession->setError(Craft::t($this->services['lang']->__('fix_form_errors')));
            }
		}		
		
		
        $this->renderTemplate('backuppro/storage/edit', $variables);
    }
    
    public function actionRemove()
    {
        if( count($this->settings['storage_details']) <= 1 )
        {
            craft()->userSession->setFlash('error', $this->services['lang']->__('min_storage_location_needs'));
            $this->redirect('backuppro/settings/storage');
        }
        
        $storage_id = craft()->request->getParam('id');
        if( empty($this->settings['storage_details'][$storage_id]) )
        {
            craft()->userSession->setFlash('error', $this->services['lang']->__('invalid_storage_id'));
            $this->redirect('backuppro/settings/storage');
        }

        $storage_details = $this->settings['storage_details'][$storage_id];
        
        $variables = array();
        $variables['form_data'] = array('remove_remote_files' => '0');
        $variables['form_errors'] = array('remove_remote_files' => false);
        $variables['errors'] = $this->errors;
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
        $variables['storage_engine'] = $variables['available_storage_engines'][$storage_details['storage_location_driver']];
        $variables['storage_details'] = $storage_details;
        
        if(craft()->request->getRequestType() == 'POST')
        {
            $data = craft()->request->getPost();
            $backups = $this->services['backups']->setBackupPath($this->settings['working_directory'])
                                                 ->getAllBackups($this->settings['storage_details'], $this->services['backup']->getStorage()->getAvailableStorageDrivers());
            
            if( $this->services['backup']->getStorage()->getLocations()->setSetting($this->services['settings'])->remove($storage_id, $data, $backups) )
            {
                craft()->userSession->setFlash('notice', $this->services['lang']->__('storage_location_removed'));
                $this->redirect('backuppro/settings/storage');
            }
		    else
		    {
		        $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
		        craft()->userSession->setError(Craft::t($this->services['lang']->__('fix_form_errors')));
		    }
        }
        
        $this->renderTemplate('backuppro/storage/remove', $variables);
    }
}