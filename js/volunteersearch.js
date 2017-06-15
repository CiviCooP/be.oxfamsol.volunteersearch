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
            'need_id': Search.need_id,
            'options': {
                'limit': Search.resultsPerPage,
                'offset': 0
            }
        };
        Search.params = _.extend(defaults, Search.params);

        var defer = CRM.$.Deferred();
        CRM.api3('Contact', 'volunteersearch', Search.params, {
            success: function(data) {
                var end = Search.params.options.offset + Search.params.options.limit;
                var start = Search.params.options.offset + 1;

                if (end > data.metadata.total_found) {
                    end = data.metadata.total_found;
                }

                Search.pagerData.set({
                    'end': end,
                    'start': start,
                    'total': data.metadata.total_found
                });

                defer.resolve(_.toArray(data.values));
            }
        });
        return defer.promise();
    };
});