<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);

	include('credentials.php');
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- <meta name="description" content=""> -->
		<!-- <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors"> -->
		<!-- <meta name="generator" content="Jekyll v3.8.5"> -->
		<title>TNN-APP02</title>

		<!-- jquery -->
		<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

		<!-- bootstrap -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

		<!-- datatables -->
		<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
		<script src="https://cdn.datatables.net/fixedheader/3.1.4/js/dataTables.fixedHeader.min.js"></script>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
		<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.4/css/fixedHeader.bootstrap4.min.css"/>

		<style>
			body {
				/* padding-top: 5rem; */
				/* font-size: 0.9rem; */
			}
			.starter-template {
				padding: 3rem 1.5rem;
				text-align: center;
			}
			.bd-placeholder-img {
				font-size: 1.125rem;
				text-anchor: middle;
				-webkit-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
			}
			@media (min-width: 768px) {
				.bd-placeholder-img-lg {
					font-size: 3.5rem;
				}
			}
		</style>
		<!-- Custom styles for this template -->
		<!--<link href="starter-template.css" rel="stylesheet">-->
	</head>
	<body>
	<!--<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">-->
	<nav class="navbar navbar-expand-md navbar-dark bg-dark">
		<a class="navbar-brand" href="/index.php">TNN-APP02</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarsExampleDefault">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="templates.php">Templates</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="tasks.php">Scheduled tasks</a>
			</li>
			<!-- <li class="nav-item">
				<a class="nav-link" href="filters.php">Filters</a>
			</li> -->
			<!--
			<li class="nav-item">
				<a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
				<div class="dropdown-menu" aria-labelledby="dropdown01">
					<a class="dropdown-item" href="#">Action</a>
					<a class="dropdown-item" href="#">Another action</a>
					<a class="dropdown-item" href="#">Something else here</a>
				</div>
			</li>
			-->
		</ul>
		<!--
		<form class="form-inline my-2 my-lg-0">
			<input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
			<button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
		</form>
		-->
		<span class="text-light">
			<?php 
				$dbconn = pg_connect($tnnapp01)
					or die('Could not connect: ' . pg_last_error());

				# get the most recent audit log table
				$query = "select table_name from information_schema.tables
					where table_name like 'auditlog_%'
					order by table_name desc
					limit 1";
				$result = pg_query($query) or die('Query failed: ' . pg_last_error());
				$line = pg_fetch_array($result, null, PGSQL_ASSOC);
				$auditTableName = $line['table_name'];
				pg_free_result($result);

				# get the last backup time from that audit log
				# this is split into two queries because the text filter is slow otherwise
				$query = "select * from (
						select * from $auditTableName
							where userid = 1
							--and details = 'Starting System Backup'
							order by audittime desc
							limit 10
						) as asdf
					where details = 'Starting System Backup'
					limit 1";
				$result = pg_query($query) or die('Query failed: ' . pg_last_error());
				$line = pg_fetch_array($result, null, PGSQL_ASSOC);
				# print the timestamp from this backup (left of the decimal in e.g. "2019-05-31 23:30:02.076524-04")
				echo '<span class="text-muted">Data from ' . explode('.', $line['audittime'])[0] . '</span>';
				pg_free_result($result);
	
				pg_close($dbconn);
			?>
		</span>
		</div>
	</nav>
