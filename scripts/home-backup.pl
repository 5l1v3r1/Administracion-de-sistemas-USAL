#!/usr/bin/perl
use strict;
use warnings;
use Try::Tiny;
use MySQL::Backup;
use Log::Log4perl;
use Archive::Tar;
use File::Find;

my $log_conf = "/etc/log4home.conf";
Log::Log4perl::init($log_conf);
my $logger = Log::Log4perl->get_logger();

my ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) = localtime();
$mon=$mon+1;
$year=$year+1900;

try {
    my $tar = Archive::Tar->new();
    find(sub { $tar->add_files($File::Find::name) }, '/home/');
    my $filename = "/var/backups/home/".$year."_".$mon."_".$mday."_".$hour."_".$min.".tar.gz";
    $tar->write($filename, 9);
    $logger->info( "HOME BACKUP");
    chmod 0700, $filename;
} catch {
$logger->error( "HOME BACKUP: $_");
