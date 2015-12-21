var ck = window.ck = {

};


var ckUrl = {
    resetGetParams: function (params) {
        var url = new Url();
        url.query.clear();

        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                url.query[key] = params[key];
            }
        }

        return url.toString();
    },
    addGetParams: function (params) {
        var url = new Url();
        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                url.query[key] = params[key];
            }
        }
        return url.toString();
    }
};

var app = angular.module("ckApp", [
	'cgBusy',
	'angular.filter',
    'ui.bootstrap'
]);

ck.flashbag = {
    subscribers: {},
    add: function (items) {
        for(var category in items) {
            if(!items.hasOwnProperty(category))
                continue;
            var catItems = items[category];
            if(ck.flashbag.subscribers.hasOwnProperty(category)) {
                var subs = ck.flashbag.subscribers[category];
                for(var i = 0; i < subs.length; i++) {
                    subs[i] (catItems);
                }
            }
        }
    },
    subscribe: function (category, callback) {
        if(!ck.flashbag.subscribers.hasOwnProperty(category)) {
            ck.flashbag.subscribers[category] = [];
        }

        ck.flashbag.subscribers[category].push (callback);
    }
}

var GenerateAPIFactory = function (make_call_real) {
    var make_call = function (url, params, urlOnly) {
        url += "&ajax=true";
        if(urlOnly) {
            return url;
        }
        return make_call_real(url, params);
    };

    var apis = {
    	page: {
    		func: function (page_id, func_name, params) {
    			return make_call(ckUrl.resetGetParams({
    				page: page_id,
    				func: func_name,
    				action: "page_function"
    			}), params);
    		},
    		get_colSpec: function (page_id, params) {
                params = params ? params : {};
    			return apis.page.func(page_id, "get_colSpec", params);
    		},
            get_data: function (page_id, params) {
                return apis.page.func(page_id, "get_data", params);
            },
            get_foreign: function (page_id, item_id, key, params) {
                params = params ? params : {};
                params.foreign_key = key;
                params['item_id'] = item_id;
                return apis.page.func(page_id, "get_foreign", params);
            },
            get_form_values: function (page_id, item_id, params) {
                params = params ? params : {};
                params['item_id'] = item_id;

                return apis.page.func(page_id, "get_form_values", params);
            },
            set_form_values: function (page_id, item_id, values, params) {
                params = params ? params : {};
                params['item_id'] = item_id;
                params['values_json'] = JSON.stringify(values);

                return apis.page.func(page_id, "set_form_values", params);
            },
            create_item: function (page_id, values, params) {
                params = params ? params : {};
                params['values_json'] = JSON.stringify(values);

                return apis.page.func(page_id, "create_item", params);
            },
            delete_items: function (page_id, ids, params) {
                params = params ? params : {};
                params['delete_ids'] = JSON.stringify(ids);

                return apis.page.func(page_id, "delete_items", params);
            }
    	}
    };

    return apis;
};

ck.fatalError = function (title, message) {
    BootstrapDialog.show ({
        title: title,
        message: message,
        type: "type-danger",
        buttons: [{
            icon: "fa fa-reload",
            label: "Reload Page",
            action: function () {
                window.location.reload()
            }
        }],
        closable: false
    })
};

ck.converters = {
    standard_to_js: function (schema, object) {
        var resultObject = {};
        for(var key in schema) {
            if(!object.hasOwnProperty(key)) {
                continue;
            }
            var type = schema[key].type;
            var input = object[key];
            var result = null;

            switch(type) {
                case "string":
                    result = input;
                break;
                case "number":
                    result = parseFloat(input);
                break;
                case "datetime":
                    // Server sends a timestamp in UTC. We use that in UTC and convert to JS date
                    result = moment.unix(parseInt(input)).tz("UTC").toDate();
                break;
            }

            resultObject[key] = result;
        }

        return resultObject;
    },
    js_to_standard: function (schema, object) {
        var resultObject = {};
        for(var key in schema) {
            if(!object.hasOwnProperty(key)) {
                continue;
            }
            var type = schema[key].type;
            var input = object[key];
            var result = null;

            switch(type) {
                case "string":
                    result = input.toString();
                break;
                case "number":
                    result = input.toString();
                break;
                case "datetime":
                    result = moment(input).tz("UTC").unix();
                break;
            }
            resultObject[key] = result;
        }

        return resultObject;
    },
    standard_to_js_table: function (schema, data) {
        var len = data.length;
        var processed = [];
        for(var i = 0; i < len; i ++) {
            processed.push(ck.converters.standard_to_js(schema, data[i]));
        }
        return processed;
    }
}

app.factory ("ckAPI", function ($http, $q) {
    var make_call_real = function (url, params) {
        var deferred = $q.defer();

        $http.post(url, params).error(function (data) {
            console.error("XHR Failed!!", data);
            deferred.reject($q.reject(data));
            if(data.error) {
                ck.fatalError("Error", "<p>There was an error in the server.</p><p><b>" + data.error.type + "</b></p><p>" + data.error.message + "</p>");
            }
            else {
                ck.fatalError("Error", "There was an unknown error in the server");
            }
        }).success(function (data) {
            if(data.flashbag) {
                ck.flashbag.add(data.flashbag);
            }
            if(data.success === false) {
                deferred.reject("Unknown error. Conflicting success codes");
                return;
            }
            deferred.resolve(data);
        });
        return deferred.promise;
    };

    return GenerateAPIFactory(make_call_real);
});

app.controller("SummaryTableController", function ($scope, ckAPI, $q, $timeout) {
	var forceDataRefresh = false; // Flag to indicate whether we want to refresh the whole table data(advance filter dependency)

	$scope.pageId = window.pageId;
	$scope.perPage = window.ckValues.rowsPerPage;
	$scope.currentPage = 1;
    $scope.pageCount = 1;
    $scope.advancedSearchHidden = true;
    $scope.searchTerm = "";
    $scope.advFilterItems = [];
    $scope.schema = [];
    $scope.advFilterOptions = [];
    $scope.allSelectedFlag = false;
    $scope.primaryCol = '';
    $scope.selectedCount = 0;
    $scope.rows = [];

	var update_data = function (params) {
		params = params ? params : {};
		params['pageNumber'] = $scope.currentPage;
		params['perPage'] = $scope.perPage;
        params['filters_json'] = JSON.stringify(getFilters());
        $scope.loadingPromise = ckAPI.page.get_data($scope.pageId, params).then(function (data) {
            $scope.rows = data.rows;
            $scope.forceDeselect();
        });
	};

    $scope.startAdvancedSearch = function() {
        if($scope.advancedSearchHidden) {
            $scope.addConditionButtonClicked ();
            $scope.advancedSearchHidden = false;
        }
    };

    // First load
    $scope.loadingPromise = ckAPI.page.get_colSpec($scope.pageId).then(function (colSpec) {
        $scope.rowCount = colSpec.count;
        $scope.pageCount = colSpec/$scope.perPage;

        $scope.columns = _.map(colSpec.columns, function (val) {
            return _.extend(val, colSpec.schema[val.key]);
        });

        $scope.primaryCol = _.find (colSpec.schema, 'primaryFlag', true).key;

        $scope.schema = colSpec.schema;
        $scope.advFilterOptions = _.map(colSpec.schema, function (val, key) {
            return _.extend(val, {id: key});
        });

        update_data ();
    });

    $scope.deleteRows = function () {
        var selected_ids = _.chain($scope.rows).filter ('selectedFlag', true).pluck ($scope.primaryCol).map(function (val) {
            return parseInt(val);
        }).value ();
        return ckAPI.page.delete_items($scope.pageId, selected_ids).then (function () {
            update_data ();
            return $q.when (true);
        })
    };

    $scope.updateSelectedCount = function () {
        $scope.selectedCount = _.filter ($scope.rows, 'selectedFlag', true).length;
    };

    $scope.forceDeselect = function () {
        $scope.allSelectedFlag = false;
        // Use selectAll to manually copy the allSelectedFlag
        $scope.selectAll ();
    };

    $scope.selectAll = function () {
        _.each($scope.rows, function (val) {
            val.selectedFlag = $scope.allSelectedFlag;
        });
        $scope.updateSelectedCount();
    };

    $scope.isWritable = function () {
        return !!window.ckValues.writable;
    }





    $scope.filterColumnSelected = function (item) {
        var id = item.id;
        var type = $scope.schema[id].type;

        var availableCmp = [];
        var defCmpType; // default comparison type

        if(type === "string") {
            availableCmp = [
                {id:"eq", label:"Equals", inputType: "string"},
                {id:"sw", label:"Starts With", inputType: "string"},
                {id:"ew", label:"Ends With", inputType: "string"},
                {id:"contains", label:"Contains", inputType: "string"}
            ];
            defCmpType = availableCmp[0];
        }
        else if(type === "number") {
            availableCmp = [
                {id:"eq", label:"Equals", inputType: "number"},
                {id:"gt", label:">", inputType: "number"},
                {id:"gte", label:">=",inputType: "number"},
                {id:"lt", label:"<", inputType: "number"},
                {id:"lte", label:"<=", inputType: "number"}
            ];
            defCmpType = availableCmp[0];
        }

        item.availableCmp = availableCmp;
        item.cmp = defCmpType;
    };

    var getFilters = function () {
        var filters = [];

        if($scope.searchTerm !== "") {
            filters.push({
                id: "_ck_all_summary",
                type: "like",
                value: $scope.searchTerm
            })
        }
        for(var i = 0; i < $scope.advFilterItems.length; i ++) {
            var filterItem = $scope.advFilterItems[i];
            filters.push({
                id: filterItem.id,
                type: typeof filterItem.cmp === 'undefined' ? 'null' : filterItem.cmp.id,
                value: filterItem.value
            })
        }

        return filters;
    };

    // Empty the advance filter items array
    var clearAdvFilterItems = function() {
      $scope.advFilterItems = [];
    };

    // Initial state for the advace filter
    var initAdvFilterState = function() {
      $scope.advFilterItems.push ({
          id: 'null',
          type: 'null',
          value: ''
      });
    };

    // Clear the search term input
    var clearSearchTerm = function() {
      $scope.searchTerm = "";
    };

    // Check if entire table refres is needed.
    var needsDataRefresh = function() {
      // Returns true if:
      // `advFilterItems` is the default input box and if the comparator param is `null`
      // OR `searchTerm` is non-empty 
      return !($scope.advFilterItems.length === 1 && $scope.advFilterItems[0]['id'] === 'null') || $scope.searchTerm !== '';
    }

    $scope.addConditionButtonClicked = function () {
      initAdvFilterState();
    };

    $scope.filterColumns = function () {
        var params = {
            filters_json: JSON.stringify(getFilters())
        };
        $scope.loadingPromise = ckAPI.page.get_colSpec($scope.pageId, params).then(function (colSpec) {
            $scope.rowCount = colSpec.count;
            $scope.pageCount = colSpec/$scope.perPage;
            update_data ();

            forceDataRefresh = needsDataRefresh();
        });
    };

    // Disable the advanced search button
    // if either the filter key / condition is not selected
    // the value param here is not considered
    $scope.isAdvancedSearchBtnDisabled = function() {
      var filters = getFilters();

      var needsToBeDisabled = function(filter) {
        return ( filter.id === 'null' || filter.type === 'null' );
      };

      for(var i=0, length=filters.length; i<length; i++) {
        if(needsToBeDisabled(filters[i])) { 
          return true;
        }
      }

      return false;
    };


	$scope.pageChanged = function () {
		update_data ();
	};

	$scope.itemLink = function (row, col) {
		return ckUrl.resetGetParams ({
			action: "page_function",
			func: "view_item",
			item_id: row[col.primaryColumn],
			page: $scope.pageId
		})
	};

	// Reset advanced search filter and search term.
  $scope.resetAdvSearch = function() {
    clearAdvFilterItems();
    initAdvFilterState();
    clearSearchTerm();
    
    // Reload the whole table data
    if(forceDataRefresh) {
      $scope.filterColumns();
    }
  }
});

app.controller("CKFormController", function ($scope, ckAPI) {
    $scope.formItems = {};
    $scope.loadingPromise = null;
    $scope.openStatus = {};
    $scope.schema = {};
    $scope.dirtyFlag = false;
    var formConfig = {};

    $scope.selectValues = {

    };

    var activate_relationships = function () {
        var relId = formConfig.itemId ? formConfig.itemId : "new";
        if(formConfig.hasRelationships) {
            for(var i = 0; i < formConfig.relationships.length; i++) {
                (function (relItem) {
                    var relKey = "" + relItem.key;

                    // TODO: how will ckloader work with multiple loaders ?
                    $scope.loadingPromise = ckAPI.page.get_foreign(formConfig.pageId,relId, relKey, {}).then(function (data) {
                        if(relItem.type === "manyToOne")
                            $scope.selectValues[relKey] = data.values;
                        else if(relItem.type === "oneToMany")
                            $scope.formItems[relKey] = data.values;
                    });

                })(formConfig.relationships[i]);
            }
        }
    };

    $scope.changedValues = {};
    $scope.extraClasses = {};
    $scope.registerChange = function (key) {
        $scope.changedValues[key] = $scope.formItems[key];
        $scope.extraClasses[key] = "has-change";
        $scope.dirtyFlag = true;
    };

    var activate_edit = function () {
        $scope.loadingPromise = ckAPI.page.get_form_values(formConfig.pageId, formConfig.itemId).then(function(data) {
            $scope.schema = data.schema;
            $scope.formItems = ck.converters.standard_to_js(data.schema, data.values);
        });
        activate_relationships();
    };

    var activate_new = function () {
        $scope.loadingPromise = ckAPI.page.get_form_values(formConfig.pageId, "_ck_new").then(function(data) {
            $scope.schema = data.schema;
            $scope.formItems = ck.converters.standard_to_js(data.schema, data.values);
        });
        activate_relationships();
    };

    $scope.$watch('form_id', function(newVal, oldVal) {
        formConfig = window.ckValues[newVal];

        if(formConfig.newItem) {
            activate_new();
        }
        else {
            activate_edit();
        }
    });


    $scope.saveValues = function () {
        if (!$scope.dirtyFlag)
            return;
        var vals = ck.converters.js_to_standard($scope.schema, $scope.changedValues);
        if(formConfig.newItem) {
            $scope.loadingPromise = ckAPI.page.create_item(formConfig.pageId, vals).then(function(data) {
                if(!data.dataValid) {
                    $scope.failedValues = data.failedValues;
                }
            });
        }
        else {
            $scope.loadingPromise = ckAPI.page.set_form_values(formConfig.pageId, formConfig.itemId, vals).then(function(data) {
                if(!data.dataValid) {
                    $scope.failedValues = data.failedValues;
                }
                console.log($scope.failedValues)
            });
        }
    };
});

$(function () {
    // subscribe to push alerts
    ck.flashbag.subscribe("alert", function(items) {
        var alertContainer = $("#alertTarget");
        items.forEach(function (item) {
            // ugh this is disgusting. I know. Sorry.
            var $alert = $("<div/>", {'class':"alert alert-dismissable alert-" + item.extra});
            $alert.append($("<button/>", {
                'class': 'close',
                'text': '×'
            }).attr({
                'data-role': 'close'
            })).append($("<div/>", {
                text: item.message
            }));
            alertContainer.append($alert);
        })
    });

    ck.flashbag.subscribe("log", function(items) {
        items.forEach(function (item) {
            if(item.extra === "json") {
                var obj = JSON.parse(item.message);
                console.log("[SERVER] ", obj);
            }
            else {
                console.log("[SERVER] " + item.message);
            }
        })
    });

    ck.flashbag.add(window.ckValues.flashbag);
})
