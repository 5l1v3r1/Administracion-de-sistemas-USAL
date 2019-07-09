<?php 
    $db = mysqli_connect('localhost', 'admin', 'password', 'Admin_sis') or die('Error: mysql connect');
    $ds = ldap_connect("localhost");
    if ($ds) {
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_bind($ds, "cn=admin,dc=mmacademy,dc=es", "Ud?Yug62H2LQxb") or die('Error: ldap connect');
    }
?>
