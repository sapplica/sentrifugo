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
 	
require_once '../application/modules/default/library/sapp/Global.php';
require_once '../public/db_constants.php';
require_once '../public/constants.php';
require_once '../public/mail_settings_constants.php';
require_once '../public/application_constants.php';
require 'PHPMailer/PHPMailerAutoload.php';;
ini_set('display_errors', '1');
ini_set('max_execution_time',0);

	
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sentrifugo</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="shortcut icon" href="../public/media/images/favicon.ico" />
    <link rel="stylesheet" type="text/css"	href="../public/media/css/select2.css"/>
    <link href="css/style.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic,300,300italic,100italic,100,700italic,900,900italic' rel='stylesheet' type='text/css'>
	<link href="../public/media/css/jquery.alert.css"	rel="stylesheet" type="text/css" />	
     <!--[if IE 8]>  
	<link rel="stylesheet" type="text/css" href="../public/media/css/ie8.css">  
	<![endif]-->     
    <script type="text/javascript" src="../public/media/jquery/js/jquery-1.7.1.min.js"></script> 
	<script type="text/javascript" src="../public/media/jquery/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="../public/media/jquery/js/jquery-ui-1.10.3.custom.js"></script>
	<script  language="JavaScript" type="text/javascript" src="../public/media/jquery/js/select2.js" ></script><!-- added on 07-aug-2013 by rama krishna -->
	<script type="text/javascript" src="../public/media/jquery/js/jquery.blockUI_2.64.js"></script>
	<script type="text/javascript" language="javascript" src="../public/media/jquery/js/jquery.alert.js"></script>
	
	<script type="text/javascript">
	$(document).ready(function(e){
		$("#submitbutton,#next,#previous,#idbtnfinish").click(function(){ 
	    	$.blockUI({ width:'50px',message: $("#spinner").html() });
	    });

		navigator.sayswho= (function(){
		    var N= navigator.appName, ua= navigator.userAgent, tem;
		    var M= ua.match(/(opera|chrome|safari|firefox|msie)\/?\s*(\.?\d+(\.\d+)*)/i);
		    if(M && (tem= ua.match(/version\/([\.\d]+)/i))!= null) M[2]= tem[1];
		    M= M? [M[1], M[2]]: [N, navigator.appVersion, '-?'];

		    return M;
		})();
		var version = parseInt(navigator.sayswho[1]);
		       
		   if(navigator.userAgent.match(/firefox/i) == 'Firefox')
		    {
			  if(version<5)
			  { 
				window.location = "browserfailure.php";
			  }	
		    }
		    else if(navigator.userAgent.match(/msie/i) == 'MSIE')
		    {        
		         if(version<8)      
		        	 window.location = "browserfailure.php";
		    }
		    else if(navigator.userAgent.match(/chrome/i) == 'Chrome')
		    {
		       if(version<13)
		    	   window.location = "browserfailure.php";
		    }
		    else if(navigator.userAgent.match(/safari/i) == 'Safari' && navigator.userAgent.match(/Android/i) != 'Android')
		    {
		       if(version<5)
		    	   window.location = "browserfailure.php";
		    }
		    else if(navigator.userAgent.match(/opera/i) == 'Opera')
		    {
		       if(version<12)
		    	   window.location = "browserfailure.php";
		    }
	    
	});

	</script>
</head>
  <body>
      <div class="container">
      <div id="spinner" class="ajax-loader" style="display:none;" >
								<img id="img-spinner" src="../public/media/images/loader3.gif" alt="Loading" />				
	</div>
      	<div class="header"> <div class="logo"></div></div>
        <div class="left_steps">
        	<h3 class="steps_title">Configuration</h3>
        	<ul class="steps_ul">
            	<li class="first_li">
                <h4>Pre-requisites</h4>
                <div class="first_icon icon "></div>
                <span>Software requirements and permissions for files and folders</span>
                </li>
                <li class="second_li">
                <h4>Database Settings</h4>
                <div class="second_icon icon "></div>
                <span>Configure database for your application</span>
                </li>
                <li class="third_li">
                <h4>Application Settings</h4>
                <div class="third_icon icon"></div>
                <span>Configure application name and super admin credentials</span>
                 </li>
                <li class="fourth_li">
                <h4>Mail Server Settings</h4>
                <div class="fourth_icon icon"></div>
                <span>Configure your mail server to get automated mails</span>
                </li>
                <li class="fifth_li">
                <h4>Final Check</h4>
                <div class="fifth_icon icon"></div>
                <span>Confirm and complete the installation process</span>
                </li>
            </ul>
        </div>
        <div class="content_wrapper">
            
            <?php 
            if(isset($_GET['s']) && $_GET['s'] !='')
	{
		$redirectUrl = sapp_Global::_decrypt($_GET['s']); 
		require_once 'step'.$redirectUrl.'.php';
	}	
	else 
		require_once 'step1.php';
            ?>
        </div>
      </div>
  </body>
</html>