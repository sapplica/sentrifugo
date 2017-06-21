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
function getPHPVersion() {
	$prereq = array('php'   => '5.3',
                        'mysql' => '5.0');
        return $prereq['php'];
    }                        
function check_php() {
        return (version_compare(PHP_VERSION, getPHPVersion())>=0);
    }

    function check_mysql() {
        return (extension_loaded('mysqli'));
    }                        

$req_arr = array(
			'php' => check_php(),
			'pdo_mysql' => extension_loaded('pdo_mysql'),
			'gd' => extension_loaded('gd'),
            'openssl' => extension_loaded('openssl'),
);

$req_html_arr = array(
		'php' => "PHP v5.3 or greater",
		"pdo_mysql" => "PDO-Mysql extension for PHP (pdo_mysql)",
		"gd" => "GD Library (gd)",
        'openssl' => "Open SSL (openssl)"
);
$stat_arr = array(0=> "No",1 => "Yes");
chdir("../");
$writable_paths = array(
    getcwd().DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."downloads",
    getcwd().DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."uploads",
    getcwd().DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."email_constants.php",
    getcwd().DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."emptabconfigure.php",
    getcwd().DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."site_constants.php",
    getcwd().DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."db_constants.php",
    getcwd().DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."application_constants.php",
    getcwd().DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."mail_settings_constants.php",
    getcwd().DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR."application.log",
    getcwd().DIRECTORY_SEPARATOR."application".DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."default".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."AccessControl.php",
	getcwd().DIRECTORY_SEPARATOR."install"
);
?>
<form name="frmstep1" id="frmstep1" method="post" action="" class="frm_install">
    <h3 class="page_title">Pre-requisites</h3>
    <div class="content_part">
<ul class="progress">
<?php 
    $cnt = 0;
	foreach($req_arr as $req => $req_value)
	{
            if($req_value == 0)
                $cnt ++;
                
            if($req_value == 1)    
             $statusclass = 'status_yes';
            else
             $statusclass = 'status_no'; 
?>
		<li>
			<?php echo $req_html_arr[$req];?> 
			<div class="<?php echo $statusclass;?>">
				<?php echo $stat_arr[$req_value];?>
			</div>
			<?php if($req_value == 0){?>
					<?php if($req == 'php' ){?>
				   			<a href="<?php echo PHPURL;?>" target="_blank" style="text-decoration: none;"><div class="error-txt" id = "phplink">Current php version does not comply with installation process.</div></a>
				    <?php } else if($req == 'pdo_mysql') {?>
				           <a href="<?php echo PDOURL;?>" target="_blank" style="text-decoration: none;"><div class="error-txt" id = "phplink">PDO-Mysql extension is disabled in your php.ini file.</div></a>		
				    <?php } else if($req == 'gd') {?>
		                   <a href="<?php echo GDURL;?>" target="_blank" style="text-decoration: none;"><div class="error-txt" id = "phplink">GD Library module is disabled in your php.ini file.</div></a>	
		             <?php } else if($req == 'openssl') {?>
		                   <a href="<?php echo OPENSSLURL;?>" target="_blank" style="text-decoration: none;"><div class="error-txt" id = "phplink">Open SSL module is disabled in your php.ini file.</div></a>                        
			<?php }}?> 
		</li>
<?php 		
	}
        $with_permission = array();
        $without_permission = array();
        foreach($writable_paths as $path)
        {
            if(!is_writable($path))
            {
                $without_permission[] = $path;
                $cnt++;      
            }
            else 
            {
                $with_permission[] = $path;
            }
        }
?>		
		</ul>
        <div class="folder_title">Files and Folders permissions</div>
         <ul class="progress">
<?php		
        if(count($with_permission) > 0)
        {
?>
        
       
<?php                 
            foreach($with_permission as $path)
            {
    ?>
            <li><div class="folderstructure"><?php echo $path;?></div><div class="status_yes"></div><div class="clear"></div></li>     
    <?php                 
            }
        }?>
	
<?php		
        if(count($without_permission) >0)
        {
?>
        <!--<div class="folder_title">Folders/files needed permissions.</div>-->
        
<?php                 
            foreach($without_permission as $path)
            {
    ?>
            <li><div class="folderstructure"><?php echo $path;?></div><div class="status_no"></div><div class="clear"></div></li>     
    <?php                 
            }
        }
?>
        
</ul>     
</div>                   
<?php 
if($cnt == 0)
{
?>
    <button name="next" id="next" type="button" onclick="window.location='index.php?s=<?php echo sapp_Global::_encrypt(2);?>';">Next</button>
<?php 
}
  
?>    
    </form>
    
  
    <script type="text/javascript">
		$(document).ready(function(){
			
			<?php if($cnt == 0){ ?>
			$(".first_li").addClass('active');
			$(".first_icon").addClass('yes');
			<?php }else{ ?>
				$(".first_li").addClass('current');
				$(".first_icon").addClass('loding_icon');
			<?php }?>

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
			<?php }?>
			
			<?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME') && defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_AUTH') && defined('MAIL_TLS')){ ?>
			$(".fifth_li").addClass('active');
			$(".fifth_icon").addClass('yes');
			<?php }?>
			
		});
</script>
