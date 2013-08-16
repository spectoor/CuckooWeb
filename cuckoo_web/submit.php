<?php

	// Database parameters

	// E.G. USER : cuckoo PASS : cuckoo
	$db = mysql_connect('localhost', 'mysql_user', 'mysql_pass');

	// E.G. DB name : cuckoo
	mysql_select_db('database_name',$db);

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
	
	$sql = "SELECT id FROM samples WHERE sha512='$sha512';";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	$row = mysql_fetch_assoc($req));
	
	if( $row ) {
		$sample_id = $row['id'];
	}
	else {
		$req = mysql_query("SELECT * FROM samples;");
		$sample_id = mysql_num_rows($req);
		$sample_id+=1;
	}

	$query_list = "id, file_size, file_type, md5, crc32, sha1, sha256, sha512, ssdeep";
	$args_list = "'$sample_id', '$file_size', '$file_type', '$md5', '$crc32', '$sha1', '$sha256', '$sha512', '$ssdeep'";

	$sql = "INSERT INTO samples($query_list) VALUES ($args_list);";
	//echo "$query<br>";
	if($req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());) {
		echo 'OK adding samples<br>';
	}
	else {
		echo 'FAIL adding samples<br>';
	}
	
	// Fill out the tasks table

	// Get new task id
	$req = mysql_query("SELECT * FROM tasks;");
	$task_id = mysql_num_rows($req);
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

	$sql = "INSERT INTO tasks($query_list) VALUES ($args_list);";
	
	if($req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());) {
		echo 'OK adding tasks<br>';
	}
	else {
		echo 'FAIL adding tasks<br>';
	}

	// Database disconnection

	mysql_close();

	// Redirection

	header('Location: index.php');
?>
