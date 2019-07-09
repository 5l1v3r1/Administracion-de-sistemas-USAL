#!/usr/bin/perl
use strict;
use warnings;
use Try::Tiny;
use MySQL::Backup;
use Log::Log4perl;

my $log_conf = "/etc/log4mysql.conf";
Log::Log4perl::init($log_conf);
my $logger = Log::Log4perl->get_logger();

my ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) = localtime();
$mon=$mon+1;
$year=$year+1900;

try {
    my $mb = new MySQL::Backup('Admin_sis','127.0.0.1','admin','contraseña',{'USE_REPLACE' => 1, 'SHOW_TABLE_NAMES' => 1});
    my $filename = "/var/backups/mysql/$year"."_".$mon."_".$mday."_".$hour."_".$min.".sql";
    open(my $sql, ">", $filename);
    print $sql $mb->create_structure();
    print $sql $mb->data_backup();
    $logger->info( "MYSQL BACKUP");
    close $sql;
    chmod 0700, $filename;
} catch {
$logger->error( "MYSQL BACKUP: $_");
