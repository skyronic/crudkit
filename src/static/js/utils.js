var app = angular.module ("ckApp");

app.directive ('confirmButton', function () {
    return {
        scope: {
            'label': '@',
            'ckClass': '@',
            'activate': "&"
        },
        restrict: "AE",
        template: "<button class='btn' ng-click='buttonClicked()' ng-disabled='busy' ng-class='ckClass'>{{ label }}</button>",
        controller: function ($scope, $timeout) {
        $scope.busy = false;
            $scope.buttonClicked = function () {
                $scope.busy = true;
                $scope.originalLabel = $scope.label;
                $scope.label = "Working ...";
                if ($scope.activate) {
                    $scope.activate ().then (function () {
                        $scope.busy = false;
                        $scope.label = $scope.originalLabel;
                    });
                }
            }
        }
    }
});