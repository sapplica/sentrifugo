Sentrifugo
==========

Sentrifugo is a free and powerful new-age Human Resource Management System that can be easily configured to adapt to your organizational processes.



Installing Sentrifugo Version 3.1.1
======================================

Sentrifugo comes with its own web-installer to help guide you through the installation process. 

Note: You can also find these steps in http://www.sentrifugo.com/home/installationguide

Table of Contents:

1. What server Sentrifugo works on?
2. Windows installation Guide 
3. Linux installation Guide 
4. MAC installation Guide 
5. Upgrading your application code with patches

	1. What server does Sentrifugo work on?
	=======================================
	Sentrifugo works only on Apache Server

	2. Windows Installation Guide 
	=============================
		AMP stack for Windows
		---------------------
		- The recommended AMP stack for Windows is XAMPP (Download the installer from basic package)
		- The system installer for XAMPP will guide you through the installation process

		Copying files 
		-------------
		- Move Sentrifugo zip file into the document root of Apache HTTP server.
		- If you used XAMPP for windows, document root is   <XAMPP installed location>\htdocs\
		- For example: C:\xampp\htdocs\

		Extracting 
		----------
		- Extract the Sentrifugo zip file in the document root of Apache HTTP server

		Web Installer  
		-------------
		- XAMPP users; the AMP stack for Windows needs to be started manually.
		- Using a JavaScript enabled browser go to http://<webhost>/sentrifugo/; Where <webhost> is localhost if it is installed in the machine you are
		currently working on, IP address if it is remotely hosted 
		
		Pre-requisites
		--------------
		The system requirements for installing Sentrifugo are described below. Make sure your system meets these requirements.

		a. PHP 5.3 or later
			You can download PHP 5.3 or later by visiting http://windows.php.net/download/

		b. PDO MySQL (for MySQL connection) 
			To install Sentrifugo on windows, you need to enable the PDO and PDO_MYSQL extensions in your php.ini file. You can add the following
			 lines in your php.ini file:

			1. extension=php_pdo.dll
			2. extension=php_pdo_mysql.dll
		
		c. Rewrite module (for working of MVC architecture)
			To activate the module, the following line in httpd.conf needs to be uncommented:

			1. LoadModule rewrite_module modules/mod_rewrite.so
			To see whether it is already active, try putting a .htaccess file into a web directory containing the line

			2. RewriteEngine on
			If this works without throwing a 500 internal server error, and the .htaccess file gets parsed, URL rewriting works.

			You also need to make sure that in your httpd.conf, AllowOverrides is enabled:

			3. AllowOverride all
			This is important as many httpd.conf ship by default with allowoverride none

		d. GD library (for images)
			You can add the following lines in your php.ini file:

			1. extension = php_gd2.dll
		
		e. Open SSL (For SSL and TSL Protocols)
			Download the installer for OpenSSL 1.0.1e from http://www.openssl.org/related/binaries.html

			If OpenSSL is already installed in your system, to enable this extension in your php.ini file, you can add the following line in your php.ini
			file:

			1. extension=php_openssl.dll

	3. Linux Installation Guide 
	===========================
		AMP stack for Linux
		-------------------
		- The recommended AMP stack for Linux is XAMPP Linux 1.6 (Download the complete stack and not the upgrades)
		- The system installer for XAMPP in the XAMPP site will guide you through the installation process
		- Start the stack manually every time you reboot.
		- Change the ownership of Sentrifugo files (Ex: /opt/xampp/htdocs/sentrifugo/ $ chown -R nobody.nobody)

		Copying files 
		-------------
		- Move Sentrifugo zip file into the document root of Apache HTTP server.
		- If you used XAMPP for windows, document root is   <XAMPP installed location>\htdocs\
		- For example: C:\xampp\htdocs\

		Extracting 
		----------
		- Extract the Sentrifugo zip file in the document root of Apache HTTP server

		Web Installer  
		-------------
		- XAMPP users; the AMP stack for Linux needs to be started manually.
		- Using a JavaScript enabled browser go to http://<webhost>/sentrifugo/; Where <webhost> is localhost if it is installed in the machine you are 
		currently working on, IP address if it is remotely hosted 
		
		Pre-requisites
		--------------
		The system requirements for installing Sentrifugo are described below. Make sure your system meets these requirements.

		a. PHP 5.3 or later
			To install PHP 5.3 on Linux, please follow the below links:

			For Ubuntu: https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu
			For Redhat and CentOS: http://www.thetechnicalstuff.com/install-php5-3-in-centos-and-redhat/

		b. PDO MySQL (for MySQL connection) 
			To install Sentrifugo on Linux, you can compile php with --with-pdo-mysql in your php.ini, and add the following lines:

			1. extension=pdo.so
			2. extension=pdo_mysql.so
		
		c. Rewrite module (for working of MVC architecture)
			activate mod_rewrite in linux, open the terminal and add the below line:

			1. sudo a2enmod rewrite
			
			You also need to make sure that in your httpd.conf, AllowOverride is enabled:
			2. AllowOverride All
			
		d. GD library (for images)
			To install GD library in Linux, open the terminal and add the below lines:
			
			1. #apt-get install php5-gd
		
		e. Open SSL (For SSL and TSL Protocols)
			Download the OpenSSL 1.0.1c tarball archive from the OpenSSL web site at http://www.openssl.org/source/

	4. MAC Installation Guide
	=========================
		AMP stack for MAC
		-----------------
		- The recommended AMP stack for MAC is MAMP 
		- The system installer for XAMPP will guide you through the installation process
		- If MAMP is previously installed, the installer will rename the MAMP folder to MAMP_current_date.
		- An existing <htdocs> folder will be moved to your new /Applications/MAMP folder.
		- Your /Applications/MAMP_current_date folder can now be deleted. You can keep it if you wish to fall back to your original setup.

		Copying files 
		-------------
		- Move Sentrifugo zip file into the document root of Apache HTTP server.
		- If you used XAMPP for windows, document root is   <XAMPP installed location>\htdocs\
		- For example: C:\xampp\htdocs\

		Extracting 
		----------
		- Extract the Sentrifugo zip file in the document root of Apache HTTP server

		Web Installer  
		-------------
		- MAMP users; the AMP stack for MAC needs to be started manually.
		- Using a JavaScript enabled browser go to http://<webhost>/sentrifugo/; Where <webhost> is localhost if it is installed in the machine you are 
		currently working on, IP address if it is remotely hosted
		
		Pre-requisites
		--------------
		The system requirements for installing Sentrifugo are described below. Make sure your system meets these requirements.

		a. PHP 5.3 or later
			You can download PHP 5.3 or later by visiting http://php.net/downloads.php

		b. PDO MySQL (for MySQL connection) 
			To install Sentrifugo on MAC, you need to enable the PDO and PDO_MYSQL extensions in your php.ini file. You can add the following lines in 
			your php.ini file:

			1. extension=php_mysqli.so
			2. extension=php_pdo_mysql.so
		
		c. Rewrite module (for working of MVC architecture)
			To activate mod_rewrite module in MAC, add the below line to httpd.conf file

			1. LoadModule rewrite_module libexec/apache2/mod_rewrite.so
			2. LoadModule php5_module libexec/apache2/libphp5.so
			
			Also, make sure that AllowOverride is set to All within the <Directory "/Library/WebServer/Documents"> section.

		d. GD library (for images)
			You can add the following lines in your php.ini file:

			1. extension = gd.so
		
		e. Open SSL (For SSL and TSL Protocols)
			Download the installer for OpenSSL from http://www.openssl.org/source/

	5. Upgrading your application code with patches
	=====================================================
	
	MANUAL
	
	1. Download Sentrifugo.zip
	2. Extract the zip file
	3. Check for patches folder in the extracted Sentrifugo folder
	4. Check your application code version in index.php Ex: defined('CODEVERSION')|| define('CODEVERSION', '3.1');
	5. If your code version is not 3.1, take patches <CODEVERSION> till patches 3.1.1  
	  Eg: If CODEVERSION is 3.0, take patches from 3.0 to patches 3.1.1
	6. If your code version is 3.1, consider patches_3.1.1 folder

	 AUTOMATION 

	1. Login into your application as 'Super Admin'
	2. Click on Profile pop-up on the right hand-side
	3. Select Upgrade application
	4. Current application version will be displayed. Choose version to upgrade as 3.1.1
	5. On clicking 'Upgrade' button, patches_3.1.1 will be downloaded

	To install patch 3.1.1			

	1. Extract the patches_3.1.1 .zip file 
	2. Copy and replace the files to your current application folder 
	3. Execute the queries in sql/queries.txt file, if any (commands are mentioned at the bottom of this document)

	NOTE:
	Once you login to the application, update any role in Human Resources -> User Management -> Roles and Privileges page.

	Refer UPGRADE document for a detailed description of the installation process of patches.

