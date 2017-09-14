/**
 * @file
 * Some basic behaviors and utility functions for Linkit.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  var months = [
    Drupal.t("Jan"),
    Drupal.t("Feb"),
    Drupal.t("Mar"),
    Drupal.t("Apr"),
    Drupal.t("May"),
    Drupal.t("Jun"),
    Drupal.t("Jul"),
    Drupal.t("Aug"),
    Drupal.t("Sep"),
    Drupal.t("Oct"),
    Drupal.t("Nov"),
    Drupal.t("Dec"),
  ];

  /**
   * Drupal behavior to handle imce linkit integration.
   */
  Drupal.behaviors.lite = {
    attach: function (context, settings) {
      var tooltipTemplate = drupalSettings.lite.tooltipTemplate.replace(/\%/g, '@');
      $('.ice-ins, .ice-del', context).once('liteViewProcessed').each(function () {
        new Opentip($(this), _makeTooltipTitle(tooltipTemplate, $(this)));
      });
    }
  };

  /**
 * @ignore
 * @param change
 * @returns {Boolean}
 */
 function _makeTooltipTitle($title, elem) {
    var change = {
      'time': parseInt(elem.attr('data-time')),
      'lastTime': parseInt(elem.attr('data-last-change-time')),
      'userName': elem.attr('data-username'),
      'type': elem.prop("tagName")
    };

        var time = new Date(change.time),
              lastTime = new Date(change.lastTime),
        $params = {
          '@a': ("INS" === change.type) ? Drupal.t('added') : Drupal.t('deleted'),
          '@t': relativeDateFormat(time),
          '@u': change.userName,
          '@dd': padNumber(time.getDate(), 2),
                '@d': time.getDate(),
                '@mm': padNumber(time.getMonth() + 1, 2),
                '@m': time.getMonth() + 1,
                '@yy': padNumber(time.getYear() - 100, 2),
                '@y': time.getFullYear(),
                '@nn': padNumber(time.getMinutes(), 2),
                '@n': time.getMinutes(),
                '@hh': padNumber(time.getHours(), 2),
                '@h': time.getHours(),
                '@T': relativeDateFormat(lastTime,),
                '@DD': padNumber(lastTime.getDate(), 2),
                '@D': lastTime.getDate(),
                '@MM': padNumber(lastTime.getMonth() + 1, 2),
                '@M': lastTime.getMonth() + 1,
                '@YY': padNumber(lastTime.getYear() - 100, 2),
                '@Y': lastTime.getFullYear(),
                '@NN': padNumber(lastTime.getMinutes(), 2),
                '@N': lastTime.getMinutes(),
                '@HH': padNumber(lastTime.getHours(), 2),
                '@H': lastTime.getHours(),
        };

        return Drupal.t($title, $params);
        }

    function relativeDateFormat(date) {
          var now = new Date(),
              today = now.getDate(),
              month = now.getMonth(),
              year = now.getFullYear(),
              minutes, hours;

          var t = typeof(date);

          if (t === "string" || t === "number") {
              date = new Date(date);
          }

          if (today == date.getDate() && month == date.getMonth() && year == date.getFullYear()) {
              minutes = Math.floor((now.getTime() - date.getTime()) / 60000);
              if (minutes < 1) {
                  return Drupal.t('now');
              }
              else if (minutes < 60) {
                  return (Drupal.formatPlural(minutes, '1 minute ago', '@minutes minutes ago'));
              }
              else {
                  hours = date.getHours();
                  minutes = date.getMinutes();
                  return Drupal.t('on') + " " + padNumber(hours, 2) + ":" + padNumber(minutes, 2, "0");
              }
          }
          else if (year == date.getFullYear()) {
              return Drupal.t('on') + " " + label_dates(date.getDate(), date.getMonth());
          }
          else {
              return Drupal.t('on') + " " + label_dates(date.getDate(), date.getMonth(), date.getFullYear());
          }
      }

    function label_dates(day, month, year) {
          if (typeof(year) != 'undefined') {
              year = ", " + year;
          }
          else {
              year = "";
          }
          return months[month] + " " + day + year;
      }

    function padNumber(s, length) {
          return padString(s, length, '0');
      }

    function padString(s, length, padWith, bSuffix) {
      if (null === s || (typeof(s) === "undefined")) {
        s = "";
      }
      else {
        s = String(s);
      }
      padWith = String(padWith);
      var padLength = padWith.length;
      for (var i = s.length; i < length; i += padLength) {
        if (bSuffix) {
          s += padWith;
        }
        else {
          s = padWith + s;
        }
      }
      return s;
    }

})(jQuery, Drupal, drupalSettings);
