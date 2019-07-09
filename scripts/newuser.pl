#!/usr/bin/perl
use Quota;
use strict;
use warnings;
use File::stat;
use File::Find;

# Este script lo ejecuta el PAM del sistema
# Se configura en el fichero /etc/pam.d/common-session: pam_exec.so /usr/bin/newuser.pl
my $name = $ENV{'PAM_USER'}; 
if ($name ne "root"){
  my $gid = stat("/home/$name")->gid;
  my $uid = getpwnam($name);
  my $dev = "/dev/sda1";
  my $isoft = 5120; # 5 MB límite soft
  my $ihard = 7168; # 7 MB límite hard

  if ($gid == 500 || $gid==501){
      Quota::setqlim( $dev, $uid, $isoft, $ihard, 0, 0);
      find(\&wanted, "/home/$name/miWeb/");
  }
} 
sub wanted
{
    chmod 0755, $File::Find::name;
}
