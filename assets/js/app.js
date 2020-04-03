angular.module('todoApp', ['ui.select', 'ngSanitize'])
    .controller('TodoListController', function ($scope, $http, $httpParamSerializerJQLike, flash) {
        const vm = this;
        const URL = `${backend_data.siteUrl}/wp-admin/admin-ajax.php`;
        $scope.loading = false;
        $scope.showComment = false;
        vm.itemArray = []
        $scope.comments = []
        $scope.userMocked = {}

        $scope.sources = [
            {
                name: 'Productos',
                value: 'products'
            },
            {
                name: 'Médicos',
                value: 'doctors'
            }
        ]

        $scope.handleComment = () => {
            $scope.showComment = !$scope.showComment;
            vm.getComments()
        }

        $scope.selectSource = (a) => {
            $scope.sources.map(b => {
                b.select = b.value === a.value;
            })
        }

        vm.comment = null;

        vm.selectItem = {};

        /**
         * Get Data
         */

        $scope.parseSource = () => {
            let src = [];
            $scope.sources.map(a => {
                if (a.select) {
                    src.push(a.value)
                }
            })

            return src.join(',');
        }

        vm.search = (src = '') => {
            if (src.length > 2) {
                $http.get(`${URL}?action=get_source_tag&src=${src}&sources=${$scope.parseSource()}`)
                    .then(function (response) {
                        vm.itemArray = [...response.data]
                    });
            }
        }

        $scope.resetForm = function () {
            vm.selectItem.selected = undefined;
            vm.comment = null;
            vm.starts.selected = undefined;
            vm.mockedUser()
        }

        vm.mockedUser = () => {
            $http.get(`https://randomuser.me/api?nat=es`)
                .then(response => {
                    const {data} = response
                    $scope.userMocked = data.results.find(e => true);
                    console.log($scope.userMocked)
                })
        }

        vm.getComments = () => {
            $http.get(`${URL}?action=get_comments`)
                .then(response => {
                    vm.comments = [...response.data]
                    console.log(vm.comments)
                })
        }

        vm.starts = [
            {name: '⭐', vote: 1},
            {name: '⭐⭐', vote: 2},
            {name: '⭐⭐⭐', vote: 3},
            {name: '⭐⭐⭐⭐', vote: 4},
            {name: '⭐⭐⭐⭐⭐', vote: 5},

        ];
        vm.mockedUser();

        $scope.send = () => {
            $scope.loading = true;
            $http({
                method: 'POST',
                data: $httpParamSerializerJQLike({
                    action: 'other_comment_data',
                    comment: vm.comment,
                    id_source: vm.comment,
                    select: vm.selectItem.selected,
                    vote: vm.starts.selected.vote,
                    user_mocked: $scope.userMocked
                }),
                url: `${URL}`,
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).then(function successCallback(response) {
                flash.pop({title: 'Exito', body: 'Se guardo correctamente', type: 'success'})
                $scope.resetForm();
                $scope.loading = false;
            }, function errorCallback(response) {
                flash.pop({title: 'Error', body: '¡Algo ocurrio!', type: 'error'})
                $scope.loading = false;
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
        }

        // $scope.search('prod', ['products']);

        // $scope.selected = {value: $scope.itemArray[0]};
    }).factory("flash", function ($rootScope) {

    return {

        pop: function (message) {
            switch (message.type) {
                case 'success':
                    toastr.success(message.body, message.title);
                    break;
                case 'info':
                    toastr.info(message.body, message.title);
                    break;
                case 'warning':
                    toastr.warning(message.body, message.title);
                    break;
                case 'error':
                    toastr.error(message.body, message.title);
                    break;
            }
        }
    };
});