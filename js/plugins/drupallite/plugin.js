/**
 * @file
 * Drupal Lite extended plugin.
 *
 * @see https://github.com/loopindex/ckeditor-track-changes#api
 *
 * @ignore
 */

(function ($, Drupal, drupalSettings, CKEDITOR) {

  'use strict';

  CKEDITOR.plugins.add('drupallite', {

    // We evaluate the auto show option.
    /*
    init: function (editor) {

      var config = editor.config.drupallite,
          format = editor.config.drupal.format,
          key = '';

      // Alter lite when init event is triggered.
      editor.on('lite:init', function (event) {

        // Lite plugin instance.
        var lite = event.data.lite;
        // Handle auto show setting.
        if (!editor.config.lite.auto_show) {
          @see https://github.com/loopindex/ckeditor-track-changes#api
          lite.toggleShow(false, true);
        }
      });
    },
    */

    // We are checking after init to be able to remove contextual menu items.
    afterInit: function (editor) {

      var config = editor.config.drupallite.options,
          permissions = editor.config.drupallite.permissions,
          format = null,
          moderated = editor.config.drupallite.options.moderation,
          node = null,
          key = '',
          remove_buttons = '',
          can_toggle = false,
          can_resolve = false,
          debug = false;

      if (editor.config.hasOwnProperty('drupal')) {
        format = editor.config.drupal.format;
      }
      if (drupalSettings.hasOwnProperty('lite')) {
        if (drupalSettings.lite.hasOwnProperty('node')) {
          node = drupalSettings.lite.node;
        }
        if (drupalSettings.lite.hasOwnProperty('node')) {
          debug = drupalSettings.lite.debug;
        }
      }

      debug && console.log('User permissions: ' + permissions);

      // User can not toggle tracking based on main permission.
      if (permissions.indexOf("toggle") !== -1) {
        debug && console.log('User HAS main TOOGLE permission');
        can_toggle = true;
      }
      else {
        debug && console.log('User DO NOT have main TOOGLE permission');
      }

      // User can not accept or reject changes.
      if (permissions.indexOf("resolve") !== -1) {
        debug && console.log('User HAS main RESOLVE permission');
        can_resolve = true;
      }
      else {
        debug && console.log('User DO NOT have main RESOLVE permission');
      }

      // Text format permission.
      if (format) {
        key = '_' + format;

        if (!can_toggle && permissions.indexOf("toggle" + key) !== -1) {
          debug && console.log('User HAS format TOOGLE permission');
          can_toggle = true;
        }
        else {
          debug && console.log('User DO NOT have format TOOGLE permission');
        }
        if (!can_resolve && permissions.indexOf("resolve" + key) !== -1) {
          debug && console.log('User HAS format RESOLVE permission');
          can_resolve = true;
        }
        else {
          debug && console.log('User DO NOT have format RESOLVE permission');
        }
      }

      // Moderation permission.
      if (node) {
        if (node.moderated) {
          key = '_' + node.workflow + '_' + node.state;
          var label = node.workflow + ': ' + node.state;

          if (!can_toggle && permissions.indexOf("toggle" + key) !== -1) {
            debug && console.log('User HAS moderation ' + label + ' TOOGLE permission');
            can_toggle = true;
          }
          else {
            debug && console.log('User DO NOT have moderation ' + label + ' TOOGLE permission');
          }
          if (!can_resolve && permissions.indexOf("resolve" + key) !== -1) {
            debug && console.log('User HAS moderation ' + label + ' RESOLVE permission');
            can_resolve = true;
          }
          else {
            debug && console.log('User DO NOT have moderation ' + label + ' RESOLVE permission');
          }
        }
      }

      // Add toggle button to be removed.
      if (!can_toggle) {
        remove_buttons += 'lite-toggletracking,';
      }
      // Add accept and reject buttons to be removed.
      if (!can_resolve) {
        remove_buttons += 'lite-acceptall,lite-rejectall,lite-acceptone,lite-rejectone';
        // Disable contextual menu options.
        editor.removeMenuItem('lite-acceptone');
        editor.removeMenuItem('lite-rejectone');
      }
      // Remove toolbar buttons dynamically on the editor config.
      editor.config.removeButtons = remove_buttons;

    }

  });

})(jQuery, Drupal, drupalSettings, CKEDITOR);
