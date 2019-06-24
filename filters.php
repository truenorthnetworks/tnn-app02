<?php include('header.php'); ?>

<main role="main" class="container-fluid">

	<div class="p-4">
		<table id='myTable' class='table table-striped table-bordered'>

			<thead>
				<tr><th>rule name</th><th>rule desc</th><th>filter name</th><th>filter desc</th></tr>
			</thead>

			<tbody>
				<?php
					// Connecting, selecting database
					$dbconn = pg_connect($tnnapp01)
						or die('Could not connect: ' . pg_last_error());

					// Performing SQL query
					$query = "select foldername,folderdesc,filtername,filterdesc from rulefiltermap rfm
						left join (select folderid,foldername,description as folderdesc from folder) folder on folder.folderid = rfm.folderid
						left join (select filterid,name as filtername,description as filterdesc from filter) filter on filter.filterid = rfm.filterid
						order by foldername,filtername";

					$result = pg_query($query) or die('Query failed: ' . pg_last_error());

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
