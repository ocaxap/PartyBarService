<?php
session_start(); 
$name = $_POST["name"];
$message = $_POST["message"];
$email = $_POST["email"];
$success = false;

if ($name != "" && $message != "" && $email != "")
{	
	if (mail('partybarservice@hotmail.com', 'PartyBarService.by', "e-mail: ".$email."\nИмя: ".$name."\nТекст сообщения: ".$message))
		$success = true;
}
else
{	
	$success = false;	
}
header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");  
print json_encode($success);
?>