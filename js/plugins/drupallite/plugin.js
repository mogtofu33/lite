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

  var $use_cases = ['toggle', 'resolve'];
  var $user_can = {'toggle': false, 'resolve': false};

  CKEDITOR.plugins.add('drupallite', {

    // We are checking after init to be able to remove contextual menu items.
    afterInit: function (editor) {

      var config = editor.config.lite.options,
          permissions = editor.config.lite.permissions,
          extra_permissions = editor.config.lite.extra_permissions,
          format = null,
          node = null,
          key = '',
          remove_buttons = '',
          debug = true;

      if (editor.config.hasOwnProperty('drupal')) {
        format = editor.config.drupal.format;
      }

      // Main Lite settings from configuration or pre_render on element.
      if (drupalSettings.hasOwnProperty('lite')) {
        if (drupalSettings.lite.hasOwnProperty('node')) {
          node = drupalSettings.lite.node;
        }
        if (drupalSettings.lite.hasOwnProperty('debug')) {
          // Debug = drupalSettings.lite.debug;.
        }
        debug && console.log(drupalSettings.lite);
      }

      debug && console.log(editor.config.lite);

      // Main permission check.
      checkPermission(key, permissions, $user_can, debug);

      // Text format permission.
      if (format && extra_permissions == 'permissions_by_formats') {
        debug && console.log('Check extra permissions for text format: ' + format);
        key = '_' + format;
        checkPermission(key, permissions, $user_can, debug);
      }
      // Moderation permission.
      else if (node && extra_permissions == 'permissions_by_states') {
        debug && console.log('Check extra permissions by states: ' + node.workflow + '_' + node.state);
        if (node.moderated) {
          key = '_' + node.workflow + '_' + node.state;
          checkPermission(key, permissions, $user_can, debug);
        }
        else {
          debug && console.log('Node not moderated.');
        }
      }

      // Add toggle button to be removed.
      if (!$user_can.toggle) {
        remove_buttons += 'lite-toggletracking,';
      }

      // Add accept and reject buttons to be removed.
      if (!$user_can.resolve) {
        remove_buttons += 'lite-acceptall,lite-rejectall,lite-acceptone,lite-rejectone';

        // Disable contextual menu options.
        // https://docs.ckeditor.com/#!/api/CKEDITOR.editor-method-removeMenuItem
        editor.removeMenuItem('lite-acceptone');
        editor.removeMenuItem('lite-rejectone');
      }

      // Remove toolbar buttons dynamically on the editor config.
      // https://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-removeButtons
      editor.config.removeButtons = remove_buttons;
    }

  });

  /**
   * Simple user permission check for the action key.
   *
   * @param string key
   *   The key to check.
   * @param array permissions
   *   The user permissions.
   * @param object $user_can
   *   The current user capability.
   * @param bool debug
   *   Flag to print log message in the console..
   */
  function checkPermission(key, permissions, $user_can, debug) {
    $use_cases.forEach(function (use_case) {
      if (!$user_can[use_case] && permissions.indexOf(use_case + key) !== -1) {
        debug && console.log(use_case + ' ' + key + ': GRANTED');
        $user_can[use_case] = true;
      }
      else {
        debug && console.log(use_case + ' ' + key + ': REFUSED');
      }
    });
  }

})(jQuery, Drupal, drupalSettings, CKEDITOR);
