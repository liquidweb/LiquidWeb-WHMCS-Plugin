# Changelog

## 1.2.5

* Docs: updated chown command
* removed unused serverUrl variables
* Adding the text "Please note that this may take 30..." under the load wheel
* Adding price column to the product setup wizard and also when loading configs
* Removing the % based price calculator from the product setup wizard
* Added "Deprecated" message for "StormOnDemand" modules.
* Hide non working configurations
* Caching admin page widget data, data gets updated from WHMCS cron.
* Docs: Add license and update readme.
* Docs: Add CHANGELOG.md file; transfer known changes into new file.

## 1.2.4

* new file added for ajax call handling

## 1.2.3

* Changed LiquidwebPrivateParent module configuration code to support WHMCS 7
* Hide "WiredTree VPS configs" from config lists in VPS setup page and configurable options

## 1.2.1

* Fixed issue not displaying error message in Products & Services page in WHMCS version 7.

## 1.2.0

* Added configuration page for reseller to select that they use a custom theme to accommodate custom theme names to execute required scripts.
* Added option to log API errors and option to log all API logs that the admin can turn on and off in the new configuration page. These errors and API calls will be logged to the WHMCS Module Logs found in the WHMCS Admin Control Panel > Utilities > Logs > Module Logs
* Added Product Import Wizard to (1) Select the products, (2) Provide Liquid Web account credentials, (3) Create new API user account, (4) Configure SSD VPS, (5) Configure Private parent
* Added module activation reporting to send data to Liquid Web for WHMCS Server Name, Server IP, Liquid Web Addon Registration Date, Liquid Web Addon Module Version, WHMCS Version, WHMCS Theme Name
* Changed Author Name in all Liquid Web modules
* Added “Goto Wizard page” button in admin home page
* Slider control for Memory and Diskspace.
* ‘NO BACKUP’ option
* CREATE "I want to give my VPS the following hostname" for entering the hostname for the customer.
* No modify option in wizard, instead it should create new products each time.
* On save and continue go to first page of the wizard.
* Create product groups if no group exists
* Enable reseller to generate new API credentials or use existing credentials in the setup wizard

## Pre-1.2.0

* N/A
