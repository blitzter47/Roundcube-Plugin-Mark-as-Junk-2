#!/bin/bash
# 
# To be used with the plugin MarkAsJunk2 of RoundCube, via "cmd_learn"
#
# creates system's rules for SpamAssassin in
# /etc/spamassassin/01_roundcube_whitelist.cf for whitelisting but
# not blacklisting
# 
# @version 1.0
# Auteur : Chi-Huy Trinh, 2016
#
# arguments : [bl|wl] [nom_user] [[email1], email2, ...]

#set -x
whoami

option="$1"

user="$2"

spamass_system_rules_file="/etc/spamassassin/01_roundcube_whitelist.cf"
shift 2

# make sure system's whitelist exists
if [ ! -f "$spamass_system_rules_file" ]
then
    #mkdir -p "$(dirname "$spamass_system_rules_file")"
    #touch "$spamass_system_rules_file"
    #chmod o-rwx "$spamass_system_rules_file"
    echo "WARNING : in order to use system's whitelist capability, please"
    echo "create ""$spamass_system_rules_file"" file with permissions for www-data user"
fi

#for blacklisting
if [[ $option == "bl" ]]
then
    for email in "$@"
    do
       
        # And remove from system's whitelist
        grep -E "whitelist_from\s*$email" "$spamass_system_rules_file" | grep -v \#
        reponse="$?"
        if [ $reponse -eq 0 ] # if exists...
        then
            ## equivalent to sed -i "/^[^#]*whitelist_from[[:blank:]]\+$email/d" "$spamass_system_rules_file"
            echo "$(grep -vE "^[^#]*whitelist_from\s+$email" "$spamass_system_rules_file")" > "$spamass_system_rules_file"
            grep -E "whitelist_from\s*$email" "$spamass_system_rules_file" | grep -v \#
            reponse="$?"
        fi
    done
    
#for whitelisting
elif [[ $option == "wl" ]]
then
    for email in "$@"
    do
        
        # adding to system's whitelist
        grep "$email" "$spamass_system_rules_file" | grep -v \#
        reponse="$?"
        if [ $reponse -eq 1 ] # add rule only if not existed yet
        then
            #debug
            echo -e "\n"
            echo -e "whitelist_from $email\n"
            echo -e "whitelist_from $email\n" >> "$spamass_system_rules_file"
        fi
        
    done
fi

# cleanup of multiple new lines
echo "$(awk -v RS= -v ORS='\n\n' '1'  "$spamass_system_rules_file")" > "$spamass_system_rules_file"
