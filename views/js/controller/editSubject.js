/*global define,$*/
define([
    'layout/actions/binder',
    'ui/feedback',
    'i18n',
    'lodash'
], function (binder, feedback, __, _) {
    'use strict';
    binder.register('duplicateTestTaker', function (actionContext) {
        var uri = '/taoTestTaker/TestTaker/isValid?uri=' + actionContext.uri,
            action = this,
            duplicateAction = _.clone(action);

        duplicateAction.binding = 'duplicateNode';

        $.ajax({
            url: uri,
            dataType: 'json',
            success: function (response) {
                if (response === true) {
                    binder.exec(duplicateAction, actionContext);
                } else {
                    feedback().error(__('The source Test takers data is not filled in correctly.'));
                }
            }
        });
    });
});