<?php
/*********************************************************************************
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/
?>

<?php

if (count($_POST) > 0) {

    $msgarray = array();
    if (!empty($_POST)) {

        $ldapEnabled = trim($_POST['ldapEnabled']);

        $host = trim($_POST['host']);
        $port = trim($_POST['port']);

        // OpenLDAP
        $accountFilterFormat = trim($_POST['accountFilterFormat']);
        $accountDomainName = trim($_POST['accountDomainName']);
        $accountDomainNameShort = trim($_POST['accountDomainNameShort']);
        $accountCanonicalForm = trim($_POST['accountCanonicalForm']);
        $baseDn = trim($_POST['baseDn']);

        $superAdminUsername = trim($_POST['superAdminUsername']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if ($host != '' && $port != '' && $username != '' && $password != ''
            && $accountDomainName != '' && $baseDn != '' && $superAdminUsername != '') {

            if (!preg_match("/^([0-9])+$/", $port)) {
                $msgarray['port'] = 'Please enter valid port number.';
            } else if (!preg_match("/^([1-4])$/", $accountCanonicalForm)) {
                $msgarray['accountCanonicalForm'] = 'Please select valid number for canonical form.';
            } else {
                $msgarray = main_function($host, $port, $username, $password,
                    $accountFilterFormat, $accountDomainName, $accountCanonicalForm,
                    $baseDn, $ldapEnabled, $accountDomainNameShort, $superAdminUsername);
                if (isset($msgarray['result']) && $msgarray['result'] == 'send') {
                    ?>
                    <script type="text/javascript" language="javascript">
                        window.location = "index.php?s=<?php echo sapp_Global::_encrypt(6);?>";
                    </script>
                    <?php
                }
            }

        } else {
            $msgarray = set_validation_messages($host, $port, $username, $password, $accountDomainName, $baseDn, $superAdminUsername, $ldapEnabled);
        }
    }
}
function main_function($host, $port, $username, $password,
                       $accountFilterFormat, $accountDomainName, $accountCanonicalForm,
                       $baseDn, $ldapEnabled, $accountDomainNameShort, $superAdminUsername)
{
    $msgarray = array();

    $ldapConnection = @ldap_connect($host, $port);
    ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
    $ldapBind = @ldap_bind($ldapConnection, $username, $password);

    if (!$ldapBind) {
        $error = ldap_errno($ldapConnection);
        $msgarray['error'] = ldap_err2str( $error ) . '(' . $error . ')';
        return $msgarray;
    }

    $result = @ldap_search($ldapConnection, $baseDn, sprintf($accountFilterFormat, "*"));
    if (!$result) {
        $error = ldap_errno($ldapConnection);
        $msgarray['error'] = ldap_err2str( $error ) . '(' . $error . ')';
        return $msgarray;
    }

    $data = @ldap_get_entries($ldapConnection, $result);

    if (!$data) {
        $error = ldap_errno($ldapConnection);
        $msgarray['error'] = ldap_err2str( $error ) . '(' . $error . ')';
        return $msgarray;
    }

    if (insert_into_db($superAdminUsername)) {
        $constantresult = write_LDAP_settings_constants($host, $port, $username, $password, $ldapEnabled, $accountFilterFormat,
            $accountDomainName, $accountDomainNameShort, $accountCanonicalForm, $baseDn, $superAdminUsername);
        if($constantresult === true)
        {
            $msgarray['result'] = 'send';
        }
    } else {
        $msgarray['superAdminUsername'] = 'Admin record is broken in database';
    }

    return $msgarray;
}

function set_validation_messages($host, $port, $username, $password, $accountDomainName, $baseDn, $superAdminUsername, $ldapEnabled)
{
    $msgarray = array();
    if ($ldapEnabled == 'false') {
        return $msgarray;
    }

    if ($host == '') {
        $msgarray['host'] = 'LDAP Server cannot be empty';
    }

    if ($port == '') {
        $msgarray['port'] = 'Port cannot be empty';
    }

    if ($username == '') {
        $msgarray['username'] = 'User name cannot be empty';
    }

    if ($password == '') {
        $msgarray['password'] = 'Password cannot be empty';
    }

    if ($accountDomainName == '') {
        $msgarray['accountDomainName'] = 'Account Domain Name cannot be empty';
    }

    if ($baseDn == '') {
        $msgarray['baseDn'] = 'Base DN cannot be empty';
    }

    if ($superAdminUsername == '') {
        $msgarray['superAdminUsername'] = 'Admin username cannot be empty';
    }

    if ($ldapEnabled == '') {
        $msgarray['ldapEnabled'] = 'Authentication cannot be empty';
    }

    return $msgarray;
}

function insert_into_db($superAdminUserName)
{
    $mysqlPDO = new PDO('mysql:host=' . SENTRIFUGO_HOST . ';dbname=' . SENTRIFUGO_DBNAME . '', SENTRIFUGO_USERNAME, SENTRIFUGO_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $stmt = $mysqlPDO->prepare("SELECT count(*) as count from main_users WHERE id=1");
    $stmt->execute();
    $row = $stmt->fetch();

    if ($row['count'] > 0) {
        $query1 = "UPDATE main_users SET username='" . $superAdminUserName . "' WHERE id=1";
    } else {
        return false;
    }

    $mysqlPDO->query($query1);

    return true;
}//end of insert_into_db function.


function write_LDAP_settings_constants($host, $port, $username, $password, $ldapEnabled, $accountFilterFormat,
                                       $accountDomainName, $accountDomainNameShort, $accountCanonicalForm, $baseDN,
                                       $superAdminUsername)
{
    $filename = '../public/ldap_constants.php';
    if (file_exists($filename)) {
        $db_content = "<?php
               defined('LDAP_ENABLED') || define('LDAP_ENABLED','" . $ldapEnabled . "');
	           defined('LDAP_HOST') || define('LDAP_HOST','" . $host . "');
	           defined('LDAP_PORT') || define('LDAP_PORT','" . $port . "');
	           defined('LDAP_USERNAME') || define('LDAP_USERNAME','" . $username . "');
	           defined('LDAP_PASSWORD') || define('LDAP_PASSWORD','" . $password . "');
	           defined('LDAP_ACCOUNTFILTERFORMAT') || define('LDAP_ACCOUNTFILTERFORMAT','" . $accountFilterFormat . "');
	           defined('LDAP_ACCOUNTDOMAINNAME') || define('LDAP_ACCOUNTDOMAINNAME','" . $accountDomainName . "');
	           defined('LDAP_ACCOUNTDOMAINNAMESHORT') || define('LDAP_ACCOUNTDOMAINNAMESHORT','" . $accountDomainNameShort . "');
	           defined('LDAP_ACCOUNTCANONICALFORM') || define('LDAP_ACCOUNTCANONICALFORM','" . $accountCanonicalForm . "');
	           defined('LDAP_BASEDN') || define('LDAP_BASEDN','" . $baseDN . "');
	           defined('LDAP_SUPER_ADMIN_USERNAME') || define('LDAP_SUPER_ADMIN_USERNAME','" . $superAdminUsername . "');
	         ?>";
        try {
            $handle = fopen($filename, "w+");
            fwrite($handle, trim($db_content));
            fclose($handle);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}//end of write_LDAP_settings_constants function.
?>
<form method="post" action="index.php?s=<?php echo sapp_Global::_encrypt(5); ?>" id="step5" name="step5"
      class="frm_install">
    <h3 class="page_title">LDAP Server Settings</h3>
    <div class="content_part">


        <span class="error_info"><?php echo isset($msgarray['error']) ? $msgarray['error'] : ''; ?></span>

        <div class="new-form-ui ">
            <label class="required">Use LDAP<img src="images/help.png" title="Use LDAP authentication method to access sentrifugo (ex: true/false)" class="tooltip"></label>
            <div>
                <?php
                if(isset($_POST['ldapEnabled'])) $ldapEnabled = $_POST['ldapEnabled'];
                else if(defined('LDAP_ENABLED')) $ldapEnabled = LDAP_ENABLED;
                else $ldapEnabled = 'true';
                ?>
                <select id="ldapEnabled" name="ldapEnabled" onchange="this.form.submit()">
                    <option value="true" <?php echo ($ldapEnabled == 'true')? 'selected':"";?> >True</option>
                    <option value="false" <?php echo ($ldapEnabled == 'false')? 'selected':"";?> >False</option>
                </select>
                <span><?php echo isset($msgarray['ldapEnabled'])?$msgarray['ldapEnabled']:'';?></span>
            </div>
        </div>

        <?php
        if ($ldapEnabled == 'true') $display = 'block';
        else $display = 'none';
        ?>
        <div id="ldapDiv" style="display:<?php echo $display; ?>">

            <div class="new-form-ui ">
                <label class="required">LDAP Server<img src="images/help.png"
                                                        title="IP address or host name of your LDAP server."
                                                        class="tooltip"></label>
                <div>
                    <input type="text" maxlength="100" value="<?php if (!$_POST) {
                        echo defined('LDAP_HOST') ? LDAP_HOST : '';
                    } else {
                        echo $_POST['host'];
                    } ?>" id="host" name="host">
                    <span><?php echo isset($msgarray['host']) ? $msgarray['host'] : ''; ?></span>
                </div>
            </div>

            <div class="new-form-ui ">
                <label class="required">Port<img src="images/help.png"
                                                 title="Port number to access LDAP server (Ex: 389)"
                                                 class="tooltip"></label>
                <div>
                    <input type="text" maxlength="50" value="<?php if (!$_POST) {
                        echo defined('LDAP_PORT') ? LDAP_PORT : '';
                    } else {
                        echo $_POST['port'];
                    } ?>" id="port" name="port">
                    <span><?php echo isset($msgarray['port']) ? $msgarray['port'] : ''; ?></span>
                </div>
            </div>

            <div class="new-form-ui ">
                <label class="required">User name<img src="images/help.png"
                                                      title="LDAP username provided by LDAP server (ex: CN=user1,CN=Users,DC=example,DC=com)."
                                                      class="tooltip"></label>
                <div>
                    <input type="text" maxlength="100" value="<?php if (!$_POST) {
                        echo defined('LDAP_USERNAME') ? LDAP_USERNAME : '';
                    } else {
                        echo $_POST['username'];
                    } ?>" id="username" name="username">
                    <span><?php echo isset($msgarray['username']) ? $msgarray['username'] : ''; ?></span>
                </div>
            </div>

            <div class="new-form-ui ">
                <label class="required">Password<img src="images/help.png"
                                                     title="Password for LDAP username."
                                                     class="tooltip"></label>
                <div>
                    <input type="password" maxlength="100" value="<?php if (!$_POST) {
                        echo defined('LDAP_PASSWORD') ? LDAP_PASSWORD : '';
                    } else {
                        echo $_POST['password'];
                    } ?>" id="password" name="password">
                    <span><?php echo isset($msgarray['password']) ? $msgarray['password'] : ''; ?></span>
                </div>
            </div>

            <div class="new-form-ui ">
                <label>Account Filter Format<img src="images/help.png"
                                                 title="The LDAP search filter used to search for accounts (ex: (&(objectClass=posixAccount)(uid=%s)))."
                                                 class="tooltip"></label>
                <div>
                    <input type="text" maxlength="40" value="<?php if (!$_POST) {
                        echo defined('LDAP_ACCOUNTFILTERFORMAT') ? LDAP_ACCOUNTFILTERFORMAT : '';
                    } else {
                        echo $_POST['accountFilterFormat'];
                    } ?>" id="accountFilterFormat" name="accountFilterFormat">
                    <span><?php echo isset($msgarray['accountFilterFormat']) ? $msgarray['accountFilterFormat'] : ''; ?></span>
                </div>
            </div>

            <div class="new-form-ui ">
                <label class="required">Account Domain Name<img src="images/help.png"
                                                                title="The FQDN domain for which the target LDAP server is an authority (e.g., example.com)."
                                                                class="tooltip"></label>
                <div>
                    <input type="text" maxlength="40" value="<?php if (!$_POST) {
                        echo defined('LDAP_ACCOUNTDOMAINNAME') ? LDAP_ACCOUNTDOMAINNAME : '';
                    } else {
                        echo $_POST['accountDomainName'];
                    } ?>" id="accountDomainName" name="accountDomainName">
                    <span><?php echo isset($msgarray['accountDomainName']) ? $msgarray['accountDomainName'] : ''; ?></span>
                </div>
            </div>

            <div class="new-form-ui ">
                <label>Account Domain Name Short<img src="images/help.png"
                                                     title="The 'short' domain for which the target LDAP server is an authority. This is usually used to specify the NetBIOS domain name for Windows networks but may also be used by non-AD servers."
                                                     class="tooltip"></label>
                <div>
                    <input type="text" maxlength="40" value="<?php if (!$_POST) {
                        echo defined('LDAP_ACCOUNTDOMAINNAMESHORT') ? LDAP_ACCOUNTDOMAINNAMESHORT : '';
                    } else {
                        echo $_POST['accountDomainNameShort'];
                    } ?>" id="accountDomainNameShort" name="accountDomainNameShort">
                    <span><?php echo isset($msgarray['accountDomainNameShort']) ? $msgarray['accountDomainNameShort'] : ''; ?></span>
                </div>
            </div>

            <div class="new-form-ui ">
                <label class="required">Account Canonical Form<img src="images/help.png"
                                                                   title="A small integer indicating the form to which account names should be canonicalized."
                                                                   class="tooltip"></label>
                <div>
                    <?php
                    if (isset($_POST['accountCanonicalForm'])) $accountCanonicalForm = $_POST['accountCanonicalForm'];
                    else if (defined('LDAP_ACCOUNTCANONICALFORM')) $accountCanonicalForm = LDAP_ACCOUNTCANONICALFORM;
                    else $accountCanonicalForm = '1';
                    ?>
                    <select id="accountCanonicalForm" name="accountCanonicalForm">
                        <option value="1" <?php echo ($accountCanonicalForm == '1') ? 'selected' : ""; ?> >1</option>
                        <option value="2" <?php echo ($accountCanonicalForm == '2') ? 'selected' : ""; ?> >2</option>
                        <option value="3" <?php echo ($accountCanonicalForm == '3') ? 'selected' : ""; ?> >3</option>
                        <option value="4" <?php echo ($accountCanonicalForm == '4') ? 'selected' : ""; ?> >4</option>
                    </select>
                    <span><?php echo isset($msgarray['accountCanonicalForm']) ? $msgarray['accountCanonicalForm'] : ''; ?></span>
                </div>
            </div>

            <div class="new-form-ui ">
                <label class="required">Base DN<img src="images/help.png"
                                                    title="The default base DN used for searching (e.g., for accounts). This option is required for most account related operations and should indicate the DN under which accounts are located."
                                                    class="tooltip"></label>
                <div>
                    <input type="text" maxlength="40" value="<?php if (!$_POST) {
                        echo defined('LDAP_BASEDN') ? LDAP_BASEDN : '';
                    } else {
                        echo $_POST['baseDn'];
                    } ?>" id="baseDn" name="baseDn">
                    <span><?php echo isset($msgarray['baseDn']) ? $msgarray['baseDn'] : ''; ?></span>
                </div>
            </div>

            <div class="new-form-ui ">
                <label class="required">Super Admin Username<img src="images/help.png"
                                                    title="Sentrifugo Super Admin Username from a directory."
                                                    class="tooltip"></label>
                <div>
                    <input type="text" maxlength="40" value="<?php if (!$_POST) {
                        echo defined('LDAP_SUPER_ADMIN_USERNAME') ? LDAP_SUPER_ADMIN_USERNAME : '';
                    } else {
                        echo $_POST['superAdminUsername'];
                    } ?>" id="superAdminUsername" name="superAdminUsername">
                    <span><?php echo isset($msgarray['superAdminUsername']) ? $msgarray['superAdminUsername'] : ''; ?></span>
                </div>
            </div>

            <input type="submit" value="Confirm" id="submitbutton" name="btnSubmit" class="save_button">
        </div>


    </div>
    <button name="previous" id="previous" type="button" class="previous_button"
            onclick="window.location='index.php?s=<?php echo sapp_Global::_encrypt(4); ?>';">Previous
    </button>
    <?php if (
        ($ldapEnabled == 'true' && defined('LDAP_HOST') && defined('LDAP_PORT')
            && defined('LDAP_USERNAME') && defined('LDAP_PASSWORD')
            && defined('LDAP_ACCOUNTDOMAINNAME') && defined('LDAP_ACCOUNTCANONICALFORM')
            && defined('LDAP_ACCOUNTCANONICALFORM') && defined('LDAP_BASEDN')) ||
        $ldapEnabled != 'true'

    ) { ?>
        <button name="next" id="next" type="button"
                onclick="window.location='index.php?s=<?php echo sapp_Global::_encrypt(6); ?>';">Next
        </button>
    <?php } ?>

</form>
<script type="text/javascript">
    $(document).ready(function () {
        $("select:not(.not_appli)").select2({
            formatResult: format_select,
            escapeMarkup: function (m) {
                return m;
            }
        });
        function format_select(selData) {
            return "<span>" + selData.text + "</span><div class='seldivimg'></div>";
        }


        $(".first_li").addClass('active');
        $(".first_icon").addClass('yes');

        <?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME')){ ?>
        $(".second_li").addClass('active');
        $(".second_icon").addClass('yes');
        <?php }?>

        <?php if(defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && constant('SUPERADMIN_EMAIL') != '') { ?>
        $(".third_li").addClass('active');
        $(".third_icon").addClass('yes');
        <?php }?>

        <?php if(defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_TLS') && defined('MAIL_AUTH')){ ?>
        $(".fourth_li").addClass('active');
        $(".fourth_icon").addClass('yes');
        <?php }else{?>
        $(".fourth_li").addClass('current');
        $(".fourth_icon").addClass('loding_icon');
        <?php }?>

        <?php if(defined('LDAP_HOST') && defined('LDAP_PORT') && defined('LDAP_USERNAME') && defined('LDAP_PASSWORD') && defined('LDAP_BASEDN') && defined('LDAP_ACCOUNTDOMAINNAME')){ ?>
        $(".fifth_li").addClass('active');
        $(".fifth_icon").addClass('yes');
        <?php }else{?>
        $(".fifth_li").addClass('current');
        $(".fifth_li").addClass('loding_icon');
        <?php }?>

        <?php if(defined('SENTRIFUGO_HOST') && defined('SENTRIFUGO_USERNAME') && defined('SENTRIFUGO_PASSWORD') && defined('SENTRIFUGO_DBNAME') && defined('APPLICATION_NAME') && defined('SUPERADMIN_EMAIL') && defined('MAIL_SMTP') && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD') && defined('MAIL_PORT') && defined('MAIL_TLS') && defined('MAIL_AUTH')){ ?>
        $(".sixth_li").addClass('active');
        $(".sixth_icon").addClass('yes');
        <?php }?>

        $('#ldapEnabled').change(function () {

            if ($('#ldapEnabled').val() == 'true') {
                $('#ldapDiv').show();
            }
            else if ($('#ldapEnabled').val() == 'false') {
                $('#ldapDiv').hide();
            }
            $('span[id^="errors-"]').html('');
            $('.error_info').html('');

        });

    });

</script>
