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
    		get_summary_data: function (page_id, params) {
    			return apis.page.func(page_id, "get_summary_data", params);
    		}
    	}
    }

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
	$scope.currentPage = 2;

	var update_data = function (params) {
		params = params ? params : {};
		params['pageNumber'] = $scope.currentPage;
		params['perPage'] = $scope.perPage;

		$scope.loadingPromise = ckAPI.page.get_summary_data($scope.pageId, params).then(function (data) {
			$scope.schema = data.schema;
			$scope.rows = data.data;
			$scope.rowCount = data.count;
		});
	};

	$scope.pageChanged = function () {
		update_data ();
	};

	$scope.itemLink = function (val) {
		return ckUrl.resetGetParams ({
			action: "page_function",
			func: "edit_item",
			item_id: val,
			page: $scope.pageId
		})
	};

	update_data();
});

app.controller("CKFormController", function ($scope) {
    $scope.formItems = [];
});
