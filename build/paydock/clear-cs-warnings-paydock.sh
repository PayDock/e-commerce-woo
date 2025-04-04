#!/bin/sh

git grep -z -l "' . PLUGIN_TEXT_DOMAIN . '" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/' . PLUGIN_TEXT_DOMAIN . '/paydock/g"
git grep -z -l "PLUGIN_TEXT_DOMAIN . '" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_TEXT_DOMAIN . '/'paydock/g"
git grep -z -l "PLUGIN_TEXT_DOMAIN" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_TEXT_DOMAIN/'paydock'/g"

git grep -z -l "PLUGIN_TEXT_NAME . '" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_TEXT_NAME . '/'Paydock/g"
git grep -z -l "PLUGIN_TEXT_NAME" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_TEXT_NAME/'Paydock'/g"

git grep -z -l "PLUGIN_METHOD_DESCRIPTION" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_METHOD_DESCRIPTION/'Paydock simplify how you manage your payments. Reduce costs, technical headaches \& streamline compliance using Paydock\\\'s payment orchestration.'/g"

git grep -z -l "PLUGIN_METHOD_TITLE . '" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_METHOD_TITLE . '/'Paydock payment/g"
git grep -z -l "PLUGIN_METHOD_TITLE" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_METHOD_TITLE/'Paydock payment'/g"


echo "Clear NonSingularStringLiteralDomain and NonSingularStringLiteralText CS Warnings for Paydock concluded"
