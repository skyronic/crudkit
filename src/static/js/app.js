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
	'ui.bootstrap',
	'cgBusy',
	'angular.filter',
    'kendo.directives'
]);

var GenerateAPIFactory = function (make_call_real) {
    var make_call = function (url, params, urlOnly) {
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
    		get_colSpec: function (page_id) {
    			return apis.page.func(page_id, "get_colSpec", {});
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
            }
    	}
    };

    return apis;
};

app.factory ("ckAPI", function ($http, $q) {
    var make_call_real = function (url, params) {
        var deferred = $q.defer();

        $http.post(url, params).error(function (data) {
            console.error("XHR Failed!!");
            deferred.reject($q.reject(data));
        }).success(function (data) {
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

app.controller("SummaryTableController", function ($scope, ckAPI) {
	$scope.pageId = window.pageId;
	$scope.perPage = 10;
	$scope.currentPage = 1;

	var update_data = function (params) {
		params = params ? params : {};
		params['pageNumber'] = $scope.currentPage;
		params['perPage'] = $scope.perPage;
        $scope.loadingPromise = ckAPI.page.get_data($scope.pageId, params).then(function (data) {
            $scope.rows = data.rows;
        });
	};

    $scope.loadingPromise = ckAPI.page.get_colSpec($scope.pageId).then(function (colSpec) {
        $scope.rowCount = colSpec.count;

        $scope.columns = _.map(colSpec.columns, function (val) {
            return _.extend(val, colSpec.schema[val.key]);
        });


        update_data ();
    });

	$scope.pageChanged = function () {
		update_data ();
	};

	$scope.itemLink = function (row, col) {
		return ckUrl.resetGetParams ({
			action: "page_function",
			func: "edit_item",
			item_id: row[col.primaryColumn],
			page: $scope.pageId
		})
	};
});

app.controller("CKFormController", function ($scope, $http, ckAPI) {
    $scope.formItems = {};
    $scope.loadingPromise = null;
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

    var activate_edit = function () {
        $scope.loadingPromise = ckAPI.page.get_form_values(formConfig.pageId, formConfig.itemId).then(function(data) {
            $scope.formItems = data.values;
        });
        activate_relationships();
    };

    var activate_new = function () {
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
        var vals = $scope.formItems;
        if(formConfig.newItem) {
            $scope.loadingPromise = ckAPI.page.create_item(formConfig.pageId, vals).then(function(data) {
                alert("Saved");
            });
        }
        else {
            $scope.loadingPromise = ckAPI.page.set_form_values(formConfig.pageId, formConfig.itemId, vals).then(function(data) {
                alert("Saved");
            });
        }
    };
});
