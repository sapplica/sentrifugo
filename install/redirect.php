<?php 
if(isset($_SERVER['HTTP_USER_AGENT']))
	header('Location: ../index.php');
else
	echo 'yes';
?>