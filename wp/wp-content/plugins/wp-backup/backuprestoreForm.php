        <div class="wrap">
        <form name="backupdatabase" action="<? echo add_query_arg('action', 'backupdatabase', $_SERVER['REQUEST_URI']); ?>" method="post" id="post">
        <div id="poststuff">
		    <fieldset id="postdiv">
		      <legend><strong>Backup Database</strong></legend>
				<input name="backupdatabase" type="submit" id="backupdatabase" tabindex="9" value="Backup Database" />
				</fieldset>
				</form>
		    <br/>
		<form name="import" action="<? echo add_query_arg('action', 'backupfiles', $_SERVER['REQUEST_URI']); ?>" method="post" id="post">
		    <fieldset id="postdiv">
		      <legend><strong>Backup WordPress Files</strong></legend>
		      Click the button below to backup your files to your local computer<br/>
			  <input name="backupfiles" type="submit" id="backupfiles" tabindex="10" value="Backup WordPress Files" />
		    </fieldset>
		</form>
		<br />
	    <form name="restoredatabase" action="<? echo add_query_arg('action', 'restore', $_SERVER['REQUEST_URI']); ?>" method="post" id="post">
		<fieldset id="postdiv">
		      <legend><strong>Restore your database</strong></legend>
		      Type in the name of your restore file (ending in .sql) from the list below and click "Restore Database"<br/>
		      WARNING: This is not reversible. Once you click restore, it is permanent!
		       Please, Please, Please double check!!!<br/><br/>
		       <fieldset id="postdiv">
		       <legend><em>Database files in the backup directory</em></legend>
		       <?php
		           $results = array();
			       $handler = opendir($path);
			       while ($file = readdir($handler)) {
			           if (($file != '.') && ($file != '..')) {
			               $results[] = $file;
                       }
			       }
			       closedir($handler);
                   rsort($results);
			       $counter = 0;
			       while ($results[$counter] != '')	{
			       		echo "<br/>".($counter+1).") ".$results[$counter];
			       		$counter++;
    				}
    			?>
    			</fieldset><br/>
			  <div><input type="text" name="restoredbfile" size="60" tabindex="1" value="<?php $restoredbfile ?>" id="restoredbfile" /></div>
			  <input name="restoredatabase" type="submit" id="restoredatabase" tabindex="11" value="Restore Database" />
		</fieldset>
		</form>
        </div>
