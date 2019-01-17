<?php
   require("dbcontroller.php");
   $db_handle = new DBController();

if(isset($_GET["email"]))
{
	$email= $_GET['email'];
	$name=$_GET['name'];
	$contact=$_GET["contact_number"];
	$insertedon= date('Y-m-d H:i:s');

	$query="SELECT count(*) as cnt from `client_team`";

	$count=$db_handle->runQuery($query);
	foreach ($count as $cnt) {
		$cont = $cnt['cnt'];
		$cont++;
	}
	
	$query="INSERT INTO `client_team`(`id`,`name`, `contact_number`, `email`, `status`, `parent`, `inserted_on`) VALUES ('$cont','$name','$contact','$email','0','12','$insertedon')";

      $result = $db_handle->insQuery($query);
      if($result)
      {
      	$json['success'] = 1;
      }
      else
      {
      	$json['success'] = 0;
      }
	}

	$json_array = $json;
	echo json_encode($json_array);	
?>