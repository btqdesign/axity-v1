=== MailChimp Immediate Send ===
Contributors: marcqueralt
Tags: mailchimp, email, newsletter, notification
Donate link: http://demomentsomtres.com/english/wordpress-plugins/mailchimp-immediate-send/
Requires at least: 3.7
Tested up to: 4.7.2
Stable tag: head

== Description ==

** WARNING ON UPDATE TO MAILCHIMP VERSION 3.X **

**If you have DeMomentSomTres Mailchimp Subscribe installed, you MUST upgrade BOTH plugins. You MUST, first, deactivate both plugins and then perform the upgrade. After that you can activate the plugins.**

**If you fail to follow these recommendations, you may get a 500 error in your server due to class redeclaration.**

The DeMomentSomTres Mailchimp Immediate Send plugin allows you to send an automatic message to all the subscribers of some list on content publication.

This plugin is **not** an alternative to Mandrill the MailChimp platform for transactional email.

You can get more information at [DeMomentSomTres Digital Marketing Agency](http://demomentsomtres.com/en/wordpress-plugins/mailchimp-immediate-send/).

= Features =

* Selection based on post type (post, page, custom post)
* Selection based on categories, post tags and any other taxonomy terms
* Selection based on multiple taxonomy terms.
* Template Support.
* Edit area configuration.

= Filters =

The following filters are added to the plugin:

* dms3immediate-campaign: customize the campaign name
* dms3immediate-message: customize the message text
* dms3immediate-title: customize the message subject

= History & Raison d’être =

While working for Consorci Administració Oberta de Catalunya we integrated Mailchimp and WordPress to perform RSS Campaigns.

Having them on operation the customer faced the need of sending immediate messages when a content was published on a specific category. However, Mailchimp RSS Campaings doesn't allow this because they are launched time based.

So we decide tu build this component that creates an adhoc campaign "regular campaing" every time a content in certain taxonomies is published.

== Installation ==

This portfolio plugin can be installed as any other WordPress plugin. 

= WARNING ON UPDATE TO MAILCHIMP VERSION 3.X =
If you have DeMomentSomTres Mailchimp Subscribe installed, you MUST upgrade BOTH plugins. You MUST, first, deactivate both plugins and then perform the upgrade. After that you can activate the plugins. 

If you fail to follow these recommendations, you will get a 500 error in your server due to class redeclaration.

= Requirements =

It manages the requirements by itself.

== Frequently Asked Questions ==

= I've updated a content and I want to send it again =

You have to check the Force Resend in the top right area called 'Send'.

= The post type I want to use does not appear on the admin page =

Check if this post type:

* has any taxonomy.
* the taxonomy has values set (with or without elements).

= Why pages are not shown in the admin page =

In default WordPress install classes won't be displayed because pages don't have any taxonomy.

= The content does not appear if I use a template =

This plugin uses the 'std_content00' section (mc:edit) in the MailChimp template.

= Where do I configure the mail contents? =

You can setup a template to make the mails look as you want.

Some of the parameters from the mail are taken from the MailChimp List defaults:

* Subject
* From name
* From email

= Where is my message stored in MailChimp? =

The message is stored as a campaign named as the list where it is sent with a YYYY/MM/DD HH:MM:SS suffix.

== Screenshots ==

TBD

== Changelog ==
= 3.201704251008 =
* Bug with TGMPA that did not detect requirements.
= 3.201704112011 =
* Bug Fatal Error if Titan Framework was not present.
= 3.20170302259 =
* TGMPA updated to 2.6.1
= 3.201703012005 =
* Bug: campaign settings
* Bug: unable to send campaign if groups informed
* Prevent update when background save is executed that forced messages to be sent when Broken Link checked is used to its job.
* Option not to send the campaigns. Only created.
* Translation compatible with WordPress Translate
= 3.201702280929 =
* update of version numbering
= 3.20170222c =
* Update of term names
= 3.20170222b =
* Admin bug
= 3.20170222 =
* DeMomentSomTres Tools is not required anymore
* Admin redesigned
* Filters added
* Code rebuild
* Sections can be choosen from a select
* Performance optimization
* Freemius added
* TGM used to manage dependencies

= 2.9.1 =
* Mailchimp library changed: changed maximum templates retrieved

= 2.9 =
* Compatibility with Mailchimp API v3.0

= 2.3 =
* Events manager compatibility and compatibility with plugins using - in terms and taxonomies slugs

= 2.2 =
* nl2br changed by apply_filters('the_contents')
* message if demomentsomtres tools is not installed
* Campaign name changed to mail subject + date

= 2.1 =
* nl2br usage on content sent (thanks to bjarteao)
* Delete conditions added in order to prevent involuntary deletion of conditions due to bug

= 2.0 =
* DeMomentSomTres Tools compatibility
* Administration optimization and redesign
* Groups of interest management
* Multiple terms (and) query to activate
* Prevent sending when quick edit is used

= 1.2.5 =
* compatibility upgrade admin helper library

= 1.2 =
* Post title as subject of the campaign
* Post title included in post as h1

= 1.1 = 
* Metabox added to force resend after publishing.
* Bug Fix: some posttypes sharing taxonomies where not shown.

= 1.0.3 =
* catalan translation

= 1.0.2 =
* remove internal git references

= 1.0.1 = 
* Template support error solved

= 1.0 =
* Initial version translation ready

= Next Steps =

* Translate it
* freely choose an edit area for every template