<?php
	include('connect_db.php');
	$user_username = $_COOKIE['logged_username'];
	$creator_branch_code = $_COOKIE['logged_username_branch_code'];

	$customer = htmlentities(mysqli_real_escape_string($connect_link,$_POST['customer']));
	$customer_company = htmlentities(mysqli_real_escape_string($connect_link,$_POST['customer_company']));

	$invoice_num = htmlentities(mysqli_real_escape_string($connect_link, $_POST['invoice_num']));

	$return_type = htmlentities(mysqli_real_escape_string($connect_link, $_POST['return_type']));
	$return_brand = htmlentities(mysqli_real_escape_string($connect_link, $_POST['return_brand']));
	$return_model_name = htmlentities(mysqli_real_escape_string($connect_link, $_POST['return_model_name']));
	$return_model_number = htmlentities(mysqli_real_escape_string($connect_link, $_POST['return_model_number']));
	$return_hsn_code = htmlentities(mysqli_real_escape_string($connect_link, $_POST['return_hsn_code']));

	$return_note = htmlentities(mysqli_real_escape_string($connect_link, $_POST['return_note']));

	$create_user_query = "INSERT INTO returns VALUES('', '$user_username', '$creator_branch_code', '$customer', '$customer_company', '$invoice_num', '$return_brand', '$return_model_name', '$return_model_number', '$return_hsn_code', '$return_type', '$return_note', now())";
	if($create_user_query_run = mysqli_query($connect_link, $create_user_query))
	{
		echo 1;
	}
	else
	{
		echo 0;
	}

?>