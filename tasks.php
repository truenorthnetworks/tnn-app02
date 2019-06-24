<?php include('CronSchedule.php'); ?>

<?php include('header.php'); ?>

<main role="main" class="container-fluid">

	<div class="p-4">
		<table id='myTable' class='table table-striped table-bordered'>

			<thead>
				<tr>
					<th>customer name/site</th>
					<th>task name</th>
					<th>task type</th>
					<th>sec</th><th>min</th><th>hr</th><th>day&nbsp;of mo</th><th>mo</th><th>day&nbsp;of wk</th><th>yr</th>
					<th>description</th>
			</thead>

			<tbody>
				<?php
					// Connecting, selecting database
					$dbconn = pg_connect($tnnapp01)
						or die('Could not connect: ' . pg_last_error());

					// Performing SQL query
					$query = "--select distinct customername,ret.name,tasktype,value
						--select * from remoteexecutiontask ret
						select distinct c.customername,ret.name,tasktype,value,description from remoteexecutiontask ret
						left join (select * from remoteexecutiontaskschedule) rets on rets.remoteexecutiontaskid = ret.remoteexecutiontaskid
						left join (select remoteexecutionitemid,name,description from remoteexecutionitem) rei on rei.remoteexecutionitemid = ret.remoteexecutionitemid
						left join (select customerid,customername from customer) c on c.customerid = ret.customerid
						where ret.deleted = 'false'
							and rets.type = 'Recurring'
							and status != 'Deleted'
							and ret.enabled = true
							--and ret.customerid = 100
							and tasktype not like 'AVDefender%'
						order by c.customername";

					$result = pg_query($query) or die('Query failed: ' . pg_last_error());

					while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
						echo "\t<tr>\n";
						foreach ($line as $col_value) {
							// if we're printing the cron string
							if ($col_value === $line['value']) {
								//$schedule = CronSchedule::fromCronString($col_value);
								//echo "<td>$schedule->asNaturalLanguage()</td>";
								foreach (explode(" ", $col_value) as $i) {
									echo "<td>$i</td>";
								}
							} else {
								echo "\t\t<td>$col_value</td>\n";
							}
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
