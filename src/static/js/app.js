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
	'ui.bootstrap',
	'cgBusy',
	'angular.filter'
]);

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
                    result = new Date(input);
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
                    result = input.toISOString().toString();
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
    $scope.advancedSearchHidden = true;
    $scope.searchTerm = "";

	var update_data = function (params) {
		params = params ? params : {};
		params['pageNumber'] = $scope.currentPage;
		params['perPage'] = $scope.perPage;
        params['filters_json'] = JSON.stringify(getFilters());
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

    var getFilters = function () {
        if($scope.searchTerm !== "") {
            return [
            {
                id: "_ck_all_summary",
                cmp: "like",
                val: $scope.searchTerm
            }
            ];
        }
    };

    $scope.filterColumns = function () {
        var params = {
            filters_json: JSON.stringify(getFilters())
        };
        $scope.loadingPromise = ckAPI.page.get_colSpec($scope.pageId, params).then(function (colSpec) {
            $scope.rowCount = colSpec.count;
            update_data ();
        });
    };

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
    $scope.openStatus = {};
    $scope.schema = {};
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
            $scope.schema = data.schema;
            $scope.formItems = ck.converters.standard_to_js(data.schema, data.values);
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
        var vals = ck.converters.js_to_standard($scope.schema, $scope.formItems);
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
