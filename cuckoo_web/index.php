<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Cuckoo Web Server</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Alternate Web server for Cuckoo">
    <meta name="author" content="root" >

    <!-- Le styles -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 40px;
      }

      /* Custom container */
      .container-narrow {
        margin: 0 auto;
        max-width: 1000px;
      }
      .container-narrow > hr {
        margin: 30px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 60px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 72px;
        line-height: 1;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

    </style>
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="bootstrap/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="bootstrap/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="bootstrap/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="bootstrap/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="bootstrap/ico/favicon.png">
  </head>

  <body>

    <div class="container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="#Submit" data-toggle="modal">Submit an analysis</a></li>
        </ul>
         <a href="http://www.cuckoosandbox.org/" ><img src="graphic/cuckoo.png" alt="Cuckoo Sandbox" height="60" /></a>
      </div>

    <!-- Modal -->
    <div id="Submit" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3 id="myModalLabel">Fill out the information</h3>
	    </div>

	    <div class="modal-body">
		<form method="POST" id="submitCuckoo" action="submit.php" onsubmit="return checkFields(this);"class="form-horizontal">
		    <fieldset>

		    	<div class="control-group">
		        <label class="control-label" for="input01">File path</label>
		        <div class="controls">
		            <input type="text" name="file" class="input-xlarge" id="input01" placeholder="ex: /path/myfile.exe">
		        </div>
				</div>

		      <div class="control-group">
		      	<label class="control-label" for="input02">Package to use</label>
		      	<div class="controls">
		      		<input type="text" name="package" class="input-xlarge" id="input02" placeholder="exe, html, pdf ...">
		         </div>
				</div>

		      <div class="control-group">
		         <label class="control-label" for="input03">Options</label>
		         <div class="controls">
		            <input type="text" name="options" class="input-xlarge" id="input03">
		         </div>
		      </div>

		      <div class="control-group">
		         <label class="control-label" for="input04">Timeout</label>
		         <div class="controls">
		            <input type="text" name="timeout" class="input-xlarge" id="input04" placeholder="Seconds" value="0">
		         </div>
		      </div>

		      <div class="control-group">
		         <label class="control-label" for="select01">Priority</label>
		         <div class="controls">
		            <select id="select01" name="priority">
		         	<option value="1">Low</option>
		               <option value="2">Medium</option>
		               <option value="3">High</option>
		            </select>
		         </div>
		      </div>

				<hr>

		      <button type="submit" class="btn btn-primary pull-right">Submit</button>

		    </fieldset>
		</form>
	    </div>

    </div> <!--FIN Modal-->

    <script>
	function checkFields(form) {
		if (form.input01.value == '') {
			form.input01.focus();
			return false;
		}

		if (form.input02.value == '') {
			form.input02.focus();
			return false;
		}

		return true;
	};
    </script>

      <div class="jumbotron">
        <h2>Analysis tasks</h1>
        <hr>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th style="width: 40%;">Target</th>
                    <th>Added</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
				
				// E.G. USER : cuckoo PASS : cuckoo
				$db = mysql_connect('localhost', 'mysql_user', 'mysql_pass');

				// E.G. DB name : cuckoo
				mysql_select_db('database_name',$db);

				// Display Query
				$sql = 'SELECT id, category, target, added_on, completed_on, status FROM tasks ORDER BY id DESC;';
				$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
				
					while($row = mysql_fetch_assoc($req)) {
					?>
                <tr>
                    <td>
                    <?php
							echo $row['id']; 
                    ?>
                    </td>
                    <td>
                    <?php
                    	echo $row['category'];
                    ?>
                    </td>
                    <td>
		    <?php
			if( isset($row['completed_on']) ) {
				print("<a href=\"analyses/{$row['id']}/reports/report.html\">");
            		}

            		print("<span class=\"mono\">");
            		echo $row['target'];
            		print("</span>");

			if( isset($row['completed_on']) ) {
				print("</a>");
			}
         	    ?>
                    </td>
                    <td>
                    <?php
                    	echo $row['added_on'];
                    ?>
                    </td>
                    <td>
                    <?php
                    	echo $row['status'];
                    ?>
                    </td>
                </tr>
            <?php			}
            	mysql_close();
            ?>
            </tbody>
        </table>
      </div>

      <hr>

      <div class="footer">
        <p>&copy; Mahamoud SAID OMAR 2013</p>
      </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap-alert.js"></script>
    <script src="bootstrap/js/bootstrap-modal.js"></script>
    <script src="bootstrap/js/bootstrap-button.js"></script>
    <script src="bootstrap/js/bootstrap-transition.js"></script>

  </body>
</html>
