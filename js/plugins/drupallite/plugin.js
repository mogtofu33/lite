/**
 * @file
 * Drupal Lite extended plugin.
 *
 * @ignore
 */

(function ($, Drupal, CKEDITOR) {

  'use strict';

  CKEDITOR.plugins.add('drupallite', {

    init: function (editor) {

      // Alter lite when init event is triggered.
      editor.on('lite:init', function (event) {

        // Lite plugin instance.
        var lite = event.data.lite;

        // If not auto start.
        if (!editor.config.lite.auto_start) {
          // And not already tracking.
          if (!editor.config.lite.isTracking) {
            // Disable tracking.
            lite.toggleTracking(false, true);
          }
        }
        else {
          // Handle auto show option.
          if (!editor.config.lite.auto_show) {
            lite.toggleShow(false, true);
          }
        }
      });
    },
    // We are adding our permissions check after init to be able to remove menus.
    afterInit: function (editor) {

      var config = editor.config.drupallite,
          permissions = editor.config.drupallite.permissions,
          key = '',
          remove_buttons = '',
          can_toggle = false,
          can_resolve = false;

      // If we are on a text format.
      if (editor.config.hasOwnProperty('drupal')) {
        if (editor.config.drupal.hasOwnProperty('format')) {
          key = '_' + editor.config.drupal.format;
        }
      }

      // User can not toggle tracking, so we hide the toolbar button.
      if (permissions.indexOf("toggle") !== -1) {
        can_toggle = true;
      }
      else if (permissions.indexOf("toggle" + key) !== -1) {
        can_toggle = true;
      }
      if (!can_toggle) {
        remove_buttons += 'lite-toggletracking,';
      }

      // User can not accept or reject changes.
      if (permissions.indexOf("resolve") !== -1) {
        can_resolve = true;
      }
      else if (permissions.indexOf("resolve" + key) !== -1) {
        can_resolve = true;
      }
      if (!can_resolve) {
        remove_buttons += 'lite-acceptall,lite-rejectall,lite-acceptone,lite-rejectone';
        // Disable contextual menu options.
        editor.removeMenuItem('lite-acceptone');
        editor.removeMenuItem('lite-rejectone');
      }

      // Remove toolbar buttons dynamically.
      editor.config.removeButtons = remove_buttons;

      // Register these global permissions and settings.
      editor.config.drupallite.removedButtons = remove_buttons;
      editor.config.drupallite.permissions.can_toggle = can_toggle;
      editor.config.drupallite.permissions.can_resolve = can_resolve;
    }

  });

})(jQuery, Drupal, CKEDITOR);
