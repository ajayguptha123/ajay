<?php
  require("dbcontroller.php");
  $db_handle = new DBController();

if(isset($_GET["email"]) && isset($_GET["password"]))
{
	$email= $_GET['email'];
	$password= $_GET['password'];
	
	$query = "SELECT `client_or_company`,`department`,`role`,`status` FROM `client_leads` where `email` = '$email' AND 
	`password` = '$password' ";
	
	$result = $db_handle->runQuery($query);
	
	$count = $db_handle->numRows($query);

		if($count > 0)
		{
	      
			foreach ($result as $clientinfo) 
            {
            	$status = $clientinfo['status'];
            	if($status != 0)
            	{
            		$json['success'] = 1;
            		$json['message'] = "logged in successfully";
            		$json['company_name'] = $clientinfo['client_or_company'];
            		$json['department'] = $clientinfo['department'];
            		$json['role'] = $clientinfo['role'];
            	}
            	else
            	{
            		$json['success'] = 0;
            		$json['message'] = 'your account is not active please contact foodni support or wait for 72 hours';
            	}
            }

		}

		else
		{
			$json['success'] = 0;
       		$json['message'] = 'invalid emailid or password';
		}
	}

	$json_array = $json;
	echo json_encode($json_array);	
?>