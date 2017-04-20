<?php
header("Access-Control-Allow-Origin: *");

//Connect & Select Database
$con = mysqli_connect("eu-cdbr-azure-north-e.cloudapp.net","68c0ba5cbf74ef7","68c0ba5cbf74ef7","acsm_7e1519900d8edad") or die("could not connect server");

//Create New Account
if(isset($_POST['signup']))
{
	// Avoiding SQL Injection & removing spl chars
	$fullname=$_POST['fullname']);
	$email=$_POST['email'];
	$heightincm=$_POST['heightincm'];
	$password=$_POST['password']);

	$heightInCM_INT=(int)$heightincm;
	
	//validating existing user
	$validate = mysqli_num_rows(mysqli_query($con,"SELECT * FROM `phonegap_login` WHERE `email`='$email'"));
	
	// echo $email;
	if($validate != 0)
	{
		echo "exist";
	}
	else
	{
		$date=date("d-m-y h:i:s");
		$q=mysqli_query($con,"INSERT INTO `phonegap_login` (`reg_date`,`fullname`,`email`,`password`, `height_of_container_cm`) VALUES ('$date','$fullname','$email','$password', '$heightInCM_INT')");
		if($q)
		{
			echo "success";
		}
		else
		{
			echo "error";
		}
	}
}

//Login
else if(isset($_POST['login']))
{
	$email=$_POST['email'];
	$password=$_POST['password'];
	$login=mysqli_query($con,"SELECT * FROM `phonegap_login` WHERE `email`='$email' AND `password`='$password'");
	if($login!=0)
	{
		echo "success";
	}
	else
	{
		echo "error";
	}
}
}
echo mysqli_error($con);
?>