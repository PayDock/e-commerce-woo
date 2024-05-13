=== PowerBoard ===

To install and configure Paydock plugin, you need:

* Web Server - Nginx:
* PHP 8.1
* MySQL version 8.0 or greater OR MariaDB 11.1 
* Support for HTTPS(SSL certificate).
* PHP memory limit of 256MB.
* PHP Requirements(curl, gd2, mbstring, xml, json, and zip


Plugin install steps:

1. Download zip file with the plugin:  
https://github.com/PayDock/e-commerce-woo/blob/power_board/power-board_v1.0.20.zip

2. Go to Wordpress->Plugins->Add new Plugin->Upload Plugin.

3. Upload zip.file and activate plugin.

4. Watch the video tutorial with step by step guidance: [link](https://www.loom.com/share/e3baad357d4444c6967ef4b96377784b?sid=4f21b0af-43f2-4081-9ce7-76bf946fa535).

5. Press [here](https://jetsoftpro.atlassian.net/wiki/spaces/Paydoc/pages/2607448306/Installing+plugin+the+first+time) to get the Wordpress admin user credentials.

===============

To download the latest version of our WooCommerce plugin, you can manually trigger a build and download the generated artifact directly from GitHub:

Step 1: Trigger the Build  
a. Visit the Actions tab in our GitHub repository (https://github.com/PayDock/e-commerce-woo/tree/power_board).  
b. Under Workflows, find the workflow named Build and upload Paydock plugin.  
c. Click on Run workflow.  
d. Select the branch and click the green Run workflow button.  

Step 2: Download the Plugin  
a. Once the workflow completes, click on the run that you just triggered.  
b. Scroll down to the Artifacts at the bottom of the page.  
c. Click on the link to download the ZIP file.  
