Lite
===========

Lite integrates the LITE track changes plugin for CKEditor with Drupal.
https://ckeditor.com/addon/lite

Installation
------------

* Normal module installation procedure. See
  https://www.drupal.org/documentation/install/modules-themes/modules-8
* Download the version **1.2.28** of the LITE CKEditor plugin  from
  https://ckeditor.com/addon/lite and extract it to /libraries. The extracted
  folder must be named lite. So Lite plugin file can be accessed from
  _/libraries/lite/plugin.js_
* To download Lite library with composer using Composer template for Drupal
  https://github.com/drupal-composer/drupal-project, add these lines to your
  composer.json file:
  ```
  "repositories": [
      {
          "type": "composer",
          "url": "https://packages.drupal.org/8"
      },
      {
        "type": "package",
        "package": {
          "name": "library/lite",
          "version": "1.2.28",
          "type": "drupal-library",
          "dist": {
            "url": "https://download.ckeditor.com/lite/releases/lite_1.2.28.zip",
            "type": "zip"
          }
        }
      }
  ],
  "require": {
      ..... YOUR PACKAGES .....
      "library/lite": "1.2.28"
  },
  ```

* **Optionnal** By default changes are only visible in the Wysywig editor, if you
  want to display changes on the _view mode_ with _tooltip_ support you must install
  **Opentip** library.
  Create a folder opentip in your /libraries folder.
  From https://github.com/enyo/opentip/tree/master/downloads, download
  **opentip-jquery.min.js** and place it in /libraries/opentip, so your file can
  be accessed from
  _/libraries/opentip/opentip-jquery.min.js_
* Visit status report to ensure your Lite plugin is correctly loaded.
* Enable any of the track changes buttons by dragging them into the active
  toolbar configuration for the desired text formats from the Text Formats
  configuration page.
* Enable the Lite filter and configure
* Configure Lite permissions to allow roles to toggle or resolve changes
* If the Limit allowed HTML tags filter is enabled, add to the Allowed HTML tags:
  ```
  <del class="ice-del ice-cts-*" data-changedata data-userid data-cid data-last-change-time data-time data-username> <ins class="ice-ins ice-cts-*" data-changedata data-userid data-cid data-last-change-time data-time data-username>
  ```

Configuration
------------

Lite plugin default state settings are available from the text format
configuration form.

After the installation, you can configure specific options form
/admin/config/content/lite/settings

Content moderation
------------

If the Drupal Content Moderation module is enabled, Lite text format option by
Workflow and by states will be available.

Prior to text format configuration you must enable a Workflow on your content
type and set the Workflow transitions permissions to your roles accordingly.

Known issues
------------

Lite 1.2.30 can cause an issue with images or copy/paste, see
https://www.drupal.org/node/2907869

If an image is added without any text it should be not tracked.

If image caption is enable, the image will not be tracked.
