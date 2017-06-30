<?php
               defined('LDAP_ENABLED') || define('LDAP_ENABLED','true');
	           defined('LDAP_HOST') || define('LDAP_HOST','localhost');
	           defined('LDAP_PORT') || define('LDAP_PORT','389');
	           defined('LDAP_USERNAME') || define('LDAP_USERNAME','CN=user.second,OU=users,DC=test,DC=com');
	           defined('LDAP_PASSWORD') || define('LDAP_PASSWORD','12345');
	           defined('LDAP_ACCOUNTFILTERFORMAT') || define('LDAP_ACCOUNTFILTERFORMAT','(&(objectClass=posixAccount)(cn=%s))');
	           defined('LDAP_ACCOUNTDOMAINNAME') || define('LDAP_ACCOUNTDOMAINNAME','test.com');
	           defined('LDAP_ACCOUNTDOMAINNAMESHORT') || define('LDAP_ACCOUNTDOMAINNAMESHORT','TEST');
	           defined('LDAP_ACCOUNTCANONICALFORM') || define('LDAP_ACCOUNTCANONICALFORM','1');
	           defined('LDAP_BASEDN') || define('LDAP_BASEDN','OU=users,DC=test,DC=com');
	           defined('LDAP_BINDREQUIRESDN') || define('LDAP_BINDREQUIRESDN','true');
	         ?>