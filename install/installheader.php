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
<script type="text/javascript" src="../public/media/jquery/js/jquery-1.7.1.min.js"></script> 
<script type="text/javascript" src="../public/media/jquery/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../public/media/jquery/js/jquery-ui-1.10.3.custom.js"></script>

<link href="../public/media/css/jquery-ui-1.10.4.css"	rel="stylesheet" type="text/css" />
<?php 	
require_once '../application/modules/default/library/sapp/Global.php';
require_once '../public/db_constants.php';
require_once '../public/constants.php';
require_once '../public/mail_settings_constants.php';
require 'PHPMailer/PHPMailerAutoload.php';
?>