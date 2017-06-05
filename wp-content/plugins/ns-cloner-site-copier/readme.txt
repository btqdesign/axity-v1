=== NS Cloner - Site Copier ===
Contributors: neversettle
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=53JXD4ENC8MM2&rm=2
Tags: automate, duplicate, copy, copy site, copier, clone, clone site, cloner, multisite, network, subdomain, subdirectory, subfolder, template
Requires at least: 3.0.1
Tested up to: 4.7.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The NS Cloner saves multisite admins and developers TONS of time by cloning existing sites in a multisite network to a completely new site in a few seconds.

== Description ==

This plugin ONLY works with WordPress Multisite, will NOT work in single site mode, and MUST be Network Activated. You will find its menu in your network administration dashboard (wp-content/network)

This is by far the easiest, fastest, and most user-friendly way you will ever create fully configured sites on your multisite networks. As with everything we do, Never Settle is fanatical about simplifying user tasks to an absolute bare and joyful minimum without sacrificing the complex functionality going on behind the scenes. You will not find another site cloner that comes anywhere close to how easy this is to use.

The NS Cloner will take any existing site on your WordPress multisite network and clone it into a new site that is completely identical in theme & theme settings, plugins & plugin configurations, content, pictures, videos, and site settings. **Everything** is preserved and intelligent replacements are made so that the new site settings reflect your choices for the name and title of the new site as well as other automated background housekeeping to make sure the new site works exactly the same way as if you had taken the time to set it all up manually.

= V3 Features =
* Advanced validation and preemptive issue detection
* Robust system configuration and action logging to help with troubleshooting
* All new Custom Add-on Architecture
* All new hook-rich cloning pipeline for developers to extend
* All new dynamic, responsive admin interface

If you want even more functionality, [check out our Add-ons and Bundles](http://neversettle.it/ns-cloner-add-ons-features)!

= Standard Precautions and Notes =
* Unlike other similar tools, the NS Cloner supports cloning the main root site at ID=1! But please be especially careful with this feature. Multisite plugins like BuddyPress add tables at the main (wp_) level of the database. There are also several global tables that apply to the network and NOT to the core site. The Cloner automatically excludes these global tables out of the gate so that they don't get cloned to all your new sites which will have wp_ID_ as a prefix instead of wp_. But tables for other network level plugins that don't apply to the clone could still get copied by the Cloner due to its automation and inability to be aware of the table structure of every plugin out there. Our Table Manager add-on is perfect for unique scenarios where you need total table-level cloning control.
* We always try to help, but we cannot promise support to users for this Free version, especially related to cloning the root site do to the potential complexities involved from environment to environment.
* We have used the NS Cloner on production systems for years without issues. That doesn't mean your scenario won't find some new condition that could cause you some headaches. Unlikely, but always possible. We recommend getting familiar with it on a test system before you deploy it to a critical network.
* And for the love - backup your data. This plugin operates at the database level to work its magic. We've run it hundreds of times on our own sites and client sites, and tested it thoroughly. It's safe. But don't take our word for it.

= Typical Workflow for using the NS Cloner =
1. Set up 1 or more "template" sites exactly the way you want your clones to start out
1. Go to your Network Dashboard > NS Cloner
1. Select the "template" site you that want to clone, type the name of the new site, and put in its Title
1. Clone Away!

Yes, it really is that easy.

= Primary Use Cases =
* Developers who host and manage multiple client sites in their own multisite environment - this will allow you to rapidly roll out new baseline sites with all your favorite standard plugins and configurations in place - no more tedious manual repetitive entry.
* Organizations which provide "member" sites and want to be able to reduce the site spin up time to almost nothing.
* Affiliates that host numerous sites through Multisite and are looking for a way to increase reach and decrease deployment times. 
* Designers who want to be able to create several versions of sites to test and play with different theme designs in parallel without having to re-install all the same plugins and base themes over and over.

== Features ==

= Some of the NS Cloner highlight features: =
1. Copies an entire site in seconds
1. Works in subdomain or subdirectory mode
1. Copies ALL theme and plugin settings
1. Copies ALL content and custom post types
1. Copies ALL media files from original site
1. ** Advanced validation and preemptive issue detection
1. ** Robust system configuration and action logging
1. ** Custom Add-on Architecture
1. ** All new hook-rich cloning pipeline
1. ** All new dynamic, responsive interface

(** New functionality in V3)
If you want even more functionality, [check out our Add-ons and Bundles](http://neversettle.it/ns-cloner-add-ons-features)!

== Installation ==

1. Log in to your WordPress network as a multisite super admin and go to /wp-admin/network
1. Use the Dashboard > Plugins > Add New tools to install NS Cloner from the WordPress.org repository or install by uploading the zip file
1. Network Activate the NS Cloner through the 'Plugins' menu in WordPress
1. Access the NS Cloner from its main menu on the Network Dashboard (/wp-admin/network)

== Frequently Asked Questions ==

= How do I contact Support, provide Feedback, or make a Feature Request? =
You can browse our Knowledge Base, add or vote on Feature Requests, or contact us with an issue at <a title="Never Settle Support and Feedback" href="http://support.neversettle.it" target="_blank">http://support.neversettle.it</a>

= Does the NS Cloner work on subdomain networks as well as subfolder networks? = 
YES!

= Why do I get a white screen after cloning a site? = 
Usually this means that the clone operation did not complete successfully. The most common cause for this is a script timeout. By default, PHP script execution for a single script is set to 30 seconds. This might not be enough time for larger sites with numerous posts, pages, and users to complete cloning - especially since the Cloner runs advanced search and replace operations against every table cloned to the new site to make sure that it reflects the new site url and title throughout all its data. Try increasing the max_execution_time in php.ini or wherever your host supports updating PHP configuration settings. You can <a title="Cloning White Screen Issue" href="https://neversettle.it/documentation/ns-cloner/white-screen-404-blank-site-cloning/" target="_blank">read more detailed troubleshooting tips for this issue</a> on our support site.

= Are there other troubleshooting guides? =
[See these articles](https://neversettle.it/documentation/ns-cloner/) for additional help on cloning issues.

== Screenshots ==

1. All new dynamic, responsive, add-on aware UI
2. All new Add-on manager

== Changelog ==

= 3.0.7 =
* Replaced deprecated wp_get_sites() with get_sites()

= 3.0.6.0 =
* Fixed an issue where some serial arrays get treated as objects instead of arrays and search/replace was getting missed
* Updated Kint library to resolve issues on some rare environments running OPCache and XCache

= 3.0.5.9 =
* Updated PHP 7 compatibility to use proper global variable variable emulation

= 3.0.5.8 =
* Fixed bug with search and replace preventing multiple replacements in rare cases where the search is different but the replace value is the same. 

= 3.0.5.7 =
* Added new feature to save default site template for re-use in cloning every time

= 3.0.5.6 =
* Update to prevent kint classes from causing fatal error when autoloaded by another plugin or tool

= 3.0.5.5 =
* Update to work around WP version changes that prevent site names with dashes in the wpmu_validate_blog_signup() check

= 3.0.5.4 =
* Fixed issue with latest version of WP where a test validation site name containing only numbers is no longer valid

= 3.0.5.3 =
* Small formatting fix to sidebar
* Fixed php Notice: Undefined variable: query when WP_DEBUG is true under certain conditions
* Updated README

= 3.0.5.2 =
* Fixed mysql errors for unquoted numeric strings and empty values

= 3.0.5.1 = 
* Fixed INSERT logic bug that resulted in some rows not being copied
* Fixed extra characters in sites cloned via quick clone link
* Better javascript error reporting

= 3.0.5 =
* Refactored MySQL INSERT commands to increase performance and resolve duplicate value issues with several plugins
* Added better MySQL CONSTRAINT handling to fix compatibility issues with several plugins
* Added fix for sites using the Wishlist Member plugin

= 3.0.4.9 =
* Fixed title replacement bug

= 3.0.4.8 =
* Fixed one-click cloning issue
* Fixed a few bugs relating to upload paths and replacements

= 3.0.4.7 = 
* Added WP 4.1 compatibility status
* Added additional error handling for systems that can't read external feeds for the sidebar

= 3.0.4.6 = 
* Added compatibility fix for CSS & Javascript Toolbox plugin

= 3.0.4.5 =
* Adding compatibility fix for User Access Manager plugin
* Removed site title find/replace functionality

= 3.0.4.4 =
* Added support for new feature and setting in Content and Users Add-on to send notifications to new users

= 3.0.4.3 =
* Added another condition on ensuring kintParser is not already loaded from somewhere else

= 3.0.4.2 =
* Added condition to only load Kint (used for logging) if no other plugin already has

= 3.0.4.1 =
* Fixed minor issue and removed [[ *Notice*: Undefined variable: default_db_creds ]] showing up with WP_DEBUG turned on
* Corrected mismatched version between plugin and logs
* Added basic, disabled feature for emailing new users with site login and password (not currently active - primarily preparation for a new Registration Templates Add-on feature and option)  

= 3.0.4 =
* Added one-click cloning from Manage Sites page
* Added pre WP 3.7 compatibility
* New search selector for source sites
* Revamped logs for better utility/readability
* Centralized ns_cloner_addon class for maintability

= 3.0.3.1 =
* Fixed bug introduced in 3.0.3 affecting subdirectory mode

= 3.0.3 =
* Added exception for subsite names to include dashes
* Updates to support new Registration Templates add-on

= 3.0.2 =
* Fixed conflicts with some other multisite plugins that were triggering validation errors when attempting a clone
* Raised number of sites shown by default in "Select Source" dropdown from 100 to 1000 and made parameters for that query filterable 

= 3.0.1 =
* Fixed www vs. non-www issue on subdomain installs that were using www and generating sites like site.www.domain.com
* Added robust new detection system for identifying WP version and uploads location even under abnormal edge cases
* Added advanced validation and preemptive issue detection
* Added robust system configuration and action logging to help with troubleshooting
* Added All new Custom Add-on Architecture
* Added All new hook-rich cloning pipeline for developers to extend
* Added All new dynamic, responsive admin interface

= 2.1.4.9 =
* Fixed several small bugs reported to support that impacted specific scenarios, configurations, and plugin combinations.

= 2.1.4.8 =
* Fixed bug that was causing UTF-8 and DB character encoding issues in some environments. Primarily affected non-English sites.

= 2.1.4.7 =
* Fixed critical bug that prevented user roles from getting cloned when cloning the ROOT site only

= 2.1.4.6 =
* Fixed critical bug that was causing incompatibilities with some plugins that install their own tables to the database and in some cases the cloning operation was leaving a single custom table behind in the clone. This primarily affected Calendar plugins or other plugins where the table name began with higher letters (a, b, c) after the prefix like wp_a*, wp_b*, wp_c*

= 2.1.4.5 =
* Added support for cloning the root site with ID 1 (YAY! PLEASE SEE STANDARD PRECAUTIONS AND NOTES ON DESCRIPTION PAGE)
* Added support for WP Multisite when installed in a subdirectory rather than at the root of a domain (there were previously issues in this scenario)
* Added validation to enforce lowercase and only allowed characters in the site name field
* Added validation to enforce replacement rules that aren't always obvious (you don't want the old site name to be contained in the new site domain or the cloner's automated data replacement will corrupt your new clone's data)
* Fixed permalink bug in subdirectory mode
* Updated the way the status is returned after cloning to fix issues where the status exceeds URL length restrictions
* [EXPERIMENTAL] Added support for the ThreeWP Broadcast plugin based on user contribution (thank you John @ propanestudio.com and Aamir!)
* Many other small tweaks, updates, and fixes

= 2.1.4.4 =
* Enhanced media file copy handling from 2.1.4.3

= 2.1.4.3 =
* Added better media file copy handling in cases where themes or plugins alter wp_upload_dir() and it is returning bad paths

= 2.1.4.2 =
* Fixed bug reported by Christian (Thank you!) where some upload file paths containing the same numbers as site IDs were getting mangled

= 2.1.4.1 =
* Fixed 2.1.4 to make file copies compatible with the new uploads structure in native WP 3.5 installs
* ANNOUNCING NS Cloner Pro is now Available [HERE](https://neversettle.iljmp.com/1/ns-cloner-pro)

= 2.1.4 =
* Fixed bug in 2.1.3 that caused file copies to fail in some cases where the target folders already existed

= 2.1.3 =
* Fixed bug in 2.1.2 that forced subdirectory mode

= 2.1.2 =
* Added Auto-detect of Multisite mode and Subdirectory site support!
* Added Automatic Copy of all media files in blogs.dir/##
* Fixed some image loading fails in certain scenarios

= 2.1.1 =
* First public release

== Upgrade Notice ==

= 2.1.1 =
* First public release

= 2.1.3 =
* Fixed bug in 2.1.2 that forced subdirectory mode - if you updated to 2.1.2 please update to 2.1.3 immediately.

= 2.1.4 =
* Fixed bug in 2.1.3 that caused file copies to fail in some cases where the target folders already existed. Update to correct the issue if affected.

= 2.1.4.1 =
* Fixed 2.1.4 to make file copies compatible with the new uploads structure in native WP 3.5 installs. This should correct issues with the media file copes! Please update ASAP.

= 2.1.4.5 =
* Added validation to prevent unsafe values for certain fields like site name
* Updated deprecated function calls and fixed several critical bugs affecting certain scenarios like when WP Multisite is installed in a subdirectory (not to be confused with simply running in subdirectory mode)

= 2.1.4.6 =
* Fixed critical bug that was causing incompatibilities with some plugins that install their own tables to the database and in some cases the cloning operation was leaving a single custom table behind in the clone. This primarily affected Calendar plugins or other plugins where the table name began with higher letters (a, b, c) after the prefix like wp_a*, wp_b*, wp_c*

= 2.1.4.7 =
* Fixed critical bug that prevented user roles from getting cloned when cloning the ROOT site only

= 3.0.1 =
* All new V3!

= 3.0.3.1 =
* Fixed bug introduced in 3.0.3 affecting subdirectory mode

= 3.0.4 =
* Added one-click cloning from Manage Sites page
* Added pre WP 3.7 compatibility
* New search selector for source sites
* Revamped logs for better utility/readability
* Centralized ns_cloner_addon class for maintability

= 3.0.5.4 =
* Fixed issue with latest version of WP where a test validation site name containing only numbers is no longer valid