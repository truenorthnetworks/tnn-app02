<?php include('header.php'); ?>

<main role="main" class="container-fluid">

	<div class="p-4">

		<?php
			// Connecting, selecting database
			$dbconn = pg_connect($tnnapp05)
				or die('Could not connect: ' . pg_last_error());

			// Performing SQL query
			$query = "select c.customername,d.longname,d.mspbackupenabled from device d
				left join (select customerid,customername from customer) c on c.customerid = d.customerid
				where mspbackupenabled != 'false'
				order by customername,longname";

			$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		
			echo '<h2>Count: '.pg_num_rows($result).'</h2>';

		?>
		<table id='myTable' class='table table-striped table-bordered'>

			<thead>
				<tr>
					<th>Customer/site</th>
					<th>Device</th>
					<th>MSP Backup enabled</th>
				</tr>
			</thead>

			<tbody>
				<?php
					while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
						echo "\t<tr>\n";
						foreach ($line as $col_value) {
							echo "\t\t<td>$col_value</td>\n";
						}
						echo "\t</tr>\n";
					}

					pg_free_result($result);
					pg_close($dbconn);
				?>
			</tbody>

    	</table>
	</div>

</main><!-- /.container -->

<script>
	$(document).ready(function(){
		$('#myTable').DataTable({
			paging: false,
			fixedHeader: true
		});
	});
</script>

<?php include('footer.php'); ?>
