#!/bin/sh

git grep -z -l "' . PLUGIN_TEXT_DOMAIN . '" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/' . PLUGIN_TEXT_DOMAIN . '/power-board/g"
git grep -z -l "PLUGIN_TEXT_DOMAIN . '" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_TEXT_DOMAIN . '/'power-board/g"
git grep -z -l "PLUGIN_TEXT_DOMAIN" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_TEXT_DOMAIN/'power-board'/g"

git grep -z -l "PLUGIN_TEXT_NAME . '" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_TEXT_NAME . '/'PowerBoard/g"
git grep -z -l "PLUGIN_TEXT_NAME" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_TEXT_NAME/'PowerBoard'/g"

git grep -z -l "PLUGIN_METHOD_DESCRIPTION" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_METHOD_DESCRIPTION/'PowerBoard simplify how you manage your payments. Reduce costs, technical headaches \& streamline compliance using PowerBoard\\\'s payment orchestration.'/g"

git grep -z -l "PLUGIN_METHOD_TITLE . '" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_METHOD_TITLE . '/'PowerBoard payment/g"
git grep -z -l "PLUGIN_METHOD_TITLE" -- ':!build' ':!plugin.php' | xargs -0 sed -i -e "s/PLUGIN_METHOD_TITLE/'PowerBoard payment'/g"


echo "Clear NonSingularStringLiteralDomain and NonSingularStringLiteralText CS Warnings for PowerBoard concluded"
