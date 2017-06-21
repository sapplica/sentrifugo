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

require_once 'application/modules/default/library/sapp/Global.php';
require_once 'public/constants.php';
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
    <link href="<?php echo MEDIA_PATH; ?>jquery/css/cupertino/jquery-ui-1.8.16.custom.css" media="screen" rel="stylesheet" type="text/css" >
    <script type="text/javascript" src="<?php echo MEDIA_PATH;?>jquery/js/jquery-1.7.1.min.js"></script> 
	<script type="text/javascript" src="<?php echo MEDIA_PATH;?>jquery/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="<?php echo MEDIA_PATH;?>jquery/js/jquery-ui-1.10.3.custom.js"></script>
	<script type="text/javascript" src="<?php echo MEDIA_PATH;?>js/hrmsv2.js"></script>
	<script type="text/javascript" src="<?php echo MEDIA_PATH; ?>jquery/js/jquery.blockUI_2.64.js"></script>

</head>
  <body>
      <div class="container" >
      	<div id="upgradespinner" class="ajax-loader" style="display:none;" >
		    <img id="img-spinner" src="<?php echo MEDIA_PATH;?>images/loader2.gif" alt="Loading" />				
		</div>
      	<div class="header"> <div class="logo"></div></div>
        
        <div class="content_wrapper">
        
<?php  

$file = PARENTDOMAIN;
$file_headers = @get_headers($file);
if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
    $exists = false;
}
else {
    $exists = true;
}
if($exists)
{
		if(!empty($_POST))
		{
		     $codeversion = isset($_POST['codeversion'])?$_POST['codeversion']:'';
		     $dbversion = isset($_POST['dbversion'])?$_POST['dbversion']:'';
			 $dbid = isset($_POST['dbid'])?$_POST['dbid']:'';
			 $codeid = isset($_POST['codeid'])?$_POST['codeid']:'';
			if($codeversion !='' && $dbversion !='' && $dbid !='' && $codeid !='')
			{
			?>
            			<?php if($codeid > $dbid){?>
            					<div class="show-text"> 
            						Your database does not support the current code version. 
            						Please take a backup of database and code before upgarding the system.	
            					</div>
                                <div class="upgrade-div">
	            					<div id='upgradedb' onclick="upgradesystem('<?php echo WEBSERVICEURL;?>','db','<?php echo $codeversion;?>','<?php echo $dbversion;?>')"><span>UPGRADE DATABASE</span></div>
            					</div>
            			<?php }else if($codeid < $dbid){?>
            					<div class="show-text">
	            					 Your code version doesnot support your current database version.
									 Please take a backup of your database and code and upgrade the system.
            					</div>
            					<div class="upgrade-div">
	                                <div id='upgradecode' onclick="upgradesystem('<?php echo WEBSERVICEURL;?>','code','<?php echo $codeversion;?>','<?php echo $dbversion;?>')"><span>UPGRADE CODE</span></div>
                                </div>
            			<?php }?>
            			
            			<div id ='successpan' class=""></div>
			<?php }else{ ?>
			<div id ='successpan' class=""></div>
				<?php if($codeversion !='' && $dbversion !=''){ ?>
					<script>
						comapareversions('<?php echo WEBSERVICEURL;?>','<?php echo $codeversion;?>','<?php echo $dbversion;?>');
					</script>
					
					<?php }else{?>	
								<div class="show-text">
	            					 Data is not available. Please take a backup of your database and code and upgrade the system again.
								</div>
					
					<?php }?>
			<?php }
		}else
		{ 
			header("Location: index.php");	
		 }
		?>  
<?php }else{?>
			<div class="error_mess">Your application is not updated .Please check your internet connection to update or visit sentrifugo.com to update your system.</div>
<?php }?>		
		       	
        </div>
      </div>
  </body>
</html>
