#!/usr/bin/perl
use warnings;
use strict;
use Socket;
use Term::ANSIColor;

sub check_service{
    my ($name, $port) = @_;
    print (" $name............ ");
    socket(SOCKET, PF_INET, SOCK_STREAM, (getprotobyname('tcp'))[2]);
    if(connect( SOCKET, pack_sockaddr_in($port, inet_aton("localhost")))){
        print color('bold green');
        print "UP ✔\n";
        print color('reset');
    } else {
        print color('bold red');
        print "DOWN ✖\n";
        print color('reset');
    }
}

check_service("SSH ..", 22);
check_service("SMTP .", 25);
check_service("DNS ..", 53);
check_service("HTTP .", 80);
check_service("POP3 .", 110);
check_service("IMAP .", 143);
check_service("LDAP .", 389);
check_service("MySQL ", 3306);
