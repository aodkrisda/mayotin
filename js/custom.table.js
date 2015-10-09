
angular.module('custom.table', [])

.controller('CustomTableCtrl', function ($scope, $timeout, ngTableParams, Auth, ngTableDefaults, $modal, $popover, $alert, Lookups, cfpLoadingBar, _,API_URL) {
    $scope.Lookups = Lookups;
    $scope._first_ = true;
    $scope.pkField = 'id';
    $scope.apiName = 'table';
    $scope.formID = 'custom.form.html';
    $scope.searchText = '';
    $scope.customFilters = {search:'searchText'};
    $scope.editingItem = null;
    $scope.requiredFilters = null;
    $scope._id_=false;

    this.getScope=function(){
    	return $scope;
    }
    $scope.currentScope=function(){
    	return $scope;
    }
   
    $scope.setAPI = function (str, formid, cfilter, crequired) {
        if(str) $scope.apiName = str;
        if (formid) $scope.formID = formid;
        if (cfilter) $scope.customFilters = cfilter;
        $scope.requiredFilters = crequired;

    }

    $scope.lastSearchText = '';
    $scope.clearFilter = function () {
        $scope.searchText = '';
        $scope.setFilter();
    }

    $scope._lastFilters = {};
    $scope.setFilter = function (refresh) {

        if ($scope.customFilters) {
            if ($scope.searchOption) $scope.searchOption = null;
            var s = {};
            _.each($scope.customFilters, function (v, k) {
            	var kv=$scope.$eval(v);

                if ((kv!=null) && (kv !== '')) {
                    s[k] = kv;
                }
            });
            var b=!_.isEqual($scope._lastFilters, s);

            if (b) {
    
            	
                if ($scope.searchText != undefined) {
                    if ($scope.lastSearchText != $scope.searchText) {
                        $scope.lastSearchText = $scope.searchText;
                    }
                }
                
             

                $scope._lastFilters = s;
                $scope.tableParams.filter(s);
                $scope.refresh();
                
            } else {
                if ($scope.tableParams.filter() !== $scope._lastFilters) {
                    $scope.tableParams.filter($scope._lastFilters);
                    if (Object.keys($scope._lastFilters).length == 0) {
                        $scope.refresh();
                    }
                } else {
                    if (refresh) $scope.refresh();
                }
            }
        }
    }

    $scope.isSearchMode = function () {
        return ($scope.searchOption && ($scope.tableParams.filter() === $scope.searchOption));
    }
    $scope._searchPanel = null;
    $scope.advancedSearch = function (tpl) {
        if (tpl) {
            if (!$scope._searchPanel) {
                $scope._searchPanel = $modal({ scope: $scope, title: '', backdrop: 'static', template: tpl, placement: "top", html: true, show: false });
            }
            $scope._searchPanel.$promise.then($scope._searchPanel.show);
        }
    }
    $scope.$on('modal.show', function(scope,elm) {
       var a=angular.element(elm.$element[0]).find('input[autofocus]:first');
       if(a.length) try{a[0].focus();}catch(err){}

     });

    $scope.searchOption = null;
    $scope.startSearch = function (option) {
        $scope.searchOption = {search: option };
        $scope.tableParams.filter($scope.searchOption);
    }
    $scope.newItem = function (o) {
        $scope.$saving=false;
        $scope.editingItem = {};
        if(o){
        	_.extend($scope.editingItem,o);
        }
        createForm();
        formModal.$scope.title = 'เพิ่มข้อมูลใหม่';
        formModal.$promise.then(formModal.show);
    }

    $scope.startIdx = 0;
    var formModal = null;
    function createForm() {
        if (!formModal) formModal = $modal({ scope: $scope, title: '', backdrop: 'static', template: $scope.formID, placement: "top", html: true, show: false });
        return formModal;
    }
    $scope.editItem = function () {
        if ($scope.$it) {
            var d = {};
            d[$scope.pkField] = $scope.$it[$scope.pkField];
            Auth.post($scope.apiName + '/get', d).success(function (data) {
                $scope.editingItem = data.data;
                createForm();
                formModal.$scope.title = 'แก้ไขข้อมูล';
                formModal.$promise.then(formModal.show);
            });
        }
    }
    $scope.cancelForm = function () {
        formModal.$promise.then(formModal.hide);
    	$timeout(function(){
            $scope.editingItem = null;
        	},800);        
    }
    $scope.saveForm = function (_close) {
    	if(_close==undefined) _close=true;
        if ($scope.editingItem) {
            $scope.$saving = true;
            var act = ($scope.editingItem[$scope.pkField]) ? 'update' : 'add';
            if($scope._id_){
            	if($scope.editingItem['_id_']){
            		act='update';
            	}else{
            		act='add';
            	}
            }
            var tm={};
            angular.forEach($scope.editingItem, function(a,b){
            	if(angular.isString(a)){
            		tm[b]=a.trim();
            	}else{
            		tm[b]=a;
            	}
            });

            return Auth.post($scope.apiName + '/' + act, tm).success(function (data) {
                
                if (data.data && data.data[$scope.pkField]) {
                    if (act == 'add') {
                        $scope.tableParams.data.unshift(data.data); 
                        var n= parseInt($scope.tableParams.total());
                        $scope.tableParams.total(n+1);                        
                        if(!_close){
                        	$scope.editingItem = data.data;
                        };                        
                    } else {
                        var i = -1;
                        var n = $scope.tableParams.data.length;
                        var pk=data.data[$scope.pkField];
                        if(data.data['_id_']){
                        	pk=data.data['_id_'];
                        	delete data.data['_id_'];
                        }
                        for (var j = 0; j < n; j++) {
                            if ($scope.tableParams.data[j][$scope.pkField] == pk) {
                                angular.forEach(data.data, function (v, k) {
                                    if ($scope.tableParams.data[j][k] !== undefined) {
                                        $scope.tableParams.data[j][k] = v;
                                    }
                                });
                                break;
                            }

                        }
                    }
                }
      
                $scope.$saving = false;
                
                if(_close){
                	$timeout(function(){
                		$scope.editingItem = null;
                	},800);
                	formModal.$promise.then(formModal.hide);
                }
        
            }).error(function () {

                $scope.$saving = false;

            });
        }
    }
    this.callEditItem=function(it){
    	if(it){
	    	$scope.$it=it;
	    	$scope.editItem();
    	}
    }    
    this.callRemoveItem=function(it){
    	if(it){
    		$scope.$it=it;
	    	$scope.doRemove();
    	}
    }
    this.callUpdateItem=function(it){
    	if(it){
	    	$scope.editingItem=it;
	    	$scope.saveForm(false);
    	}
    }
    
    $scope.doRemove = function (conf) {
        if ($scope.$it && $scope.$it[$scope.pkField]) {
            var d = {};
            var row = $scope.it;
            d[$scope.pkField] = $scope.$it[$scope.pkField];
            Auth.post($scope.apiName + '/delete/' + ((conf===true)?'1':''), d).success(function (data) {
            		if (data.data && data.confirm!==undefined){
            				if(data.confirm===true){
            					$scope.pleaseConfirmDelete($scope.doRemove);
            				}else{
            					$scope.pleaseConfirmDelete2($scope.doRemove);
            				}
            		}else if (data.data && data.data[$scope.pkField] == d[$scope.pkField]) {
                        var i = -1;
                        var n = $scope.tableParams.data.length;
                        for (var j = 0; j < n; j++) {
                            if ($scope.tableParams.data[j][$scope.pkField] == d[$scope.pkField]) {
                                i = j;
                                var n= parseInt($scope.tableParams.total());
                                $scope.tableParams.total(n-1);                                
                                break;
                            }
                        }
                        if (i >= 0) {
                            $scope.tableParams.data.splice(i, 1);
                        }
                        $scope.editingItem = null;
                    }
                    

            });
        }
    }
    $scope.confirm_delete=function(){
    	if(angular.isFunction($scope.pleaseConfirmDeleteHlr)){
    		$scope.pleaseConfirmDeleteHlr(true);
    		$scope.pleaseConfirmDeleteHlr=null;
    	}
    }
    $scope.pleaseConfirmDeleteHlr=null;
    $scope.pleaseConfirmDelete=function(hlr){
        if (!$scope._confirmPanel) {
            $scope._confirmPanel = $modal({ scope: $scope, title: '', backdrop: 'static', template: 'custom.confirm.delete.html', placement: "top", html: true, show: false });
        }
        $scope.pleaseConfirmDeleteHlr=hlr;
        $scope._confirmPanel.$promise.then($scope._confirmPanel.show);
    }
    $scope.pleaseConfirmDelete2=function(hlr){
        if (!$scope._confirmPanel) {
            $scope._confirmPanel = $modal({ scope: $scope, title: '', backdrop: 'static', template: 'custom.confirm.delete2.html', placement: "top", html: true, show: false });
        }
        $scope._confirmPanel.$promise.then($scope._confirmPanel.show);
    }    
    $scope.removeChecked = function () {
        if (!$scope.confirmPopover) {
            $scope.confirmPopover = $popover(angular.element('#checked-actions'), { scope: $scope, autoClose:true,trigger:'manual', template: "custom.confirm2.popover.html", show: false });
        }
        $scope.confirmPopover.$promise.then($scope.confirmPopover.show)

    }
    $scope.doRemoveChecked = function (conf) {
        var ids = [];
        angular.forEach($scope.checkboxes.items, function (v, k) {
            if (v) ids.push(k);
        });
       
        if (ids.length) {
            var d = {};
            d[$scope.pkField] = ids;
            Auth.post($scope.apiName + '/delete/' + ((conf)?'1':''), d).success(function (data) {
            		if(data && data.confirm!==undefined){
            			if(data.confirm===true){
            				$scope.pleaseConfirmDelete($scope.doRemoveChecked);
            			}else{
            				$scope.pleaseConfirmDelete2($scope.doRemoveChecked);
            			}
            			return;
            		}
                    if (angular.isArray(data.data[$scope.pkField])) {
                        var i = 0;
                        var n = data.data[$scope.pkField].length;
                        d = {};
                        for (i = 0; i < n; i++) {
                            d[data.data[$scope.pkField][i]] = true;
                        }
   
                        var idxs = [];
                        n=$scope.tableParams.data.length;
                        for (var j = 0; j < n; j++) {
                            if (d[$scope.tableParams.data[j][$scope.pkField]]) {
                                idxs.push(j);
                            }
                        }
                        if (idxs.length) {
                            for (i = idxs.length - 1; i >= 0; i--) {
                                $scope.tableParams.data.splice(idxs[i], 1);
                            }
                            var n= parseInt($scope.tableParams.total());
                            $scope.tableParams.total(n-idxs.length);
                        }
                        idxs = null;
                      
                    }
                    $scope.editingItem = null;

            });
        }
    }
    $scope.refresh = function () {
        $scope.tableParams.reload();
    }
    $scope.hasChecked = function () {
        return $scope.checkboxes.checked;
    }
    $scope.hasSelected = function () {
        if ($scope.$it) {
            var checked = 0, total = $scope.tableParams.data.length;
            for (var i = 0; i < total; i++) {
                var item = $scope.tableParams.data[i];
                if (item[$scope.pkField] == $scope.$it[$scope.pkField]) {
                    checked = 1;
                    return true;
                }
            }
        }
        return false;
    }
    $scope.isSelected = function (it) {
        return ($scope.$it == it);
    }
    $scope.selectAll = function () {
        if (angular.isDefined($scope.pkField)) {
            angular.forEach($scope.tableParams.data, function (item) {

                $scope.checkboxes.items[item[$scope.pkField]] = true;
            });
        }
    }
    $scope.selectNone = function () {
        if (angular.isDefined($scope.pkField)) {
            angular.forEach($scope.tableParams.data, function (item) {

                $scope.checkboxes.items[item[$scope.pkField]] = false;
            });
        }
    }
    $scope.selectInverse = function () {
        if (angular.isDefined($scope.pkField)) {
            angular.forEach($scope.tableParams.data, function (item) {

                $scope.checkboxes.items[item[$scope.pkField]] = !$scope.checkboxes.items[item[$scope.pkField]];
            });
        }
    }
    $scope.selectRow = function (it) {
        if ($scope.$it) $scope.$it.$selected = false;
        it.$selected = true;
        $scope.$it = it;
    }

    $scope.tableParams = new ngTableParams({
        page: 1,            // show first page
        count: 25,          // count per page
        sorting: {
            //name: 'asc'     // initial sorting
        }
    }, {
        total: 0,           // length of data
        getData: function ($defer, params) {

            if ($scope._first_) {
                delete $scope._first_;
                return;
            }


            var b = true;
            if(!($scope.searchOption && ($scope.tableParams.filter() === $scope.searchOption))){
                if ($scope.requiredFilters) {
                    var f = params.filter();
                    _.each($scope.requiredFilters, function (v, k) {
                        if (v && (f[k] == undefined)) {
                            b = false;
                        }
                    });
                    if (!b) {
                        params.total(0);
                        $defer.resolve([]);

                    }
                }
            }

            if (params.$params.sorting) {
                var key = Object.keys(params.$params.sorting)[0];
                key = key + ':' + params.$params.sorting[key];
                if (key != $scope._sortfield_) {
                    $scope._sortfield_ = key;
                    if (params.$params['page'] != 1) {
                        $scope.tableParams.page(1);
                        return;
                    }
                }
            }


            // ajax request to api
            cfpLoadingBar.start();
            Auth.post($scope.apiName, params.$params).success(function (data) {

                    // update table params
            		if(data.meta!==undefined){
            			$scope.TableMeta=data.meta;
            		}else{
            			$scope.TableMeta=null;
            		}
                    params.total(data.total);
                    params.page(data.page);
                    // set new data
                    $scope.startIdx = (data.page - 1) * params.$params.count;
                   
                    var keys = {};
                    var sel = null;
                    var n = data.data.length;
                    var b=false;
                    for (var i = 0; i < n; i++) {
                        var it = data.data[i];
                        keys[it[$scope.pkField]]=$scope.checkboxes.items[it[$scope.pkField]] || false;
                        b = b || keys[it[$scope.pkField]];
                        if ($scope.$it) {
                            if ($scope.$it[$scope.pkField] == it[$scope.pkField]) {
                                sel = it;
                            }
                        }
                    }
                    if ($scope.$it !== sel) {
                        $scope.$it=sel;
                    }
                    $scope.checkboxes.checked=b;
                    $scope.checkboxes.items=keys;
                    $defer.resolve(data.data);

                    cfpLoadingBar.complete();
                    $scope.tableParams.ready = true;
            

            });
        }
    });

    $scope.statusChecked=function(status){
        var ids = [];
        angular.forEach($scope.checkboxes.items, function (v, k) {
            if (v) ids.push(k);
        });
        if (ids.length) {
            var d = {};
            d[$scope.pkField] = ids;
            d['active']=status;
            Auth.post($scope.apiName + '/status', d).success(function (data) {
            	if(data && data.data) $scope.refresh();
            });
        }
    }
    $scope.checkboxes = { checked: false, items: {} };

    // watch for data checkboxes
    $scope.$watch('checkboxes.items', function (values) {

        if (!$scope.tableParams.data) {
            return;
        }

        var checked = 0, total = $scope.tableParams.data.length;
        for (var i = 0; i < total; i++) {
            var item = $scope.tableParams.data[i];
            if ($scope.checkboxes.items[item[$scope.pkField]]) {

                checked = 1;

                break;
            }

        }
        $scope.checkboxes.checked = (checked > 0);
    }, true);

    $timeout(function () {
        $scope.setFilter(true);
    },100)
});
