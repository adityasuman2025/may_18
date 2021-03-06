<?php
	session_start();
	include('connect_db.php');
	$creator_branch_code = $_COOKIE['logged_username_branch_code'];
?>

<!-----for exce,csv generation------>
	<script type="text/javascript" src="js/FileSaver.min.js"></script>
	<script type="text/javascript" src="js/tableexport.min.js"></script>

	<script type="text/javascript">
		$('#table_export').tableExport();
	</script>

<!------------page area---------->
	<div class="inventory_list_container">
		<br>
	<!---------get individual report------->
		<div class="report_type_div">
			<h4>Get Individual Report For:</h4>
			<button type="*" class="all_report_button">All</button>
			<button type="product" class="product_report_button">Product</button>
			<button type="part" class="part_report_button">Part</button>
			<button type="service" class="service_report_button">Service</button>
		</div>
		<br>

	<!--------search date area--------->
		<div class="search_date_div">
			<button class="today_report_button">Today's</button>
			<button class="this_week_report_button">This Week</button>
			<button class="this_month_report_button">This Month</button>
			<br><br>

			<b style="font-size: 120%;">From: </b> 
			<input type="date" class="date_lower_limit"> 

			<b style="font-size: 120%;">To: </b> 
			<input type="date" class="date_uper_limit"> 

			<button class="search_date_button">Search</button>
			<br><br>

			<?php
				if(isset($_SESSION['date_lower_limit']) && isset($_SESSION['date_uper_limit']))
				{
					$date_lower_limit = $_SESSION['date_lower_limit'];
					$date_uper_limit = $_SESSION['date_uper_limit'];

				//getting date lower limit
					$date_lower_limit = str_replace('/', '-', $date_lower_limit);
					$date_lower_limit = date('d M Y', strtotime($date_lower_limit));

				//getting date uper limit
					$date_uper_limit = str_replace('/', '-', $date_uper_limit);
					$date_uper_limit = date('d M Y', strtotime($date_uper_limit));

					echo "<span>Showing results from <b>$date_lower_limit</b> to <b>$date_uper_limit</b></span>";
				}
			?>
		</div>
		<br>

	<!--------table area--------->
		<table id="table_export" class="list_inventory_table">				
			<tr>
				<th>Invoice Number</th>
				
				<?php
					if(isset($_SESSION['report_type']))
					{
						$report_type = $_SESSION['report_type'];

						if($report_type != '*')
						{
							echo "<th>Type</th>";
						}
					}
				?>

				<th>Customer Name</th>
				<th>Company Name</th>
				<th>Phone Number</th>
				<th>Email ID</th>

				<th>Purchase Order</th>
				<th>Date of Generation</th>
				<th>Total Amount</th>
				<th>Total Tax</th>

				<th>Payment Method</th>
				<th>Payment Date</th>
				<th>Created By</th>

				<?php
					if(isset($_SESSION['report_type']))
					{
						$report_type = $_SESSION['report_type'];

						if($report_type == '*')
						{
							echo "<th>Actions</th>";
						}
					}
					else
					{
						echo "<th>Actions</th>";
					}		
				?>
			</tr>

			<?php

				//getting total count of quotation num at that branch
					$count_quotation_num_query = "SELECT quotation_num FROM quotation WHERE creator_branch_code = '$creator_branch_code' AND payment_method !=''";
					if($count_quotation_num_query_run = mysqli_query($connect_link, $count_quotation_num_query))
					{
						$count_quotation_num =  mysqli_num_rows($count_quotation_num_query_run);
					}
					else
					{
						$count_quotation_num = 0;
					}

				//setting quantity limits of shown results	
					$gap = 25;

					if(isset($_SESSION['lower_limit']) && isset($_SESSION['uper_limit']))
					{
						$lower_limit = $_SESSION['lower_limit'];
						$uper_limit = $_SESSION['uper_limit'];
					}
					else
					{
						$lower_limit = 0;
						$uper_limit = 25;
					}

				//setting time limits of shown results	
					if(isset($_SESSION['date_lower_limit']) && isset($_SESSION['date_uper_limit']))
					{
						$date_lower_limit = $_SESSION['date_lower_limit'];
						$date_uper_limit = $_SESSION['date_uper_limit'];
					}
					else
					{
						$date_lower_limit = "0-0-0";
						$date_uper_limit = date('Y-m-d');
					}

				//showing result
					$user_username = $_COOKIE['logged_username'];
					$creator_branch_code = $_COOKIE['logged_username_branch_code'];

					//checking if to show all reports or for a particular type
						if(isset($_SESSION['report_type']))
						{					
							$report_type = $_SESSION['report_type'];

							if($report_type == '*')
							{								
								$manage_customer_query = "SELECT * FROM quotation WHERE creator_branch_code ='$creator_branch_code' AND payment_method !='' AND date >= '$date_lower_limit' AND date <= '$date_uper_limit' ORDER BY quotation_num DESC LIMIT " . $lower_limit . ", " . $uper_limit;
							}
							else
							{
								$manage_customer_query = "SELECT * FROM quotation WHERE creator_branch_code ='$creator_branch_code' AND payment_method !='' AND date >= '$date_lower_limit' AND date <= '$date_uper_limit' AND type ='$report_type' ORDER BY quotation_num DESC LIMIT " . $lower_limit . ", " . $uper_limit;
							}
						}
						else
						{
							$manage_customer_query = "SELECT * FROM quotation WHERE creator_branch_code ='$creator_branch_code' AND payment_method !='' AND date >= '$date_lower_limit' AND date <= '$date_uper_limit' GROUP BY quotation_num ORDER BY quotation_num DESC LIMIT " . $lower_limit . ", " . $uper_limit;
						}		

						$manage_customer_info_query_run = mysqli_query($connect_link, $manage_customer_query);
						
					//getting customer info
						$manage_customer_result = mysqli_fetch_assoc($manage_customer_info_query_run);
						$customer = $manage_customer_result['customer'];

						$get_customer_info_query = "SELECT * FROM customers WHERE name = '$customer'";
						$get_customer_info_query_run = mysqli_query($connect_link, $get_customer_info_query);

						if($get_customer_info_result = mysqli_fetch_assoc($get_customer_info_query_run))
						{
							$customer_company = $get_customer_info_result['company_name'];
							$customer_mobile = $get_customer_info_result['mobile'];							
							$customer_email = $get_customer_info_result['email'];
						}

					//getting the invoice details
						$sum_of_money = 0;

						$manage_customer_query_run = mysqli_query($connect_link, $manage_customer_query);
						while($manage_customer_result = mysqli_fetch_assoc($manage_customer_query_run))
						{
							$quotation_id = $manage_customer_result['id'];

							$quotation_num = $manage_customer_result['quotation_num'];
							$purchase_order = $manage_customer_result['purchase_order'];

							$date = $manage_customer_result['date'];
							$type = $manage_customer_result['type'];
							
							$payment_method = $manage_customer_result['payment_method'];
							$date_of_payment = $manage_customer_result['date_of_payment'];

							$creator_username = $manage_customer_result['creator_username'];

						//gettting date of generation of quoatation
							$date = str_replace('/', '-', $date);
							$date = date('d M Y', strtotime($date));

						//gettting date of payment of quotation
							$date_of_payment = str_replace('/', '-', $date_of_payment);
							$date_of_payment = date('d M Y', strtotime($date_of_payment));

							if($date_of_payment == "01 Jan 1970" OR $date_of_payment == "30 Nov -0001" OR $date_of_payment == "")
							{
								$date_of_payment = "Not Paid";
							}

						//for getting quotation code
							$this_year = date('y');
							$next_year = $this_year +1;
							
							$comp_code = $_SESSION["comp_code"];	

							$quotation_code = $comp_code . $this_year . "-" . $next_year . "/" . $quotation_num;

						//for getting total price and total tax of the quotation
							$final_price = 0;
							$final_tax = 0;

							$get_element_price_query = "SELECT * FROM quotation WHERE quotation_num='$quotation_num'";
							$get_element_price_query_run = mysqli_query($connect_link, $get_element_price_query);

							while($get_element_price_assoc = mysqli_fetch_assoc($get_element_price_query_run))
							{
							//getting total tax
								$item_quantity = $get_element_price_assoc['quantity'];
								$item_rate = round($get_element_price_assoc['rate'], 2);

								$item_discount = $get_element_price_assoc['discount'];
								if($item_discount == "")
								{
									$item_discount = 0;
								}

								$discount_amount = $item_discount*$item_quantity*$item_rate/100;
								$net_price = $item_quantity*$item_rate - $item_discount*$item_quantity*$item_rate/100;

								$item_cgst = $get_element_price_assoc['cgst'];
								$item_sgst = $get_element_price_assoc['sgst'];
								$item_igst = $get_element_price_assoc['igst'];

								$cgst_amount = ($item_rate*$item_quantity - $discount_amount)*$item_cgst/100;
								$sgst_amount = ($item_rate*$item_quantity - $discount_amount)*$item_sgst/100;
								$igst_amount = ($item_rate*$item_quantity - $discount_amount)*$item_igst/100;

								$element_tax = $cgst_amount + $sgst_amount + $igst_amount;
								$final_tax = $final_tax + $element_tax;

							//getting total price
								$element_price = $get_element_price_assoc['total_price'];
								$final_price = $final_price + $element_price;
							}

							$sum_of_money = $sum_of_money + $final_price;

							echo "<tr>";
								echo "<td>$quotation_code</td>";

								if(isset($_SESSION['report_type']))
								{
									$report_type = $_SESSION['report_type'];

									if($report_type != '*')
									{
										echo "<td>$type</td>";
									}
								}

								echo "<td>$customer</td>";
								echo "<td>$customer_company</td>";
								echo "<td>$customer_mobile</td>";
								echo "<td>$customer_email</td>";

								echo "<td>$purchase_order</td>";
								echo "<td>$date</td>";
								echo "<td>$final_price</td>";
								echo "<td>$final_tax</td>";
								echo "<td>$payment_method</td>";
								echo "<td>$date_of_payment</td>";
								echo "<td>$creator_username</td>";

								if(isset($_SESSION['report_type']))
								{
									$report_type = $_SESSION['report_type'];

									if($report_type == '*')
									{
										echo "<td>";
											echo "<img quotation_num=\"$quotation_num\" class=\"user_view_icon\" src=\"img/view.png\"/>";
										echo "</td>";
									}
								}
								else
								{
									echo "<td>";
										echo "<img quotation_num=\"$quotation_num\" class=\"user_view_icon\" src=\"img/view.png\"/>";
									echo "</td>";
								}								
							echo "</tr>";				
						}
			?>

		</table>
		<br>

	<!--showing limit of showing invoices quantity--> 
		<?php			
			if($lower_limit > 0)
			{
				echo " <button class=\"go_back_button\">Back</button> ";
			}

			if($uper_limit < $count_quotation_num)
			{
				echo " <button class=\"go_next_button\">Next</button> ";
			}

			echo "<br>";
			echo $lower_limit . " - " . $uper_limit ;
		?>

	<!--showing total sum of the money for that particular report--> 
		<?php
			if(isset($_SESSION['report_type']))
			{
				echo "<div class=\"sum_of_money_div\">";
					echo "<h3>Total Sum of Money during this period is: Rs.$sum_of_money </h3>";
				echo "</div>";
			}
		?>

	</div>


<!---script------>
	<script type="text/javascript">
	//on choosing a report type
		$('.report_type_div button').click(function()
		{
			var report_type = $(this).attr('type');

		//setting session for report type
			var session_of = report_type;
			var session_name = "report_type";

			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
				if(e==1)
				{
					$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/invoice_report.php');
				}
				else
				{
					//alert(e);
					$('.warn_box').text("Something went wrong while setting the report type");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});
		});

	//on clicking on search button
		$('.search_date_button').click(function()
		{
			date_lower_limit = $('.date_lower_limit').val();
			date_uper_limit = $('.date_uper_limit').val();

		//setting up date lower limit
			var session_of = date_lower_limit;
			var session_name = "date_lower_limit";

			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
				if(e==1)
				{
				//setting up date uper limit
					var session_of = date_uper_limit;
					var session_name = "date_uper_limit";

					$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
					{
						if(e==1)
						{
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/invoice_report.php');
						}
						else
						{
							//alert(e);
							$('.warn_box').text("Something went wrong while setting date limits");
							$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
						}
					});
				}
				else
				{
					//alert(e);
					$('.warn_box').text("Something went wrong while setting date limits");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});
		});

	//on clicking on todays button
		$('.today_report_button').click(function()
		{
		//getting todays date
			var d = new Date();
			var strDate = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
			
			date_lower_limit = strDate;
			date_uper_limit = strDate;

		//setting up date lower limit
			var session_of = date_lower_limit;
			var session_name = "date_lower_limit";

			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
				if(e==1)
				{
				//setting up date uper limit
					var session_of = date_uper_limit;
					var session_name = "date_uper_limit";

					$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
					{
						if(e==1)
						{
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/invoice_report.php');
						}
						else
						{
							//alert(e);
							$('.warn_box').text("Something went wrong while setting date limits");
							$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
						}
					});
				}
				else
				{
					//alert(e);
					$('.warn_box').text("Something went wrong while setting date limits");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});		
		});

	//on clicking on this week button
		$('.this_week_report_button').click(function()
		{
		//getting todays date
			var today = new Date();
			var todays_date = today.getFullYear() + "-" + (today.getMonth()+1) + "-" + today.getDate();
			date_uper_limit = todays_date; //uper limit is today date

		//getting first day of this week date
			var today_day_of_the_week = new Date().getDay();  //0=Sun, 1=Mon, ..., 6=Sat
			var lastWeekDate = new Date(today.setDate(today.getDate() - today_day_of_the_week));
			var this_week_first_date = lastWeekDate.getFullYear() + "-" + (lastWeekDate.getMonth()+1) + "-" + lastWeekDate.getDate();
			
			date_lower_limit = this_week_first_date;
			
		//setting up date lower limit
			var session_of = date_lower_limit;
			var session_name = "date_lower_limit";

			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
				if(e==1)
				{
				//setting up date uper limit
					var session_of = date_uper_limit;
					var session_name = "date_uper_limit";

					$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
					{
						if(e==1)
						{
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/invoice_report.php');
						}
						else
						{
							//alert(e);
							$('.warn_box').text("Something went wrong while setting date limits");
							$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
						}
					});
				}
				else
				{
					//alert(e);
					$('.warn_box').text("Something went wrong while setting date limits");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});		
		});

	//on clicking on this month button
		$('.this_month_report_button').click(function()
		{
		//getting todays date
			var today = new Date();
			var todays_date = today.getFullYear() + "-" + (today.getMonth()+1) + "-" + today.getDate();
			date_uper_limit = todays_date; //uper limit is todays date

		//getting first day of this week date
			var todays_year =  today.getFullYear();
			var todays_month =  today.getMonth() + 1;

			var month_first_date = todays_year + "-" + todays_month + "-" + "1";
			//alert(month_first_date);
			date_lower_limit = month_first_date;
			
		//setting up date lower limit
			var session_of = date_lower_limit;
			var session_name = "date_lower_limit";

			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
				if(e==1)
				{
				//setting up date uper limit
					var session_of = date_uper_limit;
					var session_name = "date_uper_limit";

					$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
					{
						if(e==1)
						{
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/invoice_report.php');
						}
						else
						{
							//alert(e);
							$('.warn_box').text("Something went wrong while setting date limits");
							$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
						}
					});
				}
				else
				{
					//alert(e);
					$('.warn_box').text("Something went wrong while setting date limits");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});		
		});

	//on clicking on next button
		$('.go_next_button').click(function()
		{
			var gap = parseInt("<?php echo $gap; ?>");

		//changing lower limit
			var session_of = parseInt("<?php echo $lower_limit;?>") + gap;
			var session_name = "lower_limit";

			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
				if(e==1)
				{
					var gap = parseInt("<?php echo $gap; ?>");

				//changing uper limit
					var session_of = parseInt("<?php echo $uper_limit;?>") + gap;
					var session_name = "uper_limit";

					$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
					{
						if(e==1)
						{
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/invoice_report.php');
						}
						else
						{
							//alert(e);
							$('.warn_box').text("Something went wrong while setting limits");
							$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
						}
					});
				}
				else
				{
					//alert(e);
					$('.warn_box').text("Something went wrong while setting limits");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});
		});

	//on clicking on back button
		$('.go_back_button').click(function()
		{
			var gap = parseInt("<?php echo $gap; ?>");

		//changing lower limit
			var session_of = parseInt("<?php echo $lower_limit;?>") - gap;
			var session_name = "lower_limit";

			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
			//changing uper limit
				var session_of = parseInt("<?php echo $uper_limit;?>") - gap;
				var session_name = "uper_limit";

				$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
				{
					$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/invoice_report.php');
				});		
			});
	
		});

	//on clicking on view icon
		$('.user_view_icon').click(function()
		{
			var quotation_num =  $.trim($(this).attr('quotation_num'));

		//for getting pdf of the quotation
			var session_of = quotation_num;
			var session_name = "pdf_invoice_of";
				
			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
				if(e ==1)
				{
					window.open('php/invoice_pdf.php', '_blank');	
				}
				else
				{
					$('.warn_box').text("Something went wrong while generating pdf file of the quotation.");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});

			//window.open('php/invoice_pdf.php', '_blank');	
		});
	
	</script>
