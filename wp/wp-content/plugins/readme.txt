-> Database Manager For WordPress
--------------------------------------------------
Author	-> Lester 'GaMerZ' Chan
Email	-> lesterch@singnet.com.sg
Website	-> http://www.lesterchan.net/
Demo	-> N/A
Updated	-> 13th May 2005
--------------------------------------------------
Notes	-> Minium level required to add/edit/delete polls is 9
--------------------------------------------------

-> Installation Instructions
--------------------------------------------------
// Create a folder called 'wp-backup-db' under your root Wordpress folder


// CHMOD wp-backup-db to 777


// Open wp-admin folder

Put:
------------------------------------------------------------------
database-manager.php
------------------------------------------------------------------


// Open wp-admin/menu.php

Find:
------------------------------------------------------------------
$menu[25] = array(__('Presentation'), 8, 'themes.php');
------------------------------------------------------------------
Add Above It:
------------------------------------------------------------------
$menu[24] = array(__('Database'), 9, 'database-manager.php');
------------------------------------------------------------------

// Login to Wordpress Administration Panel and click the option 'Database' at the top