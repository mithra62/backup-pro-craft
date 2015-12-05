<?php  
/**
 * mithra62 - Backup Pro
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/projects/view/backup-pro/
 * @version		3.0
 * @filesource 	./craft/plugins/backuppro/BackupProPlugin.php
 */
 
namespace Craft;

require_once 'vendor/autoload.php';

use Craft\DbCommand;
use mithra62\BackupPro\Platforms\Craft AS Platform;
use mithra62\BackupPro\BackupPro;
use mithra62\Twig\m62LangTwigExtension;
use mithra62\BackupPro\Traits\Controller;
use mithra62\Exceptions\PlatformsException;

/**
 * Backup Pro - Plugin Object
 *
 * Basic Craft Class
 *
 * @package 	Craft
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupProPlugin extends BasePlugin implements BackupPro 
{   
    use Controller;
    
    /**
     * The version of the plugin
     * @var float
     */
    private $version = '';
    
    /**
     * Product Settings
     * @var array
     */
    protected $_settings = array();

    /**
     * The abstracted platform object
     * @var \mithra62\Platforms\Craft
     */
    protected $platform = null;
    
    /**
     * @ignore
     */
    public function __construct()
    {
        $this->initController();
        
        try {
            $this->platform = new Platform();
            $this->m62->setDbConfig($this->platform->getDbCredentials());
            $this->m62->setService('platform', function($c) {
                return $this->platform;
            });
        }
        catch(PlatformsException $e)
        {
            $e->logEmergency($e->__toString());
        }
        
        $this->version = self::version;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Craft\BasePlugin::onAfterInstall()
     */
    public function onAfterInstall()
    {
        $cols = array(
            'setting_key' => "varchar(60) NOT NULL DEFAULT ''",
            'setting_value' => "text NOT NULL",
            'serialized' => "int(1) DEFAULT '0'"
        );
        
        $db = new DbCommand(craft()->db);
        $db->createTable('backup_pro_settings', $cols, null, true, false);
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Craft\BasePlugin::onBeforeUninstall()
     */
    public function onBeforeUninstall()
    {

        $db = new DbCommand(craft()->db);
        $db->dropTableIfExists('backup_pro_settings');
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \Craft\BaseComponentType::getName()
     */
    public function getName()
    {
         return Craft::t('Backup Pro');
    }

    /**
     * (non-PHPdoc)
     * @see \Craft\IPlugin::getVersion()
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * (non-PHPdoc)
     * @see \Craft\IPlugin::getDeveloper()
     */
    public function getDeveloper()
    {
        return 'mithra62';
    }
    
    /**
     * Returns the icon path
     */
    public function getIconPath()
    {
        return craft()->path->getPluginsPath().'myplugin/resources/icon.svg';
    }
    
    public function getDocumentationUrl()
    {
        return 'https://mithra62.com/docs/table-of-contents/backup-pro';
    }

    /**
     * (non-PHPdoc)
     * @see \Craft\IPlugin::getDeveloperUrl()
     */
    public function getDeveloperUrl()
    {
        return 'https://mithra62.com';
    }
    
    /**
     * Adds the Twig Extension
     * @return \mithra62\Twig\m62LangTwigExtension
     */
    public function addTwigExtension()
    {
        Craft::import('plugins.backuppro.mithra62.Twig.m62LangTwigExtension');
        $plugin = new m62LangTwigExtension($this->services['lang'], $this->services['files'], $this->services['settings'], $this->services['encrypt'], $this->platform);
        return $plugin;
    } 
    
    /**
     * (non-PHPdoc)
     * @see \Craft\BasePlugin::getSettingsUrl()
     */
    public function getSettingsUrl()
    {
        return UrlHelper::getUrl('/admin/backuppro');
    }  
    
    /**
     * (non-PHPdoc)
     * @see \Craft\BasePlugin::hasCpSection()
     */
    public function hasCpSection()
    {
        return false;
    } 
    
    public function getSchemaVersion()
    {
        return '1.0';
    }
    
    /**
     * Sets up the Controller Routes
     * @return multitype:multitype:string
     */
    public function registerCpRoutes()
    {
        return array(
            //view backups
            'backuppro' => array('action' => 'backupPro/dashboard'),
            'backuppro\/database_backups' => array('action' => 'backupPro/dbBackups'),
            'backuppro\/file_backups' => array('action' => 'backupPro/fileBackups'),

            //execute backup
            'backuppro\/backup' => array('action' => 'backupPro/backup/confirm'),
            'backuppro\/backup\/files' => array('action' => 'backupPro/backup/confirm'),
            'backuppro\/backup\/db' => array('action' => 'backupPro/backup/confirm'),
            'backuppro\/backup\/exec\/file' => array('action' => 'backupPro/backup/file'),
            'backuppro\/backup\/exec\/db' => array('action' => 'backupPro/backup/database'),

            //manage backups
            'backuppro\/download' => array('action' => 'backupPro/manage/download'),
            'backuppro\/delete\/confirm' => array('action' => 'backupPro/manage/deleteConfirm'),
            'backuppro\/delete\/backups' => array('action' => 'backupPro/manage/deleteBackups'),
            'backuppro\/delete\/l' => array('action' => 'backupPro/manage/l'),
            'backuppro\/update\/note' => array('action' => 'backupPro/manage/note'),

            //automation
            'backuppro\/cron\/backup' => array('action' => 'backupPro/cron/backup'),
            'backuppro\/cron\/ia' => array('action' => 'backupPro/cron/integrityAgent'),

            //restoration
            'backuppro\/restore\/database' => array('action' => 'backupPro/restore/database'),
            'backuppro\/restore\/confirm' => array('action' => 'backupPro/restore/confirm'),
            
            //settings
            'backuppro\/settings' => array('action' => 'backupPro/settings/edit'),
            
            //storage
            'backuppro\/settings\/storage' => array('action' => 'backupPro/storage'),
            'backuppro\/settings\/storage\/new' => array('action' => 'backupPro/storage/new'),
            'backuppro\/settings\/storage\/edit' => array('action' => 'backupPro/storage/edit'),
            'backuppro\/settings\/storage\/remove' => array('action' => 'backupPro/storage/remove'),
        );
    }    
}