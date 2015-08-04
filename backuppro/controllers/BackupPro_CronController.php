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
use mithra62\BackupPro\Platforms\Controllers\Craft\Cron;

/**
 * Craft - Backup Pro Cron Controller
 *
 * Contains all the actions for accepting URL requests through Craft
 *
 * @package 	mithra62\BackupPro
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupPro_CronController extends CraftController
{
    use Cron;
    
    /**
     * The methods anyone can access
     * @var array
     */
    protected $allowAnonymous = array(
        'actionBackup', 
        'actionIntegrityAgent'
    );
    
    /**
     * The Backup Cron
     */
    public function actionBackup()
    {
        $this->cron();
    }    
    
    /**
     * The Integrity Agent Cron
     */
    public function actionIntegrityAgent()
    {
		$this->integrity();
    }    
}