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
}
catch(Exception $e)
{
    //echo "Installation failed,please re-install again.";
    header("Location: error.php?param=".sapp_Global::_encrypt('error')."");
}
}	
?>
