<html>
<head>
	<?php
		include('php/header.php');

		if(isset($_COOKIE['logged_username']) && $_COOKIE['isadmin'] == '1')
		{
			//echo "gud";
		}
		else
		{
			die('wrong attempt is made to view this resource');
		}
	?>

	<title>Admin</title>
</head>
<body>

<!-----------menu bar----->
	<div class="user_module_menu_bar">
		<img class="menu_close" src="img/close.png">
		<br>

	<!----user info----->
		<div class="user_module_profile">
			<img src="img/profile.png">
			<br>
			<span>
				<?php
					echo $_COOKIE['logged_username'];
				?>	
			</span>
			<br><br>

		</div>

	<!----user module menu----->
		<ul class="user_menu">

			<li id="dashboard_button">Dashboard</li>

			<li title="Branch">Branch
				<ul>
					<li work="add_branch">Add Branch</li>
					<li work="manage_branch">Manage Branch</li>
				</ul>
			</li>

			<li title="User">User
				<ul>
					<li work="add_user">Add User</li>
					<li work="manage_user">Manage User</li>
				</ul>
			</li>

			<li title="Product/Service">Product/Service
				<ul>
					<li work="add_inventory">Add Product/Service</li>
					<li work="manage_inventory">Manage Product/Service</li>
				</ul>
			</li>

			<li title="Manage Customer" work="admin_manage_customer">Manage Customer</li>

			<li title="Manage Supplier" work="admin_manage_supplier">Manage Supplier</li>

			<li title="Manage Purchase" work="admin_manage_purchase">Manage Purchase</li>

			<li title="Manage Return" work="admin_manage_return">Manage Return</li>

			<li title="Manage Quotation" work="admin_manage_quotation">Manage Quotation</li>
			<li title="Manage Invoice" work="admin_manage_invoice">Manage Invoice</li>
			<li title="Manage Stock" work="admin_manage_stock">Manage Stock</li>

			<li title="Report">Report
				<ul>
					<li work="admin_quotation_report">Quotation Report</li>
					<li work="admin_invoice_report">Invoice Report</li>
					<li work="admin_stock_report">Stock Report</li>
				</ul>
			</li>

		</ul>
	</div>

<!-----user module title bar------->
	<div class="user_module_title">
		<div class="logo_menu">
			<img class="mob_menu_button" active="no" src="img/mob_menu.png">
			<img class="company_logo" src="img/logo.png">
		</div>

		<div class="user_logout">
			<img class="user_logout_button" src="img/logout.png">	
		</div>
	</div>

<!-----------user module area----->
	<div class="user_module_area">
	
	<!-----user module content area------->
		<h3 class="user_module_heading"></h3>
		<div class="user_module_content">
			
		</div>
	</div>

<!--------script-------->
	<script type="text/javascript">
	//on clicking on logout button
		$('.user_logout_button').click(function()
		{
			$.post('php/logout.php', {}, function(e)
			{
				if(e==1)
				{
					location.href ='index.php';
				}
				else //warn_box will appear with the error
				{
					$('.warn_box').text("Something went wrong while logging you out");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});
		});

	//on selecting on menu
		$('.user_menu li').click(function()
		{
			$(this).css('background', '#3e454c').css('border-left', '5px solid #cc0000');
			$('.user_menu li').not(this).css('background', 'none').css('border-left', 'none');
		});

	//for showing sub menu
		$('.user_menu li').click(function()
		{
			var selected_li_display = $(this).find('ul').css('display');
			if(selected_li_display =='none')
			{
				$(this).find('ul').slideDown(300);
				$('.user_menu li').not(this).find('ul').slideUp(300);
			}
		});

	//on clicking on mob menu button
		$('.mob_menu_button').click(function()
		{
			var active = $(this).attr('active');
			if(active == "no")
			{
				//$(this).hide("slide", { direction: "left" }, 1000);
				//$(this).show("slide", { direction: "left" }, 1000);
				$('.user_module_menu_bar').css('left', '0');
				$(this).attr('active', 'yes');
			}
			else if(active == "yes")
			{
				$('.user_module_menu_bar').css('left', '-70%');
				$(this).attr('active', 'no');
			}
		});

		$('.menu_close').click(function()
		{
			$('.user_module_menu_bar').css('left', '-70%');
			$('.mob_menu_button').attr('active', 'no');
		})

	//to generate heading of the content
		$('.user_menu li').click(function()
		{
			var heading = $.trim($(this).attr('title'));
			
			$('.user_module_heading').text(heading);
		});

	//to generate the content of the selected option
		$('.user_menu li li').click(function()
		{
			var user_username = "<?php echo $_COOKIE['logged_username']; ?>";
			var work = $.trim($(this).attr('work'));
			var file = work + ".php";
			var file_address = "php/" + file;

			$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load(file_address);
		});

		$('.user_menu li').click(function()
		{
			var user_username = "<?php echo $_COOKIE['logged_username']; ?>";
			var work = $.trim($(this).attr('work'));

			if(work != 'undefined' && work != '')
			{
				var file = work + ".php";
				var file_address = "php/" + file;

				$('.user_module_content').load(file_address);
			}
		});

	//on clicking on dashboard button
		$('#dashboard_button').click(function()
		{
			$('.user_module_heading').text('Admin Dashboard');
			$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/dashboard.php');
		});

	//by default dashboard is opened
		$('.user_module_heading').text('Admin Dashboard');
		$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/dashboard.php');
		$('#dashboard_button').css('background', '#3e454c').css('border-left', '5px solid #cc0000');
		
	</script>

</body>
</html>