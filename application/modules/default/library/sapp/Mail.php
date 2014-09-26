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
		
		if($orglogo !='')
		   $imgsource = DOMAIN.'public/uploads/organisation/'.$orglogo ;
		else
		   $imgsource = MEDIA_PATH.'images/mail_pngs/hrms_logo.png';
		
		$header="";
		$footer="";
                		
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
		//end of sapplica mail configuration
		$transport = new Zend_Mail_Transport_Smtp($smtpServer, $config);	
		Zend_Mail::setDefaultTransport($transport);	
		$mail = new Zend_Mail('UTF-8');
				                
                $htmlcontentdata = '
	<div style="width:100%;">
            <div style="background-color:#eeeeee; width:80%; margin:0 auto; position:relative;">
            <div><img src="'.$imgsource.'" onError="'.MEDIA_PATH.'images/mail_pngs/hrms_logo.png" height="62" width="319" /></div>
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
	
	
		$mail->setSubject($options['subject']);
		$mail->setFrom($options['fromEmail'], $options['fromName']);
                
                
		$mail->addTo($options['toEmail'], $options['toName']);
		$mail->setBodyHtml($htmlcontentdata);
		if(array_key_exists('bcc', $options))
                    $mail->addBcc($options['bcc']);		
		if(array_key_exists('cc', $options))
			$mail->addCc($options['cc']);
		try{
                    if(!empty($options['toEmail']))
                        $a = @$mail->send();                                
	} catch(Exception $ex){
	
	$a = "error";
     }			        
            return $a;
	}	
	
public static function _checkMail($options = array()) {	    
		$options['fromEmail'] = DONOTREPLYEMAIL;
                $options['fromName'] = SUPERADMIN_EMAIL;
		$orglogo = '';
		$imgsource = '';
		$Orgmodel = new Default_Model_Organisationinfo();
		$orglogoArr = $Orgmodel->getOrgLogo();
		
		if(!empty($orglogoArr))
		$orglogo = $orglogoArr['org_image']; 
		
		if($orglogo !='')
		   $imgsource = DOMAIN.'public/uploads/organisation/'.$orglogo ;
		else
		   $imgsource = MEDIA_PATH.'images/mail_pngs/hrms_logo.png';
		
		$header="";
		$footer="";
  
                $config = array(
                        'tls' => $options['tls']
                        ,'auth' => $options['auth']
                        ,'username' => $options['username']
                        ,'password' => $options['password']
                        ,'port' => $options['port']
                    );
		$smtpServer = $options['server_name'];
		//end of sapplica mail configuration
		$transport = new Zend_Mail_Transport_Smtp($smtpServer, $config);	
		Zend_Mail::setDefaultTransport($transport);	
		$mail = new Zend_Mail('UTF-8');
                
                $htmlcontentdata = '
				<div style="width:100%;">
			            <div style="background-color:#eeeeee; width:80%; margin:0 auto; position:relative;">
			            <div><img src="'.$imgsource.'" onError="'.MEDIA_PATH.'images/mail_pngs/hrms_logo.png" height="62" width="319" /></div>
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
	
		$mail->setSubject($options['subject']);
		$mail->setFrom($options['fromEmail'], $options['fromName']);                
                
		$mail->addTo($options['toEmail'], $options['toName']);
		$mail->setBodyHtml($htmlcontentdata);
		if(array_key_exists('bcc', $options))
                    $mail->addBcc($options['bcc']);		
		if(array_key_exists('cc', $options))
			$mail->addCc($options['cc']);
		try{
                    if(!empty($options['toEmail']))
                    {
                        $a = @$mail->send();
                        return 'success';
                    }    
                   
                
	} catch(Exception $ex){
	
	$a = "error";
     }			        
            return $a;
	}
}
?>
