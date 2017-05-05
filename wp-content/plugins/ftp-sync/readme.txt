=== Plugin Name ===
Contributors: a2rocklobster, buildcreate
Tags: ftp, sync, localhost, backup, files, transfer
Requires at least: 3.0.1
Tested up to: 3.8.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to sync local and remote media uploads, theme files, and plugins folders with one click via FTP.

== Description ==

This plugin allows you to sync local and remote media uploads, theme files, and plugins folders with one click via FTP. Do all your development on your localhost, initiate a sync whenever you need to from the FTP Sync options page.

FTP Sync can only be used from your localhost. If you try to use it from your development server you will get a message reminding you that you are not on your localhost. Also, an admin bar link will be on your localhost copy but not on your development server admin bar.

*IMPORTANT: Setup your local site's wp-config.php to use your remote site's database. Add your IP address (<a target="_blank" href="http://www.whatismyip.com/">What is my IP?</a>) to the "Remote Database Access Hosts" in cPanel so you every time you sync your files even the database will match up with everything (since it's the same database!).


== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.


== Frequently Asked Questions ==

= Is this safe to use? =

Yes. But please use the backup feature before you do any file syncing (just in case). 


== Screenshots ==

1. Options Page
2. Sync Files
3. Backup Files


== Changelog ==

= 1.1.6 =
* Added ignore files/directories/extensions option
* Updated localhost detection

= 1.1.5 =
* Upload/Download newer than setting bug fix
* Added cancel button during sync process

= 1.1.4 =
* Session reset bug fix

= 1.1.3 =
* Password field bug fix

= 1.1.2 =
* Inital wordpress release


== Upgrade Notice ==

= 1.1.6 =
* Added ignore files/directories/extensions option
* Updated localhost detection

= 1.1.5 =
* Upload/Download newer than setting bug fix
* Added cancel button during sync process

= 1.1.4 =
* Session reset bug fix.

= 1.1.3 =
* Password field bug fix

= 1.1.2 =
This is the initial wordpress release



