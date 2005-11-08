WordPress Backup scripts for files and MySql database Ver 1.1

Original Author: LaughingLizard (dinki@mac.com)
http://weblogtoolscollection.com
Feel free to distribute and modify with proper attribution!
This is designed for WordPress 1.5+ (use the old version for previous WP and b2)

Simple way to backup and restore a WordPress MySql database and wordpress folder
from inside the admin page:

To install the backup system:
1) unzip all files on your computer
2) Make a directory inside your wp-content and name it backup
3) Assign write permissions to the backup folder you just created
4) Copy all included files (except readme.txt and backuprestore.php) to your wp-admin folder
5) Copy backuprestore.php to your wp-content/plugins directory and activate

4) Limit access to the backup folder with an .htaccess file such
as:

----
Order deny,allow
Deny from all
Allow from localhost
----

or

----
AuthUserFile    /usr/local/httpd/web.acl
AuthType        Basic
AuthName        "WordPress Backup"
<Limit GET POST>
        Require valid-user
</Limit>
----

Or create a dummy index.html file in the backup folder (touch
index.html)

If you don't understand Apache access control, see
http://httpd.apache.org/docs/howto/auth.html or
http://httpd.apache.org/docs-2.0/howto/auth.html for more info or
talk to your friendly neighborhood webmaster. Google and RTFM for
other webservers...


PS: This back will NOT make recursive backups of your backup folder.
To make backups, click on the Backup/Restore link in your admin page. 
A listing of all the files in your backup folder is shown. 

All Done!

PS: The restore still uses the old method of restoring (through passthru) and will not work on systems
which do not have passthru for php enabled. Please check with your provider first. In case it does
not work, you would have to restore by switching to your backup directory and invoking
the mysql client in a shell using:
mysql -h yourservername -u yoursqllogin -p yoursqlpass -f wordpressdatabase < nameofbackupfile

OR you could use a tool like phpMyAdmin to restore.


DISCALIMER: As with any (data/database manipulation) software, I am not responsible for any 
damages or any loss of data, please use at your own risk.

Some caveats: 
This script has not been tested on a Windows server. I am not sure if the required
applications are installed on Windows. I would appreciate someone testing it in Windows and giving
me some feedback.
This method of backing up needs to have a couple of warning labels. Backups are meant for disaster
recovery and not for daily archival. Everytime you create a backup, more space is taken up and this
script does not take care of deleting any old files. So please keep track of your usage.
DO NOT USE THIS SCRIPT TO TEST THE RESTORE PROCEDURE. It works on most versions of WordPress and
has been tested on 0.72 and 1.0 Alpha-2. If you do decide to test the restore on a working blog, you do
so at your own risk. Do all testing on a non-production blog.
There are no gaurantees associated with this script and the author DOES NOT ASSUME any legal or financial responsability
for the script. Any modification made to this document after its distribution, is illegal.

Peace!