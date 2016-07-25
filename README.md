# LiquidWeb-WHMCS-Plugin


The cleanest way to install the plugin is to unzip the main file, cd into the unzipped directory, then rsync the contents into the existing directories. 
<pre>
cd /home/$user/public_html/$path/$to/$whmcs
wget https://github.com/liquidweb/LiquidWeb-WHMCS-Plugin/archive/master.zip
unzip master.zip
cd LiquidWeb-WHMCS-Plugin-master/
rsync -avH includes/ ../includes/
rsync -avH modules/ ../modules/
</pre>

After you've extracted the files and placed them in the correct directories, login to the WHMCS admin section, hover over the "Setup" tab at the top of the page, then click on "Addon Modules". Click on the "Activate" button for "Storm on Demand Widget for WHMCS" as well as "Liquid Web Storm Servers Billing."

After both modules have been activated you should see "Liquid Web Storm Servers" located under the main "Addons" tab. 
