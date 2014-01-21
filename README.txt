
canSnippet v1.0 beta

Author: ademcan (ademcan@ademcan.net)
Version: 1.0 beta
License: CC BY

canSnippet is an open source web-based application to save and share your snippets.
The advantages of canSnippet are :
- it's green (literally)
- open source (License CC BY)
- web-based
- easy installation process
- sqlite database
- syntax highlighting (based on prism.js)
- possibility to save private and public snippets
- unique link per snippet for easy sharing
- support many programming language (html, css, javascript, python, java, php, ruby, c, c++, sql)
- rss feed
- search engine
- browse panel using AJAX
- responsive design
- it's flat-green :)

Known issues :
If you have a \ (backslash) before quotes (for example on the title) add the following line to your .htaccess :
SetEnv MAGIC_QUOTES 0 

Alternatives to canSnippet :
SnippetVamp

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

Screenshots:
http://ademcan.net/gallery/?dir=canSnippet

Demo :
http://ademcan.net/canSnippet/
