<?php include('header.php'); ?>

<main role="main" class="container-fluid">

	<div class="p-4">

		<?php
			// Connecting, selecting database
			$dbconn = pg_connect($tnnapp01)
				or die('Could not connect: ' . pg_last_error());

			// Performing SQL query
			$query = "SELECT customerid,customername,parentid,lastupdated,psacustomername,deleted FROM customer
				where customername like '%- managed%'
					-- exclude certain customers (50 - system level, 395 - 2patching)
					or parentid not in (50, 395)
					and psacustomername = '-1' and deleted = false";

			$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		
		?>
		<table id='myTable' class='table table-striped table-bordered'>

			<thead>
				<tr>
					<th>customerid</th>
					<th>customername</th>
					<th>parentid</th>
					<th>lastupdated</th>
					<th>psacustomername</th>
					<th>deleted</th>
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
