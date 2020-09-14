<?php include('header.php'); ?>

<main role="main" class="container-fluid">

	<div class="p-4">
		<table id='myTable' class='table table-striped table-bordered'>

			<thead>
				<tr>
					<th>rule name</th>
					<th>rule desc</th>
					<th>rule filter(s)</th>
					<th>service template name</th>
					<th>service template service</th>
					<th>service instance</th>
					<th>action</th>
				</tr>
			</thead>

			<tbody>
				<?php
					// Connecting, selecting database
					$dbconn = pg_connect($tnnapp05)
						or die('Could not connect: ' . pg_last_error());

					// Performing SQL query
					$query = "-- map folders (rules) and service templates together
					select foldername,f.description,filters.filternames,servicetemplatename,s.displayname,stsp.pvalue,sta.servicetemplateactionname from servicetemplatefoldermap m

					-- join service template details to the map
					left join (select servicetemplateid,servicetemplatename,servicetemplatedescription from servicetemplate) st on st.servicetemplateid = m.servicetemplateid

					-- join rule info to the map
					left join (select folderid,foldername,description from folder) f on f.folderid = m.folderid

					-- join a rule's filter(s)
					left outer join (
						select rfm.folderid,filternames from rulefiltermap rfm
						left join (select folderid,foldername,description as folderdesc from folder) folder on folder.folderid = rfm.folderid
						left join (
							select rfm.folderid,string_agg(filtername,',') as filternames from rulefiltermap rfm
							left join (select filterid,name as filtername from filter) filter on filter.filterid = rfm.filterid
							group by rfm.folderid
						) filter on filter.folderid = folder.folderid
					) as filters on filters.folderid = m.folderid

					-- join service templates` service IDs to the map
					left join (select servicetemplateserviceid,servicetemplateid,serviceid,servicetemplateactionid from servicetemplateservice) sts on sts.servicetemplateid = m.servicetemplateid
					--left join (select * from servicetemplateservice) sts on sts.servicetemplateid = m.servicetemplateid

					-- join sts add/modify/remove actions to sts
					left join (select * from servicetemplateaction) sta on sta.servicetemplateactionid = sts.servicetemplateactionid

					-- join service monitor details to the service template service IDs
					left join (select * from service) s on sts.serviceid = s.serviceid

					-- finally, for the services that use instances, join their instance detail to the service template service
					left outer join (
							--select serviceid,pkey,pvalue from servicetemplateserviceparameter
							select * from servicetemplateserviceparameter
							--where pkey='Matrix.0.Constraint.0.Value' or pkey like 'Log.%.LogName'
							where pkey='Matrix.0.Constraint.0.Value'
							-- 161: disk monitor (e.g. C:), 164: process monitor (e.g. svchost.exe), 22105: windows service (e.g. wuauserv)
							and (serviceid=161 or serviceid=164 or serviceid=22105)
					) stsp on (stsp.serviceid = s.serviceid and stsp.servicetemplateserviceid = sts.servicetemplateserviceid)

					order by foldername,servicetemplatename,displayname,pvalue";

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
