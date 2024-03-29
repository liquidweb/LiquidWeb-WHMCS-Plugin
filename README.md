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

For detailed instructions on how to install, setup and use our plugin please see our [articles here](https://www.liquidweb.com/kb/using-whmcs-liquid-web-reseller-plugin/)! You will find a series of posts that detail the steps to get setup and create your first product! If you're looking for alternate installation instructions please read: [Liquid Web WHMCS Plugin Installation Methods](https://www.liquidweb.com/kb/whmcs-plugin-installation-methods/).

Any managed Liquid Web customers experiencing immediate issues can feel free to contact our Heroic Support® via [Call, Email or LiveChat](https://www.liquidweb.com/support/).

## License

Copyright 2017 Liquid Web

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
