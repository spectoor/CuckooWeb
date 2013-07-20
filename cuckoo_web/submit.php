<?php

	// Database parameters

	$dbname='cuckoo.db';
	$base=new SQLite3($dbname);

	// Vars from the POST request

	$filename = $_POST['file'];
	$package = $_POST['package'];
	$priority = $_POST['priority'];
	$timeout = $_POST['timeout'];
	if($timeout == '')
		$timeout = 0;
	$options = $_POST['options'];

	// Fill out the sample table

	$sample_id = 0;
	$file_size = filesize("$filename");
	$finfo = finfo_open(FILEINFO_CONTINUE);
	$file_type = finfo_file($finfo, "$filename");
	$md5 = hash_file('md5', "$filename");
	$crc32 = hash_file('crc32', "$filename");
	$sha1 = hash_file('sha1', "$filename");
	$sha256 = hash_file('sha256', "$filename");
	$sha512 = hash_file('sha512', "$filename");
	$ssdeep = ssdeep_fuzzy_hash_filename("$filename");

	// Get a new sample id if not present in database

	$query = "SELECT id FROM samples WHERE sha512='$sha512'";
	$result = $base->query($query);
	$row = $result->fetchArray(SQLITE3_ASSOC);
	if( $row ) {
		$sample_id = $row['id'];
	}
	else {
		$sample_id = $base->querySingle("SELECT COUNT(*) as count FROM samples");
		$sample_id+=1;
	}

	$query_list = "id, file_size, file_type, md5, crc32, sha1, sha256, sha512, ssdeep";
	$args_list = "'$sample_id', '$file_size', '$file_type', '$md5', '$crc32', '$sha1', '$sha256', '$sha512', '$ssdeep'";

	$query = "INSERT INTO samples($query_list) VALUES ($args_list)";
	//echo "$query<br>";
	if($result = $base->exec($query)) {
		echo 'REUSSI samples<br>';
	}
	else {
		echo 'ECHEC samples<br>';
	}

	// Fill out the tasks table

	// Get new task id
	$task_id = $base->querySingle("SELECT COUNT(*) as count FROM tasks");
	$task_id+=1;
	$category='file';
	$custom='';
	$machine='';
	$platform='windows';
	$memory=0;
	$enforce_timeout=0;
	$added_on=date("Y-m-d h:i:s.u");

	$query_list="id, target, category, timeout, priority, custom, machine, package, options, platform, memory, enforce_timeout, added_on, sample_id";
	$args_list="'$task_id', '$filename', '$category', '$timeout', '$priority', '$custom', '$machine', '$package', '$options', '$platform', '$memory', '$enforce_timeout', '$added_on', '$sample_id'";

	$query = "INSERT INTO tasks($query_list) VALUES ($args_list)";
	//echo "$query<br>";
	if($result = $base->exec($query)) {
		echo 'REUSSI tasks<br>';
	}
	else {
		echo 'ECHEC tasks<br>';
	}

	// Database disconnection

	$base->close();

	// Redirection

	header('Location: index.php');
?>
