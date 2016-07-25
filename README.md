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
