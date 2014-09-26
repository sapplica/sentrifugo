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
        $username = $_POST['username'];
        $password = $_POST['password'];
        $smtpserver = $_POST['smtpserver'];
        $tls = $_POST['tls'];
        $auth = $_POST['auth'];
        $port = $_POST['port'];
        if($username !='' && $password !='' && $smtpserver !='' && $tls !='' && $auth !='' && $port !='')
        {     
            if( ! preg_match("/^([0-9])+$/", $port))
                $msgarray['port'] = 'Please enter valid port number.';
            else 
            {
               $msgarray = main_function($tls,$auth,$smtpserver,$username,$password,$port);	
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
           $msgarray =  set_validation_messages($tls,$auth,$smtpserver,$username,$password,$port);
        }
    }
}
function main_function($tls,$auth,$smtpserver,$username,$password,$port)
{
    $auth_arr = array(1 => 'crammd5', 2=> 'login',3 => 'plain');
    if(array_key_exists($auth, $auth_arr))
        $auth = $auth_arr[$auth];
    else 
    $auth = 'crammd5';        
    $msgarray = array(); 		
    try
    {	
        $mail = mail_send($tls,$auth,$smtpserver,$username,$password,$port);
        if($mail != 'true')
        {                                    				   				    
            $msgarray['error'] =$mail;
        }
        else
        {
            insert_into_db($tls,$auth,$smtpserver,$username,$password,$port);
            $constantresult = writeMailSettingsconstants($tls,$auth,$port,$username,$password,$smtpserver);
            if($constantresult === true)
            {		
                $msgarray['result'] = 'send';
            }
            else
            {
                $msgarray['error'] = 'Some error occured' ;
            }													
        }
    }		
    catch(PDOException $ex)
    {
        $msgarray['error'] = 'Some error occured' ;
    }
    return $msgarray;
}
function set_validation_messages($tls,$auth,$smtpserver,$username,$password,$port)
{
   $msgarray = array(); 
   if($username == '')
    {
        $msgarray['username'] = 'User name cannot be empty';
    }
    if($password == '')
    {
        $msgarray['password'] = 'Password cannot be empty';
    }
    if($smtpserver == '')
    {
        $msgarray['smtpserver'] = 'SMTP Server cannot be empty';
    }
    if($tls == '')
    {
        $msgarray['tls'] = 'Secure Transport Layer cannot be empty';
    }
    if($auth == '')
    {
        $msgarray['auth'] = 'Auth cannot be empty';
    }
    if($port == '')
    {
        $msgarray['port'] = 'Port cannot be empty';
    }
    return $msgarray;
}
function insert_into_db($tls,$auth,$smtpserver,$username,$password,$port)
{
    $mysqlPDO = new PDO('mysql:host='.SENTRIFUGO_HOST.';dbname='.SENTRIFUGO_DBNAME.'',SENTRIFUGO_USERNAME, SENTRIFUGO_PASSWORD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));   
    $date= gmdate("Y-m-d H:i:s");
    $stmt = $mysqlPDO->prepare("SELECT count(*) as count from main_mail_settings ");
    $stmt->execute();
    $row = $stmt->fetch();
				    	
    if($row['count'] > 0)
    {
        $query1 = "UPDATE main_mail_settings SET tls='".$tls."', auth='".$auth."', port=".$port.", username='".$username."', password='".$password."', server_name='".$smtpserver."', createddate='".$date."', modifieddate='".$date."' ";
    }
    else
    {
        $query1 = "INSERT INTO main_mail_settings (tls, auth, port,username,password,server_name,createddate,modifieddate) VALUES ('".$tls."','".$auth."',".$port.",'".$username."','".$password."','".$smtpserver."','".$date."','".$date."') ";
    }	
    
    $mysqlPDO->query($query1);
}//end of insert_into_db function.
function mail_send($tls,$auth,$smtpserver,$username,$password,$port)
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
    $mail->SMTPAuth = true;//$auth; // authentication enabled
    $mail->SMTPSecure = $tls; // secure transfer enabled REQUIRED for GMail
    $mail->AuthType = $auth;   
    $mail->Host = $smtpserver;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->Port = $port; // or 587

    $pos = strpos($username, 'yahoo');
	if($pos !== false)
		$mail->setFrom($username,'Do not Reply');
	else
		$mail->setFrom(SUPERADMIN_EMAIL,'Do not Reply');
    $mail->Subject = "Test Mail Checking";
    $mail->msgHTML($htmlcontentdata);
    $mail->addAddress(SUPERADMIN_EMAIL,'Super Admin');
    
    if(!$mail->Send())
        return $mail->ErrorInfo;
    else 
        return 'true';
}//end of mail_send function 
function writeMailSettingsconstants($tls,$auth,$port,$username,$password,$smtpserver)
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
			  <label class="required">User name<img src="images/help.png" title="Mail Server username provided during Mail Server account setup." class="tooltip"></label>
				<div>
					<input type="text" maxlength="100" value="<?php if(!$_POST){ echo defined('MAIL_USERNAME')?MAIL_USERNAME:'';} else {echo $_POST['username']; }?>" id="username" name="username">
					<span><?php echo isset($msgarray['username'])?$msgarray['username']:'';?></span>
				</div>
			</div>
			
			<div class="new-form-ui ">
			  <label class="required">Password<img src="images/help.png" title="Mail Server password provided during Mail Server account setup." class="tooltip"></label>
				<div>
					<input type="text" maxlength="100" value="<?php if(!$_POST){ echo defined('MAIL_PASSWORD')?MAIL_PASSWORD:'';} else {echo $_POST['password']; }?>" id="password" name="password">
					<span><?php echo isset($msgarray['password'])?$msgarray['password']:'';?></span>
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
			  <label class="required">Secure Transport Layer<img src="images/help.png" title="Provides communication security over internet (ex: tls)" class="tooltip"></label>
				<div>
					<input type="text" maxlength="40" value="<?php if(!$_POST){ echo defined('MAIL_TLS')?MAIL_TLS:''; } else {echo $_POST['tls']; }?>" id="tls" name="tls">
					<span><?php echo isset($msgarray['tls'])?$msgarray['tls']:'';?></span>
				</div>
			</div>
			
			<div class="new-form-ui ">
			  <label class="required">Authentication Type<img src="images/help.png" title="authentication to access mail account (ex: login, plain and crammd5)" class="tooltip"></label>
				<div>
					<select id="auth" name="auth">
					
					  <?php 
					      $authArray = array('1' =>'Crammd5','2' => 'Login','3' => 'Plain');
					      $value = '';
					       if(defined('MAIL_AUTH'))
					       {
					        $authconstant = MAIL_AUTH;
					        	if($authconstant == 'crammd5')
					              $value = 1;
					            else if($authconstant == 'login')
					              $value = 2;
					            else
					              $value = 3; 
					            
					       }
					        for($i = 1;$i<=sizeof($authArray);$i++)
					        {
							if(!$_POST){
					        	if($i == $value)
					        	  $selected = 'selected'; 
					        	else
					        	  $selected = ''; 
					  ?>
						   <option value="<?php echo $i;?>" <?php echo $selected;?>><?php echo $authArray[$i];?></option>
						<?php } else {
								if($i == $_POST['auth'])
								  $selected = 'selected';
								else	
								 $selected = ''; 
						?>
					       <option value="<?php echo $i;?>" <?php echo $selected;?>><?php echo $authArray[$i];?></option>
					<?php }}?>
					</select>
					<!--  <input type="text" maxlength="50" value="<?php //defined('MAIL_AUTH')?MAIL_AUTH:'';?>" id="auth" name="auth">-->
					<span><?php echo isset($msgarray['auth'])?$msgarray['auth']:'';?></span>
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
		   <?php if(defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_AUTH') && defined('MAIL_TLS')){ ?>
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
			
			<?php if(defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_AUTH') && defined('MAIL_TLS')){ ?>
			$(".fourth_li").addClass('active');
			$(".fourth_icon").addClass('yes');
			<?php }else{?>
			$(".fourth_li").addClass('current');
			$(".fourth_icon").addClass('loding_icon');
			<?php }?>

			<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME') && defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_AUTH') && defined('MAIL_TLS')){ ?>
			$(".fifth_li").addClass('active');
			$(".fifth_icon").addClass('yes');
			<?php }?>
		});
</script>
