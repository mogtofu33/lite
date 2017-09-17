/**
 * @file
 * Some basic behaviors and utility functions for Linkit.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.lite = Drupal.lite || {};

  var datePicker = $.datepicker.setDefaults($.datepicker.regional[drupalSettings.path.currentLanguage]);

  /**
   * Process ins and del produced by Lite plugin in the editor.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches lite behaviour to the Lite markup.
   */
  Drupal.behaviors.lite = {
    attach: function (context, settings) {

      // Check opentip is here.
      if (typeof Opentip === "undefined") {
        return;
      }

      // Prepare tooltip template.
      var tooltipTemplate = drupalSettings.lite.tooltipTemplate.replace(/\%/g, '@');

      $('.ice-ins, .ice-del', context).once('liteViewProcessed').each(function () {
        var $change = $(this);
        var title = Drupal.lite.makeTooltipTitle(tooltipTemplate, $change);
        new Opentip($change, title);
      });

    }
  };

  /**
   * Lite helpers functions adapatted from Lite plugin.
   *
   * These functions are just Drupal rewriten function of lite:
   * https://github.com/loopindex/ckeditor-track-changes/blob/master/src/lite/plugin.js.
   *
   * @namespace
   */
  Drupal.lite = {

    /**
     * Create tooltip title replicating what's done in Lite plugin.js.
     *
     * Src/lite/plugin.js#L1823
     *
     * @param {object} datePicker
     *   The Jquery UI Datepicker object.
     * @param {string} title
     *   The title template fromt Lite settings.
     * @param {jQuery} $elem
     *   A jQuery element containing Lite tag ins or del.
     *
     * @return {string}
     *   The message processed through translation.
     */
    makeTooltipTitle: function (title, $elem) {
      // Build base object based on element data.
      var change = {
        'type': $elem.prop("tagName"),
        'time': parseInt($elem.attr('data-time')),
        'lastTime': parseInt($elem.attr('data-last-change-time')),
        'userName': $elem.attr('data-username')
      };

      // Prepare variable to replace in text.
      var time = new Date(change.time),
        lastTime = new Date(change.lastTime),
        params = {
          '@a': ("INS" === change.type) ? Drupal.t('added') : Drupal.t('deleted'),
          '@t': Drupal.lite.relativeDateFormat(time),
          '@u': change.userName,
          '@dd': datePicker.formatDate("d", time),
          '@d': time.getDate(),
          '@mm': datePicker.formatDate("mm", time),
          '@m': time.getMonth() + 1,
          '@yy': datePicker.formatDate("y", time),
          '@y': time.getFullYear(),
          '@nn': Drupal.lite.padNumber(time.getMinutes(), 2),
          '@n': time.getMinutes(),
          '@hh': Drupal.lite.padNumber(time.getHours(), 2),
          '@h': time.getHours(),
          '@T': Drupal.lite.relativeDateFormat(lastTime),
          '@DD': datePicker.formatDate("d", lastTime),
          '@D': lastTime.getDate(),
          '@MM': datePicker.formatDate("mm", lastTime),
          '@M': lastTime.getMonth() + 1,
          '@YY': datePicker.formatDate("y", lastTime),
          '@Y': lastTime.getFullYear(),
          '@NN': Drupal.lite.padNumber(lastTime.getMinutes(), 2),
          '@N': lastTime.getMinutes(),
          '@HH': Drupal.lite.padNumber(lastTime.getHours(), 2),
          '@H': lastTime.getHours(),
        };

      // Build text message using params replacment.
      return Drupal.t(title, params);
    },

    /**
     * Transform date to a relative format. From src/lite/plugin.js#L258.
     *
     * @param {object} date
     *   The date object.
     *
     * @return {string}
     *   The relative format string.
     */
    relativeDateFormat: function (date) {
      var now = new Date(),
        today = now.getDate(),
        month = now.getMonth(),
        year = now.getFullYear(),
        minutes;

      var t = typeof date;

      if (t === "string" || t === "number") {
        date = new Date(date);
      }

      // Today.
      if (today == date.getDate() && month == date.getMonth() && year == date.getFullYear()) {
        minutes = Math.floor((now.getTime() - date.getTime()) / 60000);

        if (minutes < 1) {
          return Drupal.t('now');
        } else if (minutes < 60) {
          return Drupal.formatPlural(minutes, '1 minute ago', '@count minutes ago');
        } else {
          return Drupal.t('on') + " " + Drupal.lite.padNumber(date.getHours(), 2) + ":" + Drupal.lite.padNumber(date.getMinutes(), 2);
        }
      // This year.
      } else if (year == date.getFullYear()) {
        // We remove year in the result.
        return Drupal.t('on') + " " + datePicker.formatDate(drupalSettings.lite.tLocale.replace(/y/g, ''), date);
      } else {
        return Drupal.t('on') + " " + datePicker.formatDate(drupalSettings.lite.tLocale, date);
      }
    },

    /**
     * Wrapper helper to pad number.
     *
     * @param {string} s
     *   The string to process.
     * @param {integer} length
     *   The pad offset.
     *
     * @return {string}
     *   The padded number.
     */
    padNumber: function (s, length) {
      return Drupal.lite.padString(s, length, '0');
    },

    /**
     * Pad a string.
     *
     * @param {string} s
     *   The string to process.
     * @param {integer} length
     *   The pad offset.
     * @param {integer} padWith
     *   The pad width.
     *
     * @return {string}
     *   The padded string.
     */
    padString: function (s, length, padWith) {
      if (null === s || (typeof(s) === "undefined")) {
        s = "";
      } else {
        s = String(s);
      }
      padWith = String(padWith);
      var padLength = padWith.length;
      for (var i = s.length; i < length; i += padLength) {
        s = padWith + s;
      }
      return s;
    }

  };

})(jQuery, Drupal, drupalSettings);
