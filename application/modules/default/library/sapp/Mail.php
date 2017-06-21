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
include 'PHPMailer/PHPMailerAutoload.php';
error_reporting(E_ALL | ~E_NOTICE | ~E_WARNING);
ini_set("display_errors", 0);

class sapp_Mail
{
	
	private function __construct() {
		// private - should not be used
	}
	
	public static function _email($options = array()) {
		
		$options['fromEmail'] = (!empty($options['fromEmail']))?$options['fromEmail']:SUPERADMIN_EMAIL;
		$options['fromName'] = (!empty($options['fromName']))?$options['fromName']:DONOTREPLYNAME;
		
		$orglogo = '';
		$imgsource = '';$a = '';
		$Orgmodel = new Default_Model_Organisationinfo();
		$orglogoArr = $Orgmodel->getOrgLogo();
		
		if(!empty($orglogoArr))
		$orglogo = $orglogoArr['org_image']; 
		
		if(!empty($orglogo))
		   $imgsource = DOMAIN.'public/uploads/organisation/'.$orglogo ;
		else
		   $imgsource = MEDIA_PATH.'images/mail_pngs/hrms_logo.png';
		
		$header="";
		$footer="";
        $smtpServer = "";
		$config = array();

		if(!empty($options['server_name']) && !empty($options['auth']) && !empty($options['port']) )
		{
			if( $options['auth'] == 'true' && !empty($options['username']) && !empty($options['password']))
			{
				$config = array(
							'auth' => $options['auth']
							,'username' => $options['username']
							,'password' => $options['password']
							,'port' => $options['port']
				);   
			}
			else if($options['auth'] == 'false')
			{
				$config = array(
							'auth' => $options['auth']
							,'port' => $options['port']
				);   
			}
			if(!empty($options['tls']))
			{
				$config['tls'] = $options['tls'];
			}
			$smtpServer = $options['server_name'];
		}
		else
		{
			 //Picking from Mail Constant
			  if(!defined('MAIL_TLS')){
				define('MAIL_TLS','');
			  }
			  if(!defined('MAIL_AUTH')){
				define('MAIL_AUTH','');
			  }
			  if(!defined('MAIL_USERNAME')){
				define('MAIL_USERNAME','');
			  }
			  if(!defined('MAIL_PASSWORD')){
				define('MAIL_PASSWORD','');
			  }
			  if(!defined('MAIL_PORT')){
				define('MAIL_PORT','');
			  }
			  if(!defined('MAIL_SMTP')){
				define('MAIL_SMTP','');
			  }
			   $config = array(
					'tls' => MAIL_TLS
					,'auth' => MAIL_AUTH
					,'username' => MAIL_USERNAME
					,'password' => MAIL_PASSWORD
					,'port' => MAIL_PORT
			   );   
			   $smtpServer = MAIL_SMTP;
		}               
		
		//end of sapplica mail configuration
		return self::send_php_mail($config, $smtpServer, $imgsource, $options);
}	

public static function _checkMail($options = array()) {
		
		$options['fromEmail'] = (!empty($options['fromEmail']))?$options['fromEmail']:SUPERADMIN_EMAIL;
		$options['fromName'] = (!empty($options['fromName']))?$options['fromName']:DONOTREPLYNAME;
		
		$orglogo = '';
		$imgsource = '';$a = '';
		$Orgmodel = new Default_Model_Organisationinfo();
		$orglogoArr = $Orgmodel->getOrgLogo();
		
		if(!empty($orglogoArr))
		$orglogo = $orglogoArr['org_image']; 
		
		if(!empty($orglogo))
		   $imgsource = DOMAIN.'public/uploads/organisation/'.$orglogo ;
		else
		   $imgsource = MEDIA_PATH.'images/mail_pngs/hrms_logo.png';
		
		$header="";
		$footer="";
                		
		if(!empty($options['server_name']) && !empty($options['auth']) && !empty($options['port']) )
		{
			if( $options['auth'] == 'true' && !empty($options['username']) && !empty($options['password']))
			{
				$config = array(
							'auth' => $options['auth']
							,'username' => $options['username']
							,'password' => $options['password']
							,'port' => $options['port']
				);   
			}
			else if($options['auth'] == 'false')
			{
				$config = array(
							'auth' => $options['auth']
							,'port' => $options['port']
				);   
			}
			if(!empty($options['tls']))
			{
				$config['tls'] = $options['tls'];
			}
			$smtpServer = $options['server_name'];
		}
		
		//end of sapplica mail configuration
		return self::send_php_mail($config, $smtpServer, $imgsource, $options);		
}

	/**
	 * 
	 * Send mail with PHP Mailer library
	 * @param Array $config - List of SMTP configuration details
	 * @param String $smtpserver - Name of SMTP server
	 * @param String $imgsource - Image source of application logo
	 * @param Array $options - List of mail options
	 */
	public static function send_php_mail($config = array(), $smtpserver='', $imgsource='', $options = array())
	{
			
    	$htmlcontentdata = '
		<div style="width:100%;">
            <div style="background-color:#eeeeee; width:800px; margin:0 auto; position:relative;">
            <div style="float:right;"><img src="'.$imgsource.'" onError="this.src='.MEDIA_PATH.'images/mail_pngs/hrms_logo.png" height="62" width="319" /></div>
            <div style="padding:20px 20px 50px 20px;">
                    <div>
                        <h1 style="font-family:Arial, Helvetica, sans-serif; font-size:18px; font-weight:bold; border-bottom:1px dashed #999; padding-bottom:15px;">'.$options['header'].'</h1>
                    </div>
                    
                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:normal; line-height:30px; margin:0 0 20px 0;">
                        '.$options['message'].'
                    </div>
                    
                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:normal; line-height:30px;">
                        Regards,<br />
                        <b>'.APPLICATION_NAME.'</b>
                    </div>
            </div>
            </div>
    	</div>';
		
	    $mail = new PHPMailer(); // create a new object
	    $mail->isSMTP(); // enable SMTP
	    $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
	    $mail->SMTPAuth = ($config['auth'] == 'true')?true:false;//$auth; // authentication enabled
		if($config['tls'])
			$mail->SMTPSecure = $config['tls']; // secure transfer enabled REQUIRED for GMail
	    $mail->Host = $smtpserver;
	    if($config['auth'] == 'true')
		{
			$mail->Username = $config['username'];
			$mail->Password = $config['password'];
	    }
		$mail->Port = $config['port']; // or 587
		$mail->SMTPOptions = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false,'allow_self_signed' => true));
	
	    $yahoo_smtp = strpos($config['username'], 'yahoo');
		if($yahoo_smtp !== false) {
			//Fix for Yahoo SMTP configuration.
			$mail->setFrom($config['username'],'Do not Reply');
		} else {
			$mail->setFrom($options['fromEmail'],$options['fromName']);
		}
		
	    $mail->Subject = $options['subject'];
	    $mail->msgHTML($htmlcontentdata);
	    $mail->addAddress($options['toEmail'], $options['toName']);
	    if(array_key_exists('bcc', $options))
	    {  
	    	$sizeBcc = sizeof($options['bcc']);
	    	for($i=0;$i<$sizeBcc;$i++)
	    	{
	    		 $bccMail = $options['bcc'][$i];
	    		 $mail->addBCC($bccMail);
	    	}
	    	
	    }		
		if(array_key_exists('cc', $options))
			$mail->addCc($options['cc']);
       
		if(!$mail->Send()) {
			return false;
	    } else { 
	       return true;
	    }
	}
	//End of send_php_mail()
}
?>
