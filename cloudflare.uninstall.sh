#!/bin/bash

#
# Uninstall script for CloudFlare cPanel plugin
#

INSTALL_DIR="/usr/local/cpanel"

# Get PHP Version
CPANELSUPPORTEDPHPPATH=`ls -al $INSTALL_DIR/3rdparty/bin/php | awk '{print $11}'`
PHPVERSION=`echo $CPANELSUPPORTEDPHPPATH | rev | cut -d '/' -f 3 | rev`
PAPER_LANTERN_DIR="/usr/local/cpanel/base/frontend/paper_lantern"

if [ -d "$PAPER_LANTERN_DIR" ]; then
    rm -rf $INSTALL_DIR/base/frontend/paper_lantern/cloudflare
    rm -f $INSTALL_DIR/base/frontend/paper_lantern/dynamicui/dynamicui_cloudflare*.conf
    /usr/local/cpanel/scripts/uninstall_plugin $INSTALL_DIR/base/frontend/paper_lantern/cloudflare_plugin.tar.gz --theme paper_lantern
fi

rm -rf $INSTALL_DIR/base/frontend/jupiter/cloudflare
rm -f $INSTALL_DIR/base/frontend/jupiter/dynamicui/dynamicui_cloudflare*.conf
/usr/local/cpanel/scripts/uninstall_plugin $INSTALL_DIR/base/frontend/jupiter/cloudflare_plugin.tar.gz --theme jupiter

rm -rf $INSTALL_DIR/3rdparty/php/$PHPVERSION/lib/php/cloudflare
rm -rf $INSTALL_DIR/bin/admin/CloudFlare
rm -rf $INSTALL_DIR/Cpanel/API/CloudFlare.pm
rm -rf /root/.cpanel/datastore/cf_api
rm -rf $INSTALL_DIR/bin/admin/CloudFlare
rm -rf $INSTALL_DIR/bin/cloudflare_update.sh
## Remove post update call
cfonupgrade=`grep -F "cloudflare_update" /scripts/postupcp`
if [ "$cfonupgrade" != "" ]; then
	sed -i '/cloudflare_update.sh/d' /scripts/postupcp
fi

echo "Cloudflare cPanel plugin has been uninstalled."
