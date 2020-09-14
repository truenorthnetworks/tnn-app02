<?php include('header.php'); ?>

<main role="main" class="container-fluid">

	<div class="p-4">

		<?php
			// Connecting, selecting database
			$dbconn = pg_connect($tnnapp05)
				or die('Could not connect: ' . pg_last_error());

			// Performing SQL query
			$cols = "d.customerid,d.deviceid,d.longname,d.description,d.isprobe,nt.profileid,nt.nttriggerid,nt.ntdescription,ntt.scantargetid,ntt.triggerid";
			$query = "select $cols from device d
					left join (select scantargetid,triggerid,isprobe from notificationtriggertarget) ntt on ntt.scantargetid=d.deviceid
					left join (select profileid,triggerid as nttriggerid,description as ntdescription from notificationtrigger) nt on nt.nttriggerid = ntt.triggerid
				where d.isprobe='true'
				order by d.customerid,d.longname";
			$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		
		?>
		<table id='myTable' class='table table-striped table-bordered'>

			<thead>
				<tr>
					<?php
						foreach (explode(",",$cols) as $col) {
							echo "<th>$col</th>\n";
						}
					?>
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
