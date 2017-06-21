<?php
/*********************************************************************************  
 *  Sentrifugo is an open source human resource management system.
 *  Copyright (C) 2014 Sapplica
 *   
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentirfugo Support <support@sentrifugo.com>
 ********************************************************************************/
defined('CODEVERSION')|| define('CODEVERSION', '3.2');
$filepath = 'install/index.php';
if(file_exists($filepath))
{
header("Location: install/index.php");  
}else
{
   
try
{
// constants 
    
require_once 'public/constants.php';
require_once 'public/site_constants.php';
require_once 'public/email_constants.php';
require_once 'public/emptabconfigure.php';
require_once 'public/db_constants.php';
require_once 'public/application_constants.php';
require_once 'public/mail_settings_constants.php';
require_once 'application/modules/default/library/sapp/Global.php';
require_once 'public/text_constants.php';
/* Query to fetch db version and then comparing with code version */
try {
	$mysqlPDO = new PDO('mysql:host='.SENTRIFUGO_HOST.';dbname='.SENTRIFUGO_DBNAME.'',SENTRIFUGO_USERNAME, SENTRIFUGO_PASSWORD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));   
    $date= gmdate("Y-m-d H:i:s");
    $stmt =  $mysqlPDO->prepare("SHOW TABLES LIKE 'main_patches_version'");
    $stmt->execute();
    $count = $stmt->fetch();
    if(!empty($count))
    {
    	$dbversion = '';
		$dbid = '';
		$codeversion = '';
		$codeid = '';
	    $stmt1 = $mysqlPDO->prepare("select * from main_patches_version where isactive=1 ");
	    $stmt2 = $mysqlPDO->prepare("select * from main_patches_version where version ='".CODEVERSION."' and isactive=1  ");
	    $stmt1->execute();
	    $stmt2->execute();
	    $dbdataArray = $stmt1->fetch();
	    $codedataArray = $stmt2->fetch();
	    if(!empty($dbdataArray))
	    {
	    	$dbversion = $dbdataArray['version'];
	    	$dbid = $dbdataArray['id'];
	    }
	    
	    if(!empty($codedataArray))	
	    {
	    	$codeversion = $codedataArray['version'];
	    	$codeid = $codedataArray['id'];
	    }else
		{
			$codeversion = CODEVERSION;
		}
    }else 
    {
    	header("Location: error.php?param=".sapp_Global::_encrypt('tbl')."");exit;
    }
}
catch (PDOException $e)
{
	 header("Location: error.php?param=".sapp_Global::_encrypt('db')."");exit;
}
/**
 *  If both codeversion and dbversion are equal then application will load.
 *  Else it will be redirected to upgrade.php using form post. 
*/
	if(isset($dbid) && isset($codeid))
		{
			if($dbid == $codeid)
			{
				// Ensure library/ is on include_path
				set_include_path(implode(PATH_SEPARATOR, array(
				    realpath(APPLICATION_PATH . '/library'),
				    get_include_path(),
				)));
				
				
				
				/** Zend_Application */
				require_once 'Zend/Application.php';
				
				
				    // Create application, bootstrap, and run
				    $application = new Zend_Application(
				        APPLICATION_ENV,
				        APPLICATION_PATH . '/configs/application.ini'
				    );
				    $application->bootstrap()
				                ->run();
			}else 
			{?>
				
				<form name="upgrade" id="upgrade" action="upgrade.php" method="post" class="frm_install">
				       <input type = 'hidden' value='<?php echo $codeversion;?>' name='codeversion' id='codeversion'>
				       <input type = 'hidden' value='<?php echo $dbversion;?>' name='dbversion' id='dbversion'>
				       <input type = 'hidden' value='<?php echo $dbid;?>' name='dbid' id='dbid'>
				       <input type = 'hidden' value='<?php echo $codeid;?>' name='codeid' id='codeid'>
				</form>
				<script>
					document.getElementById('upgrade').submit();
				</script>
			<?php }   
		} else
		{

			header("Location: error.php?param=".sapp_Global::_encrypt('db')."");exit;
		}            
}
catch(Exception $e)
{    

	header("Location: error.php?param=".sapp_Global::_encrypt('error')."");exit;
}
}	
?>
