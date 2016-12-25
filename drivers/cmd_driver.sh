#!/bin/bash
# 
# To be used with the plugin MarkAsJunk2 of RoundCube, via "cmd_learn"
#
# creates users' rules for SpamAssassin in
# $HOME/.spamassassin/user_prefs for whitelisting or for adding
# score of +10 pts for blacklisted senders' email.
#
# creates system's rules for SpamAssassin in
# /etc/spamassassin/01_roundcube_whitelist.cf for whitelisting but
# not blacklisting
#
# depends on cmd_driver_p2.sh and cmd_driver_v2.sh
# 
# @version 1.0
# @author : Chi-Huy Trinh, 2016
#
# arguments : [bl|wl] [name_user] [[email1], email2, ...]

#set -x
whoami

option="$1"

user="$2"

shift 2

#for blacklisting
if [[ $option == "bl" ]]
then
    for email in "$@"
    do
        sudo -u $user bash -c "/usr/share/roundcube/plugins/markasjunk2/drivers/cmd_driver_v2.sh bl $user $email"
	sudo -u www-data bash -c "/usr/share/roundcube/plugins/markasjunk2/drivers/cmd_driver_p2.sh bl $user $email"
    done
    
#for whitelisting
elif [[ $option == "wl" ]]
then
    for email in "$@"
    do
        sudo -u $user bash -c "/usr/share/roundcube/plugins/markasjunk2/drivers/cmd_driver_v2.sh wl $user $email"
	sudo bash -c "/usr/share/roundcube/plugins/markasjunk2/drivers/cmd_driver_p2.sh wl $user $email"
    done
fi
