<?php  
/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		3.0
 * @filesource 	./backuppro/controllers/BackupPro_CronController.php
 */

namespace Craft;

use mithra62\BackupPro\Platforms\Controllers\Craft AS CraftController;
use JaegerApp\Traits\Log;

/**
 * Craft - Backup Pro Rest Controller
 *
 * Contains all the actions for accepting URL requests through Craft
 *
 * @package 	mithra62\BackupPro
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupPro_RestController extends CraftController
{   
    use Log;
    
    /**
     * The methods anyone can access
     * @var array
     */
    protected $allowAnonymous = array(
        'actionApi'
    );
    
    /**
     * The Backup REST API Server 
     * 
     * You have to update your config/general.php file to disable 
     * CSRF protection on REST requests. Something like the below;
     * 
     * 'enableCsrfProtection' => ((!isset($_REQUEST['p']) || $_REQUEST['p'] != 'actions/backupPro/rest/api') ? true : false),
     * 
     * Don't worry! Every API request HAS to include an API token so you're STILL secure :)
     * It's just Yii (and Craft) are too focused for their platform 
     */
    public function actionApi()
    {
        //disable the debugging since we're JSON only baby
        craft()->log->removeRoute('WebLogRoute');
        craft()->log->removeRoute('ProfileLogRoute');
        
        //start it up
        $_SERVER['REQUEST_URI'] = '/backup_pro/api'.$this->platform->getPost('api_method');
        $this->services['rest']->setPlatform($this->platform)->getServer()->run();
        exit;        
    }    
}