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
?>

<?php 

if(count($_POST) > 0)
{
	
    $msgarray = array();
    if(!empty($_POST))
    {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $smtpserver = trim($_POST['smtpserver']);
        $tls = trim($_POST['tls']);
        $port = trim($_POST['port']);
        $auth = trim($_POST['auth']);

		if($smtpserver !='' && $port !='' && $auth != '')
        {     
			if(($auth == 'false' && $username !='' && $password !='') || ($auth == 'true' && $username =='' && $password ==''))
			{
	           $msgarray =  set_validation_messages($tls,$smtpserver,$username,$password,$port,$auth);
			}
			else if( ! preg_match("/^([0-9])+$/", $port))
                $msgarray['port'] = 'Please enter valid port number.';
            else 
            {
				$msgarray = main_function($tls,$smtpserver,$username,$password,$port,$auth);	
				if(isset($msgarray['result']) && $msgarray['result'] =='send')
				{
				?>
					<script type="text/javascript" language="javascript">
					window.location= "index.php?s=<?php echo sapp_Global::_encrypt(5);?>";
					</script>
				<?php
				}
            }
        }
		else
        {
           $msgarray =  set_validation_messages($tls,$smtpserver,$username,$password,$port,$auth);
        }
    }
}
function main_function($tls,$smtpserver,$username,$password,$port,$auth)
{
	$msgarray = array(); 		
    try
    {	
        $mail = mail_send($tls,$smtpserver,$username,$password,$port,$auth);
        if($mail === true)
        {  	                                  				   				    
            insert_into_db($tls,$smtpserver,$username,$password,$port,$auth);
            $constantresult = writeMailSettingsconstants($tls,$port,$username,$password,$smtpserver,$auth);
            if($constantresult === true)
            {		
                $msgarray['result'] = 'send';
            }
            else
            {
                $msgarray['error'] = 'Some error occured' ;
            }
        }
        else
        {
            $msgarray['error'] = $mail;												
        }
    }		
    catch(PDOException $ex)
    {
        $msgarray['error'] = 'Some error occured. '.$ex->getMessage() ;
    }
	
    return $msgarray;
}
function set_validation_messages($tls,$smtpserver,$username,$password,$port,$auth)
{
   $msgarray = array(); 
	if($auth == 'true')
	{
		if($username == '')
		{
			$msgarray['username'] = 'User name cannot be empty';
		}
		if($auth == 'true' && $password == '')
		{
			$msgarray['password'] = 'Password cannot be empty';
		}
	}
    if($smtpserver == '')
    {
        $msgarray['smtpserver'] = 'SMTP Server cannot be empty';
    }
    /*if($tls == '')
    {
        $msgarray['tls'] = 'Secure Transport Layer cannot be empty';
    }*/
    if($port == '')
    {
        $msgarray['port'] = 'Port cannot be empty';
    }
	if($auth == '')
    {
        $msgarray['port'] = 'Authentication cannot be empty';
    }
    return $msgarray;
}
function insert_into_db($tls,$smtpserver,$username,$password,$port,$auth)
{
    $mysqlPDO = new PDO('mysql:host='.SENTRIFUGO_HOST.';dbname='.SENTRIFUGO_DBNAME.'',SENTRIFUGO_USERNAME, SENTRIFUGO_PASSWORD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));   
    $date= gmdate("Y-m-d H:i:s");
    $stmt = $mysqlPDO->prepare("SELECT count(*) as count from main_mail_settings ");
    $stmt->execute();
    $row = $stmt->fetch();
				    	
    if($row['count'] > 0)
    {
        $query1 = "UPDATE main_mail_settings SET tls='".$tls."', port=".$port.", auth='".$auth."', username='".$username."', password='".$password."', server_name='".$smtpserver."', createddate='".$date."', modifieddate='".$date."' ";
    }
    else
    {
        $query1 = "INSERT INTO main_mail_settings (tls,auth, port,username,password,server_name,createddate,modifieddate) VALUES ('".$tls."','".$auth." ',".$port.",'".$username."','".$password."','".$smtpserver."','".$date."','".$date."') ";
    }	
    
    $mysqlPDO->query($query1);
}//end of insert_into_db function.

function mail_send($tls,$smtpserver,$username,$password,$port,$auth)
{
	
	$htmlcontentdata = '
	<div style="width:100%;">
            <div style="background-color:#eeeeee; width:80%; margin:0 auto;  position:relative;">
            <div><img src="../public/media/images/sentrifugo-email_wizard.png" height="62" width="319" /></div>
            <div style="padding:20px 20px 50px 20px;">
                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:normal; line-height:30px; margin:0 0 20px 0;">
                       <div>
							<div>Dear Super Admin,</div><br/>
							<div>This is a test email to check the new mail settings provided for '.APPLICATION_NAME.'.</div>
					  </div>
                    </div>
                    
                    <div style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:normal; line-height:30px;">
                        Regards,<br />
                        <b>Sentrifugo</b>
                    </div>
            </div>
            </div>
    </div>';
    
    $mail = new PHPMailer(); // create a new object
    $mail->isSMTP(); // enable SMTP
    $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = ($auth=='true')?true:false; // authentication enabled
	if($tls)	$mail->SMTPSecure = $tls; // secure transfer enabled REQUIRED for GMail
    $mail->Host = $smtpserver;
	if($auth == 'true'){
		$mail->Username = $username;
		$mail->Password = $password;
	}
    $mail->Port = $port; // or 587
	$mail->SMTPOptions = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false,'allow_self_signed' => true));
	
	$yahoo_smtp = strpos($username, 'yahoo');
	if($yahoo_smtp !== false) {
		//Fix for Yahoo SMTP configuration.
		$mail->setFrom($username,'Do not Reply');
	}else {
		$mail->setFrom(SUPERADMIN_EMAIL,'Do not Reply');
	}
	
    $mail->Subject = "Test Mail Checking";
    $mail->msgHTML($htmlcontentdata);
    $mail->addAddress(SUPERADMIN_EMAIL,'Super Admin');
    
    if(!$mail->Send()){
		return $mail->ErrorInfo;
    }else 
        return true;
}//end of mail_send function 
function writeMailSettingsconstants($tls,$port,$username,$password,$smtpserver,$auth)
{
    $filename = '../public/mail_settings_constants.php';
    if(file_exists($filename))
    {
        $db_content = "<?php
	           defined('MAIL_SMTP') || define('MAIL_SMTP','".$smtpserver."');
	           defined('MAIL_USERNAME') || define('MAIL_USERNAME','".$username."');
	           defined('MAIL_PASSWORD') || define('MAIL_PASSWORD','".$password."');
	           defined('MAIL_PORT') || define('MAIL_PORT','".$port."');
	           defined('MAIL_AUTH') || define('MAIL_AUTH','".$auth."');
	           defined('MAIL_TLS') || define('MAIL_TLS','".$tls."');
	         ?>";
        try
        {
            $handle = fopen($filename, "w+");
            fwrite($handle,trim($db_content));
            fclose($handle);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }	
}//end of writeMailSettingsconstants function.
?>
<form method="post" action="index.php?s=<?php echo sapp_Global::_encrypt(4);?>" id="step4" name="step4" class="frm_install">
	<h3 class="page_title">Mail Server Settings</h3>
    <div class="content_part">	
     
          
           <span class="error_info"><?php echo isset($msgarray['error'])?$msgarray['error']:'';?></span>
           
		   <div class="new-form-ui ">
			  <label class="required">Authentication Type<img src="images/help.png" title="authentication to access mail account (ex: true/false)" class="tooltip"></label>
			 				<div>
							<?php 
								if(isset($_POST['auth'])) $auth = $_POST['auth'];
								else if(defined('MAIL_AUTH')) $auth = MAIL_AUTH;
								else $auth = 'true';
							?>
			 					<select id="auth" name="auth" >
								   <option value="true" <?php echo ($auth == 'true')? 'selected':"";?> >True</option>
			 					   <option value="false" <?php echo ($auth == 'false')? 'selected':"";?> >False</option>
			 					</select>
			 					<span><?php echo isset($msgarray['auth'])?$msgarray['auth']:'';?></span>
			 				</div>
			 			</div> 
		<?php
			if($auth == 'true')$display = 'block';
			else $display = 'none';
		?>
		<div id="mailAuthDiv" style="display:<?php echo $display; ?>">
           <div class="new-form-ui ">
			  <label class="required">User name<img src="images/help.png" title="Mail Server username provided during Mail Server account setup." class="tooltip"></label>
				<div>
			<input type="text" maxlength="100" value="<?php if(!$_POST){ echo defined('MAIL_USERNAME')?MAIL_USERNAME:'';} else {echo $_POST['username']; }?>" id="username" name="username">
			<span><?php echo isset($msgarray['username'])?$msgarray['username']:'';?></span>
			</div>
			</div>
			
			<div class="new-form-ui ">
			  <label class="required">Password<img src="images/help.png" title="Mail Server password provided during Mail Server account setup." class="tooltip"></label>
				<div>
					<input type="password" maxlength="100" value="<?php if(!$_POST){ echo defined('MAIL_PASSWORD')?MAIL_PASSWORD:'';} else {echo $_POST['password']; }?>" id="password" name="password">
					<span><?php echo isset($msgarray['password'])?$msgarray['password']:'';?></span>
				</div>
			</div>
		</div>
			<div class="new-form-ui ">
			  <label class="required">SMTP Server<img src="images/help.png" title="IP address of your hosting account as your Mail Server hostname (ex: mail.google.com)" class="tooltip"></label>
				<div>
					<input type="text" maxlength="100" value="<?php if(!$_POST){ echo defined('MAIL_SMTP')?MAIL_SMTP:'';} else {echo $_POST['smtpserver']; }?>" id="smtpserver" name="smtpserver">
					<span><?php echo isset($msgarray['smtpserver'])?$msgarray['smtpserver']:'';?></span>
				</div>
			</div>
			
		    <div class="new-form-ui ">
			  <label>Secure Transport Layer<img src="images/help.png" title="Provides communication security over internet (ex: tls)" class="tooltip"></label>
				<div>
					<input type="text" maxlength="40" value="<?php if(!$_POST){ echo defined('MAIL_TLS')? MAIL_TLS:''; } else {echo $_POST['tls']; }?>" id="tls" name="tls">
					<span><?php echo isset($msgarray['tls'])?$msgarray['tls']:'';?></span>
				</div>
			</div>
			
	
			
			<div class="new-form-ui ">
			  <label class="required">Port<img src="images/help.png" title="Port number to access SMTP server (Ex: 22, 25)" class="tooltip"></label>
				<div>
					<input type="text" maxlength="50" value="<?php if(!$_POST){ echo defined('MAIL_PORT')?MAIL_PORT:'';} else {echo $_POST['port']; }?>" id="port" name="port">
					<span><?php echo isset($msgarray['port'])?$msgarray['port']:'';?></span>
				</div>
			</div>											
		
			<input type="submit" value="Confirm" id="submitbutton" name="submit" class="save_button"> </div >
		   <button name="previous" id="previous" type="button" class="previous_button" onclick="window.location='index.php?s=<?php echo sapp_Global::_encrypt(3);?>';">Previous</button>
		   <?php if(defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_TLS')){ ?>
		   	<button name="next" id="next" type="button" onclick="window.location='index.php?s=<?php echo sapp_Global::_encrypt(5);?>';">Next</button>
		   	<?php }?>

</form>
<script type="text/javascript">
		$(document).ready(function(){
			 $("select:not(.not_appli)").select2({
				    formatResult: format_select,            
				    escapeMarkup: function(m) { return m; }
				});
			function format_select(selData) {
	            return  "<span>" + selData.text + "</span><div class='seldivimg'></div>";
			}

			
			$(".first_li").addClass('active');
			$(".first_icon").addClass('yes');
			
			<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME')){ ?>
			$(".second_li").addClass('active');
			$(".second_icon").addClass('yes');
			<?php }?>
			
			<?php if(defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && constant('SUPERADMIN_EMAIL') !='') { ?>
			$(".third_li").addClass('active');
			$(".third_icon").addClass('yes');
			<?php }?>
			
			<?php if(defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_TLS') && defined('MAIL_AUTH')){ ?>
			$(".fourth_li").addClass('active');
			$(".fourth_icon").addClass('yes');
			<?php }else{?>
			$(".fourth_li").addClass('current');
			$(".fourth_icon").addClass('loding_icon');
			<?php }?>

			<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME') && defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_TLS') && defined('MAIL_AUTH')){ ?>
			$(".fifth_li").addClass('active');
			$(".fifth_icon").addClass('yes');
			<?php }?>
		
		$('#auth').change(function(){
			/*if($(this).val() == 'true') 
			{
				$('#mailAuthDiv').show();
			}
			else if($(this).val() == 'false') 
			{
				$('#mailAuthDiv').hide();
				$('#username').val('');
 				$('#password').val('');
			}*/

			if($('#auth').val() == 'true') 
			{
				$('#mailAuthDiv').show();
			}
			else if($('#auth').val() == 'false') 
			{
				$('#mailAuthDiv').hide();
				$('#username').val('');
				$('#password').val('');
			}
			$('span[id^="errors-"]').html('');
			$('.error_info').html('');
		});
			
	});
		
</script>
