CRM.volunteerApp.module('Entities', function(Entities, volunteerApp, Backbone, Marionette, $, _) {
    Entities.getContacts = function() {
        var Search = CRM.volunteerApp.module('Search');
        var defaults = {
            'sequential': 1,
            'return': [
                'contact_id',
                'sort_name',
                'email',
                'phone',
                'city',
                'state_province'
            ],
            'options': {
                'limit': Search.resultsPerPage,
                'offset': 0
            }
        };
        Search.params = _.extend(defaults, Search.params);

        var defer = CRM.$.Deferred();
        CRM.api3('Contact2', 'get', Search.params, {
            success: function(data) {
                Entities.getContactCount().done(function(cnt) {
                    var end = Search.params.options.offset + Search.params.options.limit;
                    var start = Search.params.options.offset + 1;

                    if (end > cnt) {
                        end = cnt;
                    }

                    Search.pagerData.set({
                        'end': end,
                        'start': start,
                        'total': cnt
                    });

                    defer.resolve(_.toArray(data.values));
                });
            }
        });
        return defer.promise();
    };

    Entities.getContactCount = function() {
        var defaults = {
            'options': {
                'limit': 0
            }
        };
        params = _.extend(defaults, CRM.volunteerApp.module('Search').params);

        var defer = $.Deferred();
        CRM.api3('Contact2', 'getcount', params, {
            success: function(data) {
                defer.resolve(data.result);
            }
        });
        return defer.promise();
    };
});