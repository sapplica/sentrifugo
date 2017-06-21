<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/ 
$filepath = 'install';  
require_once 'public/db_constants.php';
require_once 'public/constants.php';
require_once 'public/application_constants.php';
require_once 'public/mail_settings_constants.php';
if(file_exists($filepath))
	require 'install/PHPMailer/PHPMailerAutoload.php';
else
	require 'application/modules/default/library/PHPMailer/PHPMailerAutoload.php';
?>
<?php 
if(!empty($_POST))
{
	$msgarray = array();
    if(isset($_POST['btnfinish']) && isset($_POST['mailcontent']))
    {
    		try
    		{
                    $mysqlPDO = new PDO('mysql:host='.SENTRIFUGO_HOST.';dbname='.SENTRIFUGO_DBNAME.'',SENTRIFUGO_USERNAME, SENTRIFUGO_PASSWORD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
					if (!$mysqlPDO)
					{
		                            $msgarray['error'] = 'Could not connect to specified database' ;
					}
					else
					{
						    $generatedPswd = uniqid();
							$encodedPswd = md5($generatedPswd);
							$query = "update main_users  set emppassword = '".$encodedPswd."' where emailaddress = '".SUPERADMIN_EMAIL."' and id= 1 ";
							$mysqlPDO->query($query);
							$mail = sendconfirmationmail($_POST['mailcontent'],$generatedPswd);
							$renamefolder = renamefolder();
							if($mail != 'true')
							{
								if($renamefolder == 'true')
								{
								  $msgarray['error'] = "<div>Problem encountered while sending mail to ".SUPERADMIN_EMAIL."</div><br/>
														<div>Login Credentials for ".APPLICATION_NAME."</div><br/>
														<div style='color: rgb(105, 145, 61); font-weight: 400; margin-bottom: 14px; margin-top: 8px;'>Username : empp0001</div>
														<div style='color: rgb(105, 145, 61); font-weight: 400;'>Password : ".$generatedPswd."</div><br/><br/>
								  						<div style='margin-bottom: 20px;'>Follow this <a style='color: rgb(172, 88, 26); text-decoration: none;' href=".BASE_URL.">link</a> to open application.</div>";
								}
								else 
								{
								  $msgarray['error'] = "<div>Problem encountered while sending mail to ".SUPERADMIN_EMAIL."</div><br/>
														<div>Login Credentials for ".APPLICATION_NAME."</div><br/>
														<div style='color: rgb(105, 145, 61); font-weight: 400; margin-bottom: 14px; margin-top: 8px;'>Username : empp0001</div>
														<div style='color: rgb(105, 145, 61); font-weight: 400;'>Password : ".$generatedPswd."</div><br/><br/>
								  						<div style='margin-bottom: 20px;'>After you delete, move or rename the install directory follow this  <a style='color: rgb(172, 88, 26); text-decoration: none;' href=".BASE_URL.">link</a> to access your application. While the install directory exists, only the Install Panel will be accessible.</div>";	
									
								}  

								  
							}else 
							{
								if($renamefolder == 'true')
								{
								  $msgarray['error'] = "<div class='sucss_mess_info'>Mail has been succesfully sent to ".SUPERADMIN_EMAIL."</div><br/>
														<div>Login Credentials for ".APPLICATION_NAME."</div><br/>
														<div style='color: rgb(105, 145, 61); font-weight: 400; margin-bottom: 14px; margin-top: 8px;'>Username : empp0001</div>
														<div style='color: rgb(105, 145, 61); font-weight: 400;'>Password : ".$generatedPswd."</div><br/><br/>
								  						<div style='margin-bottom: 20px;'>Follow this <a style='color: rgb(172, 88, 26); text-decoration: none;' href=".BASE_URL.">link</a> to open application.</div>";
								}else 
								{
								  $msgarray['error'] = "<div>Mail has been succesfully sent to ".SUPERADMIN_EMAIL."</div><br/>
								  						<div>Login Credentials for ".APPLICATION_NAME."</div><br/>
														<div style='color: rgb(105, 145, 61); font-weight: 400; margin-bottom: 14px; margin-top: 8px;'>Username : empp0001</div>
														<div style='color: rgb(105, 145, 61); font-weight: 400;'>Password : ".$generatedPswd."</div><br/><br/>
								  						<div style='margin-bottom: 20px;'>After you delete, move or rename the install directory follow this  <a style='color: rgb(172, 88, 26); text-decoration: none;' href=".BASE_URL.">link</a> to access your application. While the install directory exists, only the Install Panel will be accessible.</div>";	
									
								  
								}
								
							}
					}
			
    		} 
    		catch (PDOException $e)
            {   
        
   				   $msgarray['error'] = $e->getMessage();                     
            }
    }
}


function sendconfirmationmail($content,$encodedPswd)
{
	$htmlcontentdata = '<div style="width:100%;">
			            <div style="background-color:#eeeeee; width:80%; margin:0 auto; position:relative;">
				            <div><img src="public/media/images/sentrifugo-email_wizard.png" height="62" width="319" /></div>
				            <div style="padding:20px 20px 50px 20px;">
			                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:normal; line-height:30px; margin:0 0 20px 0;">
			                       <div>
										<div>Dear Super Admin,</div><br/>
										<div>Sentrifugo has been successfully installed. Following are the Super Admin login credentials for '.APPLICATION_NAME.':</div><br/>
										<div>Username : empp0001</div>
										<div>Password : '.$encodedPswd.'</div><br/><br/>
										<div>'.$content.'</div>
								  </div>
			                    </div>
	                    
			                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:normal; line-height:30px;">
			                        Regards,<br />
			                        <b>Sentrifugo</b>
			                    </div>
			                </div>    
            			</div>
    					</div>';
	$username = '';   
    $mail = new PHPMailer(); // create a new object
    $mail->isSMTP(); // enable SMTP
    $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = (MAIL_AUTH=='true')?true:false;//$auth; // authentication enabled
    if(MAIL_TLS) $mail->SMTPSecure = MAIL_TLS; // secure transfer enabled REQUIRED for GMail
   // $mail->AuthType = MAIL_AUTH;   
    $mail->Host = MAIL_SMTP;
    if(MAIL_AUTH == 'true'){
		$mail->Username = MAIL_USERNAME;
		$mail->Password = MAIL_PASSWORD;
	}
	$mail->Port = MAIL_PORT; // or 587
	$mail->SMTPOptions = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false,'allow_self_signed' => true));

    $pos = strpos(MAIL_USERNAME, 'yahoo');
	if($pos !== false)
		$mail->setFrom(MAIL_USERNAME,'Do not Reply');
	else
		$mail->setFrom(SUPERADMIN_EMAIL,'Do not Reply');

    $mail->Subject = APPLICATION_NAME." - successfully installed";
    $mail->msgHTML($htmlcontentdata);
    $mail->addAddress(SUPERADMIN_EMAIL,'Super Admin');
    
    if(!$mail->Send())
        return $mail->ErrorInfo;
    else 
        return 'true';
}

function renamefolder()
{
    try
        {
			if(is_writable("install"))
			{
				if(@rename("install","install_old"))
					return "true"; 
				else 
					return "false";
			}
			else 
			{
				return "false";
			}
        }
        catch (Exception $e)
        {
			return "false";
        }
}


if(!empty($_POST))
{

    if(isset($_POST['mailcontent']))
		$content = urlencode($_POST['mailcontent']);
	else
		$content = 'Installation succesful';
		
	$dbhost = $_POST['dbhost'];
	$dbusername = $_POST['dbusername'];
	$dbpassword = $_POST['dbpassword'];
	$dbname = $_POST['dbname'];
	$appname = $_POST['appname'];
	$appemail = $_POST['appemail'];
	$mailusername = $_POST['mailusername'];
	$mailpassword = $_POST['mailpassword'];
	$mailsmtp = $_POST['mailsmtp'];
	$mailauth = $_POST['mailauth'];
	$mailtls = $_POST['mailtls'];
	$mailport = $_POST['mailport'];
	$cronjoburl = $_POST['cronjoburl'];
	$expirydocurl = $_POST['expirydocurl'];
	$tmcronurl = $_POST['tmcronurl'];
	
?>	


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sentrifugo</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="shortcut icon" href="public/media/images/favicon.ico" />
     <link href="public/media/css/successstyle.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic,300,300italic,100italic,100,700italic,900,900italic' rel='stylesheet' type='text/css'>
   
	
</head>
  <body>
      <div class="container">
     
      	<div class="header"> <div class="logo"></div></div>
        
        <div class="content_wrapper">
            
          <div id="successmsgdiv"><?php echo isset($msgarray['error'])?$msgarray['error']:'';?></div>
			<form name="generatereport" id="generatereport" action="data/generatereport.php" method="post">
			<input type="hidden" id="pdfcontent" name="pdfcontent" value="<?php echo $content;?>" />
			<input type="hidden" id="loginusername" name="loginusername" value="empp0001" />
			<input type="hidden" id="loginpwd" name="loginpwd" value="<?php echo $generatedPswd;?>" />
			<input type="hidden" id="dbhost" name="dbhost" value="<?php echo $dbhost;?>" />
		    <input type="hidden" id="dbusername" name="dbusername" value="<?php echo $dbusername;?>" />
		    <input type="hidden" id="dbpassword" name="dbpassword" value="<?php echo $dbpassword;?>" />
		    <input type="hidden" id="dbname" name="dbname" value="<?php echo $dbname;?>" />
		    <input type="hidden" id="appname" name="appname" value="<?php echo $appname;?>" />
		    <input type="hidden" id="appemail" name="appemail" value="<?php echo $appemail;?>" />
		    <input type="hidden" id="mailusername" name="mailusername" value="<?php echo $mailusername;?>" />
		    <input type="hidden" id="mailpassword" name="mailpassword" value="<?php echo $mailpassword;?>" />
		    <input type="hidden" id="mailsmtp" name="mailsmtp" value="<?php echo $mailsmtp;?>" />
		    <input type="hidden" id="mailauth" name="mailauth" value="<?php echo $mailauth;?>" /> 
		    <input type="hidden" id="mailtls" name="mailtls" value="<?php echo $mailtls;?>" />
		    <input type="hidden" id="mailport" name="mailport" value="<?php echo $mailport;?>" />
		    <input type="hidden" id="cronjoburl" name="cronjoburl" value="<?php echo $cronjoburl;?>" />
		    <input type="hidden" id="expirydocurl" name="expirydocurl" value="<?php echo $expirydocurl;?>" />
		    <input type="hidden" id="tmcronurl" name="tmcronurl" value="<?php echo $tmcronurl;?>" />
			<input type="submit" name="btnfinish" id="idbtnfinish"    value="Download PDF" />
			</form>
        </div>
      </div>
  </body>
</html>

<?php } else {

header("Location: index.php");
 }?>



