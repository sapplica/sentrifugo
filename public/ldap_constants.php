<?php
               defined('LDAP_ENABLED') || define('LDAP_ENABLED','true');
	           defined('LDAP_HOST') || define('LDAP_HOST','172.25.3.236');
	           defined('LDAP_PORT') || define('LDAP_PORT','389');
	           defined('LDAP_USERNAME') || define('LDAP_USERNAME','CN=admin1,CN=Users,DC=domain3,DC=local');
	           defined('LDAP_PASSWORD') || define('LDAP_PASSWORD','6kjSTXO3QqdCf83');
	           defined('LDAP_ACCOUNTFILTERFORMAT') || define('LDAP_ACCOUNTFILTERFORMAT','(&(objectClass=user)(cn=%s))');
	           defined('LDAP_ACCOUNTDOMAINNAME') || define('LDAP_ACCOUNTDOMAINNAME','DT3-AD.domain3.local');
	           defined('LDAP_ACCOUNTDOMAINNAMESHORT') || define('LDAP_ACCOUNTDOMAINNAMESHORT','DOMAIN3');
	           defined('LDAP_ACCOUNTCANONICALFORM') || define('LDAP_ACCOUNTCANONICALFORM','3');
	           defined('LDAP_BASEDN') || define('LDAP_BASEDN','DC=domain3,DC=local');
	           defined('LDAP_BINDREQUIRESDN') || define('LDAP_BINDREQUIRESDN','true');
	           defined('LDAP_SUPER_ADMIN_USERNAME') || define('LDAP_SUPER_ADMIN_USERNAME','admin1');
	         ?>