<!-----for exce,csv generation------>
	<script type="text/javascript" src="js/FileSaver.min.js"></script>
	<script type="text/javascript" src="js/tableexport.min.js"></script>

	<script type="text/javascript">
		//$('#table_export1').tableExport();
		$('#table_export2').tableExport();
	</script>

	<div id="table_export" class="inventory_list_container">

		<table id="table_export2" class="part_only_table">
			<tr>
				<th>Brand</th>
				<th>Type</th>
				<th>Product/Part</th>
				<th>Product/Part Code</th>
				<th>HSN Code</th>
				
				<th>Sold Items</th>
				<th>In-Stock Items</th>
				<th>Sales Price</th>
				<th>Supplier Price</th>
				<th>Created By</th>
				<th>Actions</th>
			</tr>

			<?php
				include 'connect_db.php';
				$user_username = $_COOKIE['logged_username'];
				$creator_branch_code = $_COOKIE['logged_username_branch_code'];

				$list_user_query = "SELECT * FROM stock WHERE creator_branch_code = '$creator_branch_code' ORDER BY id DESC";
				$list_user_query_run = mysqli_query($connect_link, $list_user_query);

				while($list_user_assoc = mysqli_fetch_assoc($list_user_query_run))
				{
					$user_id = $list_user_assoc['id'];
					echo "<tr>";
						
						echo "<td>" . $list_user_assoc['brand'] . "</td>";
						echo "<td>" . $list_user_assoc['type'] . "</td>";
						echo "<td>" . $list_user_assoc['model_name'] . "</td>";
						echo "<td>" . $list_user_assoc['model_number'] . "</td>";
						echo "<td>" . $list_user_assoc['hsn_code'] . "</td>";
						
						echo "<td>" . $list_user_assoc['sold'] . "</td>";
						echo "<td>" . $list_user_assoc['in_stock'] . "</td>";
						echo "<td>" . $list_user_assoc['sales_price'] . "</td>";
						echo "<td>" . $list_user_assoc['supplier_price'] . "</td>";
						echo "<td>" . $list_user_assoc['creator_username'] . "</td>";
						echo "<td>";
							echo "<img user_id=\"$user_id\" class=\"inventory_edit_icon\" src=\"img/edit.png\"/>";
							echo "<img user_id=\"$user_id\" class=\"inventory_delete_icon\" src=\"img/delete.png\"/>";			
						echo "</td>";
					echo "</tr>";
				}
			?>
		</table>

	</div>

<!-----------script----------->
	<script type="text/javascript">
	//on clicking on inventory delete icon
		$('.inventory_delete_icon').click(function()
		{
			var user_id = $(this).attr('user_id');
			var query_recieved = "DELETE FROM stock WHERE id = '" + user_id + "'";
			
			$.post('php/query_runner.php', {query_recieved:query_recieved}, function(e)
			{
				if(e==1)
				{
					$('.user_module_content').html("<img class=\"gif_loader\" src=\"img/loaders1.gif\">").load('php/manage_stock.php');
				}
				else
				{
					$('.warn_box').text("Something went wrong while deleting the user");
					$('.warn_box').fadeIn(200).delay(3000).fadeOut(200);
				}
			});
		});

	//on clicking on user edit icon
		$('.inventory_edit_icon').click(function()
		{
			var user_id = $(this).attr('user_id');

			$('.ajax_loader_bckgrnd').fadeIn(400);
			
			$.post('php/edit_stock_form.php', {user_id:user_id}, function(data)
			{
				$('.ajax_loader_box').fadeIn(400);
				$('.ajax_loader_content').html(data);
			});					
		});
	</script>