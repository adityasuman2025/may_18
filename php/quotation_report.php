<?php
	session_start();
	include('connect_db.php');
	$creator_branch_code = $_COOKIE['logged_username_branch_code'];
?>

<!-----for excel,csv generation------>
	<script type="text/javascript" src="js/FileSaver.min.js"></script>
	<script type="text/javascript" src="js/tableexport.min.js"></script>

	<script type="text/javascript">
		$('#table_export').tableExport();
		//$('#table_export2').tableExport();
	</script>

<!------------page area---------->
	<br>

	<div class="inventory_list_container">
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
				<th>Quotation Number</th>
				<th>Customer Number</th>
				<th>Date of Generation</th>
				<th>Total Amount</th>
				<th>Created By</th>
				<th>Actions</th>
			</tr>

			<?php
			//getting total count of quotation num at that branch			
				$count_quotation_num_query = "SELECT quotation_num FROM quotation WHERE creator_branch_code = '$creator_branch_code' AND payment_method !='' GROUP BY quotation_num ";
				if($count_quotation_num_query_run = mysqli_query($connect_link, $count_quotation_num_query))
				{
					$count_quotation_num =  mysqli_num_rows($count_quotation_num_query_run);
				}
				else
				{
					$count_quotation_num = 0;
				}

			//setting limits of shown results	
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

			//setting limits of shown results	
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

				$manage_customer_query = "SELECT * FROM quotation WHERE creator_branch_code ='$creator_branch_code' AND payment_method='' AND date >= '$date_lower_limit' AND date <= '$date_uper_limit' GROUP BY quotation_num ORDER BY quotation_num DESC LIMIT " . $lower_limit . ", " . $uper_limit;
				$manage_customer_query_run = mysqli_query($connect_link, $manage_customer_query);

				while($manage_customer_result = mysqli_fetch_assoc($manage_customer_query_run))
				{
					$quotation_id = $manage_customer_result['id'];

					$quotation_num = $manage_customer_result['quotation_num'];
					$customer = $manage_customer_result['customer'];
					$date = $manage_customer_result['date'];
					$type = $manage_customer_result['type'];
					$creator_username = $manage_customer_result['creator_username'];

				//gettting date of generation of quoatation
					$date = str_replace('/', '-', $date);
					$date = date('d M Y', strtotime($date));

				//for getting quotation code
					$this_year = date('y');
					$next_year = $this_year +1;
					
					$comp_code = $_SESSION["comp_code"];	

					$quotation_code = $comp_code . $this_year . "-" . $next_year . "/" . $quotation_num;

				//for getting total price of the quotation
					$final_price = 0;

					$get_element_price_query = "SELECT total_price FROM quotation WHERE quotation_num='$quotation_num'";
					$get_element_price_query_run = mysqli_query($connect_link, $get_element_price_query);

					while($get_element_price_assoc = mysqli_fetch_assoc($get_element_price_query_run))
					{
						$element_price = $get_element_price_assoc['total_price'];

						$final_price = $final_price + $element_price;
					}

					echo "<tr>";
						echo "<td>$quotation_code</td>";
						echo "<td>$customer</td>";
						echo "<td>$date</td>";
						echo "<td>$final_price</td>";
						echo "<td>$creator_username</td>";
						echo "<td>";
							echo "<img quotation_num=\"$quotation_num\" class=\"user_view_icon\" src=\"img/view.png\"/>";
							
						echo "</td>";
					echo "</tr>";
				
				}
				
			?>

		</table>
		<br><br>

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
	</div>


<!---script------>
	<script type="text/javascript">
	//on clicking on search date button
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
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/quotation_report.php');
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
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/quotation_report.php');
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
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/quotation_report.php');
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
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/quotation_report.php');
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
							$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/quotation_report.php');
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
					$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/quotation_report.php');
				});		
			});
	
		});

	//on clicking on view icon
		$('.user_view_icon').click(function()
		{
			var quotation_num =  $.trim($(this).attr('quotation_num'));

		//for getting pdf of the quotation
			var session_of = quotation_num;
			var session_name = "pdf_quotation_of";
				
			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
				if(e ==1)
				{
					window.open('php/quotation_pdf.php', '_blank');	
				}
				else
				{
					$('.warn_box').text("Something went wrong while generating pdf file of the quotation.");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});

			//window.open('php/quotation_pdf.php', '_blank');	
		});
	
	</script>
