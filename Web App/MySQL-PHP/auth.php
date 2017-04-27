<?php
header("Access-Control-Allow-Origin: *");

//Connect & Select Database
$con = mysqli_connect("localhost","root","","usersECOWATER") or die("could not connect server");

//Create New Account
if(isset($_POST['signup']))
{
	// Avoiding SQL Injection & removing spl chars
	$fullname=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['fullname'])));
	$email=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['email'])));
	$heightincm=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['heightincm'])));
	$password=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['password'])));

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
	$email=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['email'])));
	$password=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['password'])));
	$login=mysqli_num_rows(mysqli_query($con,"SELECT * FROM `phonegap_login` WHERE `email`='$email' AND `password`='$password'"));
	if($login!=0)
	{
		echo "success";
	}
	else
	{
		echo "error";
	}
}

//Change Password
else if(isset($_POST['change_password']))
{
	$email=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['email'])));
	$old_password=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['old_password'])));
	$new_password=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['new_password'])));
	$check=mysqli_num_rows(mysqli_query($con,"SELECT * FROM `phonegap_login` WHERE `email`='$email' AND `password`='$old_password'"));
	if($check!=0)
	{
		mysqli_query($con,"UPDATE `phonegap_login` SET `password`='$new_password' WHERE `email`='$email'");
		echo "success";
	}
	else
	{
		echo "incorrect";
	}
}

// Forget Password
else if(isset($_POST['forget_password']))
{
	$email=mysqli_real_escape_string($con,htmlspecialchars(trim($_POST['email'])));
	$q=mysqli_query($con,"SELECT * FROM `phonegap_login` WHERE `email`='$email'");
	$check=mysqli_num_rows($q);
	if($check!=0)
	{
		echo "success";
		$data=mysqli_fetch_array($q);
		$string="Hey,".$data['fullname'].", Your password is".$data['password'];
		mail($email, "Your Password", $string);
	}
	else
	{
		echo "invalid";
	}
}
echo mysqli_error($con);
?>