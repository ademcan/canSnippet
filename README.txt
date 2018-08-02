
canSnippet Community Edition (CE)

Website: http://www.cansnippet.org/
Author: ademcan (ademcan@ademcan.net)
Version: 1.0 beta
License: CC BY

canSnippet is an open source web-based application to save and share your snippets.


Installation :
- Download the latest canSnippet.zip file
- send the zip file to your server and unzip
- open the following URL in your web browser
http://[YOUR_DOMAIN]/canSnippet/
- Follow the unique instruction (i.e. fill in the form)
- Remove the install.php file for better security
- You are done ! Now you canSnippet...
- If you use nginx add the following line to protect your sqlite file
	location ~ \.sqlite {
		deny all;
	}

Add theme for prism :
Simply put your css file in css/prism_them and go in admin interface to select your theme

Screenshots:
http://ademcan.net/gallery/?dir=canSnippet

Demo :
http://ademcan.net/canSnippet/
