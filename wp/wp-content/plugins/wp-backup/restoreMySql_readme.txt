Method to backup and restore WordPress database from an sql dump (either from the
built in backup method or from another MySql backup tool)
Created by LaughingLizard (http://dinki.mine.nu/weblog/ http://dinki.mine.nu/word)
Created on Feb 21, 2004


1) The WordPress built in backup creates a MySql dump which is very similar to one created by
	phpMyAdmin and/or the mysqldump client. You can use any commercial tool to restore the
	database.
	Here is the process to restore the whole WP install:
	
	- Restore your WordPress files into the same folder as before (using FTP, from your backup)
	- Create a database with the same name as before with the same MySql username and password

2) Once you have uploaded the MySql dump to your server there are two directions to take:
	
	i) If you are using a Linux/UNIX server you could use the MySql client from a command line
		- To do this, Login to a shell and change directory to where you have 
		  stored the mysql dump file
		- Type this into the shell:

mysql -h #hostname# -u #mysqlusername# -p #mysqlpassword# #WPdatabasename# < #sqldump_filename#
(Make sure you replace everything within the #'s with your own values, remove the #'s as well)

		- When you hit enter, a good restore would mean NO output at the shell, check your WP to
			see if the values are back
	
	ii) If you are using a Windows based system, you will have to use a MySql tool such as 
		phpMyAdmin or a client tool in Windows.
		(Make sure you follow Step 1 before restore your files as well and create the database)
		- If you have phpMyAdmin installed (either by yourself or through your webhost)
			- Scroll down on the left dropdown till you find your database, click on it
			- On the right frame, click on the SQL tab
			- All the way at the bottom of the frame, find "location of the text file"
			- Click on browse, find the sqldump on your local computer
			- Make sure Autodetect is checked and click Go
			- You will see the results of your import to the database
			

If you have any questions, concerns or suggestions, stop by the forums at http://www.wordpress.org/support/