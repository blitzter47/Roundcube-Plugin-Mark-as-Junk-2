#!/bin/bash
# 
# To be used with the plugin MarkAsJunk2 of RoundCube, via "cmd_learn"
#
# creates users' rules for SpamAssassin in
# $HOME/.spamassassin/user_prefs for whitelisting or for adding
# score of +10 pts for blacklisted senders' email.
# 
# @version 1.0
# @author : Chi-Huy Trinh, 2016
#
# arguments : [bl|wl] [name_user] [[email1], email2, ...]

#set -x
whoami

option="$1"

user="$2"

RULENAME="$(echo $user | awk '{print toupper($0)}')"

spamass_rules_file="$(grep -E "^$user" /etc/passwd | cut -d: -f6)""/.spamassassin/user_prefs"
shift 2

# make sure user's whitelist exists
if [ ! -f "$spamass_rules_file" ]
then
    mkdir -p "$(dirname "$spamass_rules_file")"
    touch "$spamass_rules_file"
fi

echo "$spamass_rules_file"

#for blacklisting
if [[ $option == "bl" ]]
then
    for email in "$@"
    do
        # regexify the sender's email
        email_regex=${email//./\\\\.}
        email_regex=${email_regex//@/\\\\@}
        email_regex=${email_regex//+/\\\\+}
        email_regex=${email_regex//-/\\\\-}
        email_regex=${email_regex//%/\\\\%}

        grep "$email_regex" "$spamass_rules_file" | grep -v \#
        reponse="$?"
        if [ $reponse -eq 1 ] # add rule only if not existed yet
        then
            rule_time_id="$(date +"%y%m%d%H%M%S")"
            # debug
            echo -e "\n"
            echo -e "header\t__""$RULENAME""_BL_""$rule_time_id""\tFrom =~ /""$email_regex""/i"
            echo -e "meta\t""$RULENAME""_SPAM_""$rule_time_id""\t( __""$RULENAME""_BL_""$rule_time_id"" )"
            echo -e "score\t""$RULENAME""_SPAM_""$rule_time_id""\t10\n"
            # end of debug
            
            echo -e "header\t__""$RULENAME""_BL_""$rule_time_id""\tFrom =~ /""$email_regex""/i" >> "$spamass_rules_file"
            echo -e "meta\t""$RULENAME""_SPAM_""$rule_time_id""\t( __""$RULENAME""_BL_""$rule_time_id"" )" >> "$spamass_rules_file"
            echo -e "score\t""$RULENAME""_SPAM_""$rule_time_id""\t10\n" >> "$spamass_rules_file"
        fi
        
        # And remove from user's whitelist
        grep -E "whitelist_from\s*$email" "$spamass_rules_file" | grep -v \#
        reponse="$?"
        if [ $reponse -eq 0 ]
        then
            ## equivalent to sed -i "/^[^#]*whitelist_from[[:blank:]]\+$email/d" "$spamass_rules_file"
            echo "$(grep -vE "^[^#]*whitelist_from\s+$email" "$spamass_rules_file")" > "$spamass_rules_file"
            grep -E "whitelist_from\s*$email" "$spamass_rules_file" | grep -v \#
            reponse="$?"
        fi
    done
    
#for whitelisting
elif [[ $option == "wl" ]]
then
    for email in "$@"
    do
        # adding to user's whitelist
        grep "$email" "$spamass_rules_file" | grep -v \#
        reponse="$?"
        if [ $reponse -eq 1 ] # add rule only if not existed yet
        then
            #debug
            echo -e "\n"
            echo -e "whitelist_from $email\n"
            # end of debug
            
            echo -e "whitelist_from $email\n" >> "$spamass_rules_file"
        fi
                
        # And remove from blacklist
        # regexify the sender's email
        email_regex=${email//./\\\\.}
        email_regex=${email_regex//@/\\\\@}
        email_regex=${email_regex//+/\\\\+}
        email_regex=${email_regex//-/\\\\-}
        email_regex=${email_regex//%/\\\\%}
        
        grep -E "$email_regex" "$spamass_rules_file" | grep -v \#
        reponse="$?"
        if [ $reponse -eq 0 ] # if exists...
        then
            matched_rule_id=$(grep -E "$email_regex" "$spamass_rules_file" | grep -v \# | head -n1 | grep -oE [0-9]+)
            ## equivalent a sed -i "/^[^#]\+$matched_rule_id/d" "$spamass_rules_file"
            echo "$(grep -vE "^[^#]+$matched_rule_id" "$spamass_rules_file")" > "$spamass_rules_file"
            grep -E "$email_regex" "$spamass_rules_file" | grep -v \#
            reponse="$?"
        fi
    done
fi

# cleanup of multiple new lines
echo "$(awk -v RS= -v ORS='\n\n' '1'  "$spamass_rules_file")" > "$spamass_rules_file"
