# EverPress
WordPress Evernote: Just Married

Been waiting a long time for this, WordPress and Evernote are there, shining in the fullness.
WordPress, prince of Hobbits, outstanding in helping human race concoct articles, tutorials, etc. aka "Blogging".
Evernote, princess of Elves, almost famous in note taking for Alzheimer elders, although has a little bit greeny look due to in diet mode as well as overeat Popeye's spinach, but she is still looking so, so fine.
Just enough fun :)

THE PREMISE
There are so many tools to help you integrate WordPress with Evernote, because nothing compares when you can write down your notes, tutorials in the convenient Evernote's editor, then publish straight into WordPress.

	* Evernote Sync - The evernote timing synchronization to wordpress - wordpress.org
	* Evernote and WordPress - Better together - zapier.com
	* Connect Evernote to WordPress - ifttt.com
	* Publish and manage content from Evernote to Wordpress - blogwith.co
	* The Evernote Powered Blogging Platform - Postach.io

By the way, try your best.
Honestly, nothing yet lets me feel working in the natural way when integrate them, so I decided to build for myself a "WordPress hack".


THE THEORY
With this hack, you can:

	* Create posts in WordPress straight from the Evernote editor, sure.
	* The new "note post" will be sanitize from Evernote appended promo and extra HTML.
	* Auto create category(s) based on Evernote notebook(s), support hierarchical: notebook inside stack (Web Design -> WordPress, for example).
	* Tags will be fetch automatically from the Evernote note and assign to the post.
	* Support revision, when you published a post via Evernote, you can edit the existing note, send to WordPress, the existing post will be updated instead of create a new one.
	* Support Featured Content Slider, the existing post will be updated without loosing "featured" status.
	* Append the original source/author URL if you made the note by borrow content from somewhere in the Internet.

LUGGAGE
Ensure the bellow luggage is in your hands:

	* JetPack Post By Email for WordPress

Install the JetPack plugin if you are using self-hosted Wordpress.
Active the Post By Email module:
Get the special mail box address:

	* Evernote PHP library, Evernote Developer API key, and the snoopy-evernote.php script

Evernote Developer API used to read Evernote notes via the Evernote PHP library with the help of snoopy-evernote.php script.
Basically, snoopy-evernote.php used to interactive with your Evernote database via APIs: retrieve tags, categories, the source url, etc., how it works will be dissected in the other article.

Grab this key here: https://www.evernote.com/api/DeveloperToken.action
Read more: OAuth - Evernote Developers
Everything you need is here, in snoopy-evernote.zip, extract as snoopy-evernote in the same place where functions.php file of your current theme is located: wp-content/themes/your-theme/
Assign the API key as $authToken in line 37 of snoopy-evernote.php
http://adf.ly/5891130/snoopy-evernote

	* Wordpress function: SnoOpy_Evernote

Place the content of snoopy-everpress.php inside the functions.php file of your current theme

THE FLIGHT
Now each time you write an Evernote's note, remember that you can publish it into WordPress via Share -> Send a Copy menu.
"To :" field is the special email of JetPack Post By Email.
"Comment :" as the bellow picture.
Do the same if you want to update the existing Wordpress post.
Happy ending for the WordPress Evernote love :)
