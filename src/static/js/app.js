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
            get_foreign: function (page_id, key, params) {
                params = params ? params : {};
                params.foreign_key = key;
                return apis.page.func(page_id, "get_foreign", params);
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

    $scope.SupportRepId = 1;

    $scope.$watch('form_id', function(newVal, oldVal) {
        formConfig = window.ckValues[newVal];

        if(formConfig.getValuesUrl) {
            $scope.loadingPromise = $http.get(formConfig.getValuesUrl).success(function (result) {
                $scope.formItems = angular.extend($scope.formItems, result.values);
            })
        }

        if(formConfig.hasRelationships) {
            for(var i = 0; i < formConfig.relationships.length; i++) {
                var relItem = formConfig.relationships[i];

                var relKey = "" + relItem.key;

                // TODO: how will ckloader work with multiple loaders ?
                // TODO: do we need to do early binding here?
                $scope.loadingPromise = ckAPI.page.get_foreign(window.ckValues.pageId, relKey, {}).then(function (data) {
                    $scope.selectValues[relKey] = data.values;
                });
            }
        }
    });

    $scope.saveValues = function () {
        var vals = $scope.formItems;
        $scope.loadingPromise = $http.post(formConfig.setValuesUrl, {
            values_json: JSON.stringify(vals)
        }).success(function (result) {
        })
    };
});
