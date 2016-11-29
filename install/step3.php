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

<form id="idstep3" name="frmstep3" method="post" action="index.php?s=<?php echo sapp_Global::_encrypt(3);?>" class="frm_install">
    <h3 class="page_title">Application Settings</h3>
<?php 
    $msgarray = array();
    $app_name = defined('APPLICATION_NAME')?APPLICATION_NAME:'';
    $email = defined('SUPERADMIN_EMAIL')?SUPERADMIN_EMAIL:'';
    if(!empty($_POST))
    {
        $app_name = $_POST['app_name'];
        $email = $_POST['email'];
        
        $i = 0 ;
        
        if( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", trim($email)))
        {
            $msgarray['email'] = "Please enter valid email.";
            $i++;
        }
        if( ! preg_match("/^([a-zA-Z0-9.\-&]+ ?)+$/", $app_name))
        {
            $msgarray['app_name'] = "Please enter valid application name.";
            $i++;
        }
        if(strlen($app_name) <3)
        {
            $msgarray['app_name'] = "Application Name must be atleast 3 characters.";
            $i++;
        }
        if(strlen($app_name) > 50)
        {
            $msgarray['app_name'] = "Application Name cannot be grater than 50 characters.";
            $i++;
        }
        if($app_name == '')
        {
            $msgarray['app_name'] = "Application Name cannot be empty.";
            $i++;
        }
        if($email == '')
        {
            $msgarray['email'] = "Email cannot be empty.";
            $i++;
        }
        if($i == 0) 
        {
            if(write_app_constants($app_name,$email))
            {
?>
            <script type="text/javascript" language="javascript">
                window.location= "index.php?s=<?php echo sapp_Global::_encrypt(4);?>";
            </script>
<?php       
            }              
            else 
                $msgarray['frm_error'] = "Some error,please try again.";
        }                        
    }
?>
<div class="content_part">
    <span  class="error_info"><?php echo isset($msgarray['frm_error'])?$msgarray['frm_error']:"";?></span>
    <div class="new-form-ui ">
        <label class="required">Application Name<img src="images/help.png" title="Name of your application." class="tooltip"></label>
        <div>
            <input type="text" maxlength="50" value="<?php echo $app_name;?>" id="app_name" name="app_name" />
            <span  class="error_info"><?php echo isset($msgarray['app_name'])?$msgarray['app_name']:'';?></span>
        </div>
    </div>
    <div class="new-form-ui ">
        <label class="required">Email<img src="images/help.png" title="Super admin email for authenticate Mail Server." class="tooltip"></label>
        <div>
            <input type="text" maxlength="100" value="<?php echo $email;?>" id="email" name="email" />
            <span><?php echo isset($msgarray['email'])?$msgarray['email']:'';?></span>
        </div>
    </div>
    
        <input type="submit" value="Confirm" id="submitbutton" name="submit" class="save_button"> 
        </div>
     
        <button name="previous" id="previous" class="previous_button" type="button" onclick="window.location='index.php?s=<?php echo sapp_Global::_encrypt(2);?>';">Previous</button>
	   	<?php if(defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && constant('SUPERADMIN_EMAIL') !='') { ?>
	   	<button name="next"  id="next" type="button" onclick="window.location='index.php?s=<?php echo sapp_Global::_encrypt(4);?>';">Next</button>
	   	<?php }?>
     </form>  


<?php 
function write_app_constants($app_name,$email)
{
    $filename = '../public/application_constants.php';
    if(file_exists($filename))
    {
        
            $db_content = "<?php
       defined('SUPERADMIN_EMAIL') || define('SUPERADMIN_EMAIL','".$email."');
       defined('APPLICATION_NAME') || define('APPLICATION_NAME','".$app_name."');
     ?>";
            try{
                $mysqlPDO = new PDO('mysql:host='.SENTRIFUGO_HOST.';dbname='.SENTRIFUGO_DBNAME.'',SENTRIFUGO_USERNAME, SENTRIFUGO_PASSWORD);
                $query = "update main_users set emailaddress = '".$email."' where id = ".SUPERADMIN." and emprole = ".SUPERADMINROLE;
                if($mysqlPDO->query($query))
                {                    
                    $handle = fopen($filename, "w+");
                    fwrite($handle,trim($db_content));
                    fclose($handle);
                    return true;
                }
            }
            catch (Exception $e)
            {
                return false;
            }
    }
    
    return false;
}
?>


<script type="text/javascript">
		$(document).ready(function(){
			$(".first_li").addClass('active');
			$(".first_icon").addClass('yes');
			<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME')){ ?>
			$(".second_li").addClass('active');
			$(".second_icon").addClass('yes');
			<?php }?>
			
			<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME')){ ?>
			$(".second_li").addClass('active');
			$(".second_icon").addClass('yes');
			<?php }?>
			
			<?php if(defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && constant('SUPERADMIN_EMAIL') !='') { ?>
			$(".third_li").addClass('active');
			$(".third_icon").addClass('yes');
			<?php }else{?>
			$(".third_li").addClass('current');
			$(".third_icon").addClass('loding_icon');
			<?php }?>
			
			<?php if(defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_AUTH') && defined('MAIL_TLS')){ ?>
			$(".fourth_li").addClass('active');
			$(".fourth_icon").addClass('yes');
			<?php }?>
			
			<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME') && defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_AUTH') && defined('MAIL_TLS')){ ?>
			$(".fifth_li").addClass('active');
			$(".fifth_icon").addClass('yes');
			<?php }?>
		});
</script>