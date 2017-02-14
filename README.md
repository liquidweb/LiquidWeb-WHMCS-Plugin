# LiquidWeb-WHMCS-Plugin

## Description

[Liquid Web](https://liquidweb.com/) Storm® API plugin for WHMCS providing [Liquid Web resellers](https://www.liquidweb.com/partners/reseller.html) a total solution to their provisioning needs.


## Installation

The cleanest way to install the plugin is to unzip the main file, cd into the unzipped directory, then rsync the contents into the existing directories.

<pre>
cd /home/$user/public_html/$path/$to/$whmcs
wget https://github.com/liquidweb/LiquidWeb-WHMCS-Plugin/archive/master.zip
unzip master.zip
cd LiquidWeb-WHMCS-Plugin-master/
rsync -avH includes/ ../includes/
rsync -avH modules/ ../modules/
cd ..
chown -R $user. .
</pre>

After you've extracted the files and placed them in the correct directories, login to the WHMCS admin section, hover over the "Setup" tab at the top of the page, then click on "Addon Modules". Click on the "Activate" button for "Storm on Demand Widget for WHMCS" as well as "Liquid Web Storm Servers Billing."

After both modules have been activated you should see "Liquid Web Storm Servers" located under the main "Addons" tab.

### Post-install clean up

Assuming you just completed the install, you can use the following commands to remove any unneeded install related files:

<pre>
cd ..
find ./LiquidWeb-WHMCS-Plugin-master/ -delete
rm master.zip
</pre>

The above commands, in order of execution, will change directory to the Main WHMCS folder and then will remove the plugin's install folder and package.

## Support

This plugin is provided as-is and does not carry any warranty nor does it include any form of support. Bugs and Issues with the plugin can be reported via the Issue tracker built into GitHub and they will be addressed in a timely manner.

Any managed Liquid Web customers experiencing immediate issues can feel free to contact our Heroic Support® via [Call, Email or LiveChat](https://www.liquidweb.com/support/).
