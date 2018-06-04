<?php
	session_start();
	include 'connect_db.php';
?>

<!-----for exce,csv generation------>
	<script type="text/javascript" src="js/FileSaver.min.js"></script>
	<script type="text/javascript" src="js/tableexport.min.js"></script>

	<script type="text/javascript">
		$('#table_export1').tableExport();
		$('#table_export2').tableExport();
	</script>

<h3>Stock Report</h3>

<!--------option to choose branch-------->
	<b class="admin_select_branch_text">Select Branch Code</b>

	<select id="admin_select_branch">
		<option value=""></option>
		<option value="*">All Branch</option>
		<?php			
			$get_brand_query = "SELECT * FROM branch";
			$get_brand_query_run = mysqli_query($connect_link, $get_brand_query);

			while($get_brand_result = mysqli_fetch_assoc($get_brand_query_run))
			{
				$branch_code = $get_brand_result['branch_code'];

				echo "<option value=\"$branch_code\">";
					echo $get_brand_result['branch_code'];
				echo "</option>";
			}
		?>	
	</select>
	<br><br>

<!---------user list container------>
	<div id="table_export" class="inventory_list_container">
		<h3>
			<?php
				if(isset($_SESSION['selected_branch']))
				{
					$selected_branch = $_SESSION['selected_branch'];
					echo "Showing Results for Branch Code: $selected_branch";
				}
			?>
		</h3>
		<br>
		
		<table id="table_export2">
			<tr>
				<?php
					if(isset($selected_branch))
					{
						if($selected_branch == '*')
						{
							echo "<th>Branch Code</th>";
						}
					}
					else
					{
						echo "<th>Branch Code</th>";
					}
				?>

				<th>Brand</th>
				<th>Model Name</th>
				<th>Model Number</th>
				<th>HSN Code</th>

				<th>Sold Items</th>
				<th>In-Stock Items</th>
				<th>Sales Price</th>
				<th>Supplier Price</th>
			</tr>

			<?php
				if(isset($_SESSION['selected_branch']))
				{
					$selected_branch = $_SESSION['selected_branch'];	

					if($selected_branch == '*')
					{
						$list_user_query = "SELECT * FROM stock ORDER BY id DESC";
					}
					else
					{
						$list_user_query = "SELECT * FROM stock WHERE creator_branch_code = '$selected_branch' ORDER BY id DESC";
					}

					//$list_user_query = "SELECT * FROM stock WHERE creator_branch_code = '$selected_branch' ORDER BY id DESC";
					$list_user_query_run = mysqli_query($connect_link, $list_user_query);

					while($list_user_assoc = mysqli_fetch_assoc($list_user_query_run))
					{
						$user_id = $list_user_assoc['id'];
						echo "<tr>";
							if($selected_branch == '*')
							{
								echo "<td>" .$list_user_assoc['creator_branch_code'] . "</td>";
							}
							
							echo "<td>" . $list_user_assoc['brand'] . "</td>";
							echo "<td>" . $list_user_assoc['model_name'] . "</td>";
							echo "<td>" . $list_user_assoc['model_number'] . "</td>";
							echo "<td>" . $list_user_assoc['hsn_code'] . "</td>";
							
							echo "<td>" . $list_user_assoc['sold'] . "</td>";
							echo "<td>" . $list_user_assoc['in_stock'] . "</td>";
							echo "<td>" . $list_user_assoc['sales_price'] . "</td>";
							echo "<td>" . $list_user_assoc['supplier_price'] . "</td>";

						echo "</tr>";
					}
				}
				else
				{
					$selected_branch = '*';	

					if($selected_branch == '*')
					{
						$list_user_query = "SELECT * FROM stock ORDER BY id DESC";
					}
					else
					{
						$list_user_query = "SELECT * FROM stock WHERE creator_branch_code = '$selected_branch' ORDER BY id DESC";
					}

					//$list_user_query = "SELECT * FROM stock WHERE creator_branch_code = '$selected_branch' ORDER BY id DESC";
					$list_user_query_run = mysqli_query($connect_link, $list_user_query);

					while($list_user_assoc = mysqli_fetch_assoc($list_user_query_run))
					{
						$user_id = $list_user_assoc['id'];
						echo "<tr>";
							if($selected_branch == '*')
							{
								echo "<td>" .$list_user_assoc['creator_branch_code'] . "</td>";
							}
							
							echo "<td>" . $list_user_assoc['brand'] . "</td>";
							echo "<td>" . $list_user_assoc['model_name'] . "</td>";
							echo "<td>" . $list_user_assoc['model_number'] . "</td>";
							echo "<td>" . $list_user_assoc['part_name'] . "</td>";
							echo "<td>" . $list_user_assoc['part_number'] . "</td>";
							echo "<td>" . $list_user_assoc['sold'] . "</td>";
							echo "<td>" . $list_user_assoc['in_stock'] . "</td>";
							echo "<td>" . $list_user_assoc['sales_price'] . "</td>";
							echo "<td>" . $list_user_assoc['supplier_price'] . "</td>";
							echo "<td>" . $list_user_assoc['hsn_code'] . "</td>";

						echo "</tr>";
					}
				}
			?>
		</table>

	</div>

<!---script------>
	<script type="text/javascript">
	//on selecting a branch
		$('#admin_select_branch').change(function()
		{
			var selected_branch = $(this).val();
			var session_of = selected_branch;
			var session_name = "selected_branch";

			$.post('php/session_creator.php', {session_of: session_of, session_name: session_name}, function(e)
			{
				if(e==1)
				{
					$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/admin_stock_report.php');
				}
				else
				{
					//alert(e);
					$('.warn_box').text("Something went wrong.");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});
		});
	</script>