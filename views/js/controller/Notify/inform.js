/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
define(['jquery', 'i18n', 'ui/feedback'
], function ($, __, feedback ) {
    'use strict';

    /**
     *
     * @param {jQueryElement} $master
     * @param {jQueryElement} $slave
     */
    var toggleHelper = function ($master, $slave) {
        $master.on('change', function () {
            if ($master.is(':checked')) {
                $slave.removeAttr('checked');
            }
        }).trigger('change');
    };

    return {
        start: function () {

            toggleHelper($('#actionType_2'), $('#actionType_1'));
            toggleHelper($('#actionType_1'), $('#actionType_2'));

            var $editor = $('#template');
            $('#actionType_0').on('change', function () {
                $editor.closest('div').toggle($('#actionType_0').is(':checked'));
            }).trigger('change');

            $editor.attr('data-html-editable-container', true);


            var $radio = $('[name="pwdControl"]');

            if (!$radio.is(':checked')) {
                $radio.filter(':last').attr('checked', true);
            }

            $radio.on('change', function (e) {
                $('#pwdLength').closest('div').toggle($(this).val() !== 'type_human');
            }).trigger('change');


            var data = $('.main-container').data(), feedbackType, plugin, response = data.messages;
            if (data.hashResult) {
                window.open(data.hashResult, 'results', 'menubar=no,location=no');
            }
            if (response) {
                for ( plugin in response) {
                    for (feedbackType in response[plugin].messages) {
                        if (response[plugin]['messages'][feedbackType]) {
                            feedback()[feedbackType](response[plugin]['messages'][feedbackType]);
                        }
                    }
                }
                feedback().error('asd2');
                feedback().success('asd');

            }

        }
    };
});
