'use strict';

// Declare app level module which depends on views, and components
angular.module('underscore', [])
.factory('_', ['$window',function ($window) {
    return $window._;
}])

.factory('moment', ['$window',function ($window) {
    return $window.moment;
}]);



angular.module('App', ['underscore', 'ui.router', 'angular-loading-bar', 'ngAnimate', 'mgcrea.ngStrap', 'ngTable', 'ngSanitize', 'custom.table','flow','angular-storage','chart.js','highcharts-ng'])
.constant('API_URL','rest/api.php/v1/')
.factory('myHttpInterceptor', ['$q', '$rootScope', function ($q, $rootScope) {
    return {
        'responseError': function (rejection) {
            $rootScope.detectResponseError(rejection);
            return $q.reject(rejection);
        }
    };
}])

.run(['$rootScope', '$state', '$stateParams', 'Auth', '$filter', '$alert','Lookups','Search','API_URL','store','$modal','_','$popover', function ($rootScope, $state, $stateParams, Auth, $filter, $alert,Lookups,Search,API_URL,store,$modal,_,$popover) {
    
    $rootScope._StoreData={};
    $rootScope.INC_USER=0;
    Auth.fetch();
    $rootScope.storeData=function($a,$b){
    	if($b==null){
    		delete $rootScope._StoreData[$a];
    	}else{
    		if(!$rootScope._StoreData)$rootScope._StoreData={};
    		$rootScope._StoreData[$a]=$b;
    	}
    	store.set('_StoreData',$rootScope._StoreData);
    }
    $rootScope.fetchData=function($a){
    	if($rootScope._StoreData){
    	return $rootScope._StoreData[$a];
    	}
    	return null;
    }       
    if(Auth.isLoggedIn){
    	
    	$rootScope.selectedMedia=store.get('selectedMedia');
    	$rootScope._StoreData=store.get('_StoreData');
    	$rootScope.teacherMedia=$rootScope.fetchData('teacherMedia');
    	
    	Lookups.fetch();
    	Lookups.load();
    }
    $rootScope.lookup_group = $filter('lookup_group');
    $rootScope.Lookups = Lookups;
    $rootScope.Search=Search;
    $rootScope.API_URL=API_URL;
    
    $.notify.defaults({ autoHideDelay: 3000, globalPosition: 'bottom right',  style: 'bootstrap'} );
    $rootScope.adminMenus = [
                       {href: "#user/profile", text: "<h4><i class=\"fa fa-user\"></i>&nbsp;ข้อมูลส่วนตัว</h4>"},
                       {href: "#user/search", text: "<h4><i class=\"fa fa-search\"></i>&nbsp;เรียนรู้สื่อ</h4>"},
                       {divider: true},                                
                       
                       {href: "#admin/group", text: "<h4><i class=\"fa fa-tags\"></i>&nbsp;ข้อมูลกลุ่มสาระ</h4>"},
                       {href: "#admin/level", text: "<h4><i class=\"fa fa-tags\"></i>&nbsp;ข้อมูลระดับชั้น</h4>"},
                       {href: "#admin/subject", text: "<h4><i class=\"fa fa-tags\"></i>&nbsp;ข้อมูลวิชาเรียน</h4>"},
                       {href: "#admin/evaluate_group", text: "<h4><i class=\"fa fa-tags\"></i>&nbsp;หัวข้อเรื่องที่ประเมิน</h4>"},
                       {href: "#admin/user", text: "<h4><i class=\"fa fa-user\"></i>&nbsp;ข้อมูลผู้ใช้</h4>"},
                       {href: "#admin/media", text: "<h4><i class=\"fa fa-video-camera\"></i>&nbsp;ข้อมูลสื่อ</h4>"},
                       
                       {href: "#user/history", text: "<h4><i class=\"fa fa-history\"></i>&nbsp;ประวัติการเข้าชม</h4>"},
                       {href: "#user/home", text: "<h4><i class=\"fa fa-home\"></i>&nbsp;หน้าแรก</h4>"},
                       {divider: true},                        
                       {href: "#logout", text: "<h4><i class=\"fa fa-sign-out\"></i>&nbsp; ออกจากระบบ</h4>"}
                     ];

    $rootScope.userMenus = [
                            {href: "#user/profile", text: "<h4><i class=\"fa fa-user\"></i>&nbsp;ข้อมูลส่วนตัว</h4>"},
                            {href: "#user/search",  xrole:"director,admin,teacher,student", text: "<h4><i class=\"fa fa-search\"></i>&nbsp;เรียนรู้สื่อ</h4>"},
                            {divider: true, role:"admin,director,teacher"}, 
                            {href: "#teacher/media", role:"teacher", text: "<h4><i class=\"fa fa-video-camera\"></i>&nbsp;จัดการข้อมูลสื่อ</h4>"},
                              
                             {href: "#director/report", role:"director", text: "<h4><i class=\"fa fa-bar-chart\"></i>&nbsp;รายงานการใช้สื่อ</h4>"},
                             { href: "#director/report2", role: "director", text: "<h4><i class=\"fa fa-bar-chart\"></i>&nbsp;รายงานเปรียบเทียบการใช้สื่อ</h4>" },

                             
                             {href: "#admin/group", role:"admin", text: "<h4><i class=\"fa fa-tags\"></i>&nbsp;ข้อมูลกลุ่มสาระ</h4>"},
                             {href: "#admin/level", role:"admin", text: "<h4><i class=\"fa fa-tags\"></i>&nbsp;ข้อมูลระดับชั้น</h4>"},
                             {href: "#admin/subject",role:"admin",  text: "<h4><i class=\"fa fa-tags\"></i>&nbsp;ข้อมูลวิชาเรียน</h4>"},
                             {href: "#admin/evaluate_group", role:"admin", text: "<h4><i class=\"fa fa-tags\"></i>&nbsp;หัวข้อเรื่องที่ประเมิน</h4>"},       
                             {href: "#admin/user", role:"admin", text: "<h4><i class=\"fa fa-user\"></i>&nbsp;ข้อมูลผู้ใช้</h4>"},
                             {href: "#admin/media", role:"admin", text: "<h4><i class=\"fa fa-video-camera\"></i>&nbsp;ข้อมูลสื่อ</h4>"},
                             {href: "#user/search",  role:"manager", text: "<h4><i class=\"fa fa-search\"></i>&nbsp;ตรวจสอบคุณภาพสื่อ</h4>"},
                             
                             {href: "#user/history", text: "<h4><i class=\"fa fa-history\"></i>&nbsp;ประวัติการเข้าชม</h4>"},
                             {href: "#user/history_evaluate",role:"manager", text: "<h4><i class=\"fa fa-history\"></i>&nbsp;ประวัติการประเมินสื่อ</h4>"},
                             {href: "#user/home", text: "<h4><i class=\"fa fa-home\"></i>&nbsp;หน้าแรก</h4>"},
                             {divider: true},
                             {href: "#logout", text: "<h4><i class=\"fa fa-sign-out\"></i>&nbsp;ออกจากระบบ</h4>"}
                           ];
    $rootScope.detectResponseError = function (rejection) {
        if (rejection) {
        	
            if (rejection.data && rejection.data.error && rejection.data.message) {
            	var b=(rejection.data.error2===true);
                if (b || (Auth.isLoggedIn() !== false)) {
                	var str = rejection.data.message;
                	 $.notify('แจ้งเตือนความผิดพลาด : ' + str);
                }
            }
        }
    }
    $rootScope.urlEq = function (url) {
        return ($state.$current.url.source == url);
    }
    $rootScope.isTrue=function(v){
    	var b=String(v);
    	return (b=='1') || (b==true);
    }
    $rootScope.getModalIcon=function(v){
    	if(v) return 'glyphicon glyphicon-pencil';
    	return 'glyphicon glyphicon-plus'
    }    
    $rootScope.viewMedia=function(it){
    	if(it && it.media_id){
    		$rootScope.selectedMedia=it;
    		store.set('selectedMedia',it);
    		$state.go('user.media_detail');
    	}
    }
    $rootScope.isAdmin = function () {
        return Auth.isAdmin();
    }
    $rootScope.getUser=function(){
    	return Auth.getUser();
    }
    $rootScope.isRole=function(id){
    	var args = _.toArray(arguments);
    	var role='';
        if (id == '1') {
            role = 'admin';
        } else if (id == '2') {
            role = 'teacher';
        } else if (id == '3') {
            role = 'manager';
        } else if (id == '4') {
            role = 'director';
        } else if(id=='0') {
            role = 'student'
        }
        
    	if(args.indexOf(role)>0){
    		return true;
    	}
    	return false;
    }
   
    $rootScope.getUserNumber=function(){
    	var u=Auth.getUser();
    	return (u)?u['user_number']: 'Unknow';
    }
    $rootScope.getUserName=function(){
    	var u=Auth.getUser();
    	return (u)?(u['first_name'] +  ' ' + u['last_name']): 'Unknow';
    }    
    $rootScope.getRoleName=function(){
        return Auth.getRoleName();
    }   
    $rootScope.getRoleName2=function(){
        return $filter('lookup_role')(Auth.getRoleId());
    }       
    $rootScope.getUserId = function () {
        return Auth.getUserId();
    }
    $rootScope.logOut = function () {
        Auth.post('logout', {}).success(function () {
            Auth.logOut();
        })
    }
    var _lkey=null;
    $rootScope.loadLookups=function(key){
    	if(key){
    		if(key===_lkey){
    			return;
    		}
    	}
    	_lkey=key;
    	Lookups.load();
    }
    $rootScope.getModified=function(){
    	Auth.getModified();
    }
    $rootScope._form=null;
    $rootScope.evaluateForm=function(){
        if (!$rootScope._form) $rootScope._form = $modal({ scope: $rootScope,  backdrop: 'static', template: 'custom.user.evaluate.html', placement: "top", html: true, show: false });
        $rootScope._form.$promise.then($rootScope._form.show);
    }
    $rootScope._form2=null;
    $rootScope.evaluateForm2=function(){
        if (!$rootScope._form2) $rootScope._form2 = $modal({ scope: $rootScope,  backdrop: 'static', template: 'custom.user.evaluate2.html', placement: "top", html: true, show: false });
        $rootScope._form2.$promise.then($rootScope._form2.show);
    }    
    $rootScope._confirmUpload=null;
    $rootScope._confirmUploadFlow=null;
    $rootScope.confirmUpload=function(elm, func){
	    //if (!$rootScope._confirmUpload) {
	    	$rootScope._confirmUpload = $popover(angular.element(elm),  { scope: $rootScope, autoClose:true,trigger:'manual',placement:"top", template: "custom.confirm.upload.html", show: true});
	    //}
	    $rootScope._confirmUploadFlow=func;
	    $rootScope._confirmUpload.$promise.then($rootScope._confirmUpload.show);
    }
    $rootScope.accept_upload=function(){
    	
    	if($rootScope._confirmUploadFlow){
    		$rootScope._confirmUploadFlow.upload();
    		$rootScope._confirmUploadFunc=null;
    	}
    }
    
    $rootScope.$on('$stateChangeStart', function (e, toState, toParams, fromState, fromParams) {

    	
        var isLogin = (toState.name === "login");
        if (isLogin) {
            return; // no need to redirect 
        }

        // now, redirect only not authenticated
        var userInfo = Auth.isLoggedIn();

        if (userInfo === false) {
            e.preventDefault(); // stop current execution
            $state.go('login'); // go to login
        } else if (!Auth.canAccess(toState)) {
            e.preventDefault(); // stop current execution
        }
    });
    $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
        switch (fromState.name) {
            case 'admin.results':
                break;
        }
    });
}])
.config(['$stateProvider', '$urlRouterProvider','$httpProvider','flowFactoryProvider','storeProvider',
function ($stateProvider,   $urlRouterProvider, $httpProvider,flowFactoryProvider,storeProvider) {
		storeProvider.setStore("sessionStorage");
		flowFactoryProvider.defaults = {
	        singleFile: true
	    };
        $httpProvider.interceptors.push('myHttpInterceptor');

        $urlRouterProvider.otherwise('/login');


        // Use $stateProvider to configure your states.
        $stateProvider
          .state("admin", {
              // Use a url of "/" to set a state as the "index".
              url: "/admin",
              abstract:true,
              templateUrl:'views/admin.html',
              onEnter:['Lookups',function(Lookups){
            	  Lookups.load();
              }]
          })
          .state("admin.user", {
              url: "/user",
              roles: ['admin'],
              views:{
                  '': {
                      templateUrl: 'views/admin_user.html'
                  }}
                
          })
          .state("admin.type", {
              url: "/type",
              views:{
                  '': {
                      templateUrl: 'views/admin_type.html'
                  }}
          })
          .state("admin.evaluate_group", {
              url: "/evaluate_group",
              views:{
                  '': {
                      templateUrl: 'views/admin_evaluate_group.html'
                  }}
          })  
          .state("admin.evaluate_group.topic", {
              url: "/topic",
              views:{
                  '': {
                      templateUrl: 'views/admin_evaluate_topic.html'
                  }}
          })              
          .state("admin.group", {
              url: "/group",
              roles: ['admin'],
              views: {
                  '': {
                      templateUrl: 'views/admin_group.html'
                  }
              }
          })
          .state("admin.subject", {
              url: "/subject",
              roles: ['admin'],
              views: {
                  '': {
                      templateUrl: 'views/admin_subject.html'
                  }
              }
          })          
          .state("admin.level", {
              url: "/level",
              roles: ['admin'],
              views: {
                  '': {
                      templateUrl: 'views/admin_level.html'
                  }
              }
          })
          .state("admin.media", {
              url: "/media",
              roles: ['admin'],
              views: {
                  '': {
                      templateUrl: 'views/admin_media.html'
                  }
              }
          })          
          .state("login", {
              url: "/login",
              templateUrl: 'views/login.html'
          })
          .state("logout", {
              url: "/logout",
              controller:'LogoutCtrl',
              template: ''
          })          
          .state("user", {
              // Use a url of "/" to set a state as the "index".
              url: "/user",
              abstract:true,
              templateUrl:'views/user.html'
          })        
          .state('user.home', {
              url: '/home',
              templateUrl: 'views/user_home.html'
          })  
          .state('user.history', {
              url: '/history',
              templateUrl: 'views/user_history.html'
          })   
          .state('user.history_evaluate', {
              url: '/history_evaluate',
              roles: ['manager'],
              templateUrl: 'views/user_history_evaluate.html'
          })            
          .state('user.search', {
              url: '/search',
              controller:'SearchCtrl',
              templateUrl: 'views/user_search.html'
          })
          .state('user.profile', {
              url: '/profile',
              templateUrl: 'views/user_profile.html'
          })          

          .state('user.media_detail', {
              url: '/media_detail',
              templateUrl: 'views/user_media_detail.html'
          })          
          .state("teacher", {
              // Use a url of "/" to set a state as the "index".
              url: "/teacher",
              abstract:true,
              templateUrl:'views/teacher.html'
          })          
          .state('teacher.media', {
              url: '/media',
              templateUrl: 'views/teacher_media.html',
              onEnter:['Lookups',function(Lookups){
            	  Lookups.load();
              }]              
          })
          .state('teacher.media.evaluate', {
              url: '/evaluate',
              templateUrl: 'views/teacher_media_evaluate.html'
          })          
          .state('teacher.media.form', {
              url: '/form',
              templateUrl: 'views/teacher_media_form.html'
          
          })
          .state("director", {
              // Use a url of "/" to set a state as the "index".
              url: "/director",
              abstract:true,
              templateUrl:'views/director.html'
          })          
          .state('director.report', {
              url: '/report',
              templateUrl: 'views/director_report.html',
              onEnter:['Lookups',function(Lookups){
            	  Lookups.load();
              }]              
          })
          .state('director.report2', {
              url: '/report2',
              templateUrl: 'views/director_report2.html',
              onEnter: ['Lookups', function (Lookups) {
                  Lookups.load();
              }]
          })
    }
])


.factory('Auth', ['$http', '$rootScope','$alert','$state','API_URL','store', function ($http, $rootScope, $alert, $state, API_URL,store) {
    var user;
    var token;


    var alert_sussess = null;
    function showMessage(str, title) {
        if (str || title) {
            if (!alert_sussess) {
                alert_sussess = $alert({ container: 'body', title: '', content: '', type: 'success', placement: 'top-right', duration: 5, show: false });
            }
            alert_sussess.$scope.content = str;
            alert_sussess.$scope.title = title;
            alert_sussess.$promise.then(alert_sussess.show);
        }
    }

    function logOut() {
        user = null;
        token = null;
        store.remove('utoken');
        $state.go('login');
    }
    function getRoleId(){
        var role = ((user && user.user_id) ? user.user_type : '');
        return role;
    }
    function getRoleName(){
        var role = ((user && user.user_id) ? user.user_type : '');
        if (role == '1') {
            role = 'admin';
        } else if (role == '2') {
            role = 'teacher';
        } else if (role == '3') {
            role = 'manager';
        } else if (role == '4') {
            role = 'director';
        } else if(role=='0') {
            role = 'student'
        }else{
        	role='';
        };
        return role;
    }

    return {
        showMessage: showMessage,
        logOut:logOut,
        setUser: function (aUser, aToken) {
            user = aUser;
            if(arguments.length>1){
            	token = aToken;
            }
            if(user){
            	store.set('utoken', {user:user, token:token});
            }else{
            	store.remove('utoken');
            }
            if(arguments.length>1){
            	$state.go('login');
            }
        },
        getModified:function(){
        	if(user){
        		$http.post(API_URL + 'usermodified', {}).success(function (result) {
                	if(result && result.data){
                		user.date_modified=result.data.date_modified;
                	}
        		});
        	}
        },
        fetch:function(){
        	var tm=store.get('utoken');
        	if(tm && tm.user && tm.user.user_id){
        		user=tm.user;
        		token=tm.token;
        	}
        },
        getUser:function(){return user},
        getRoleName:getRoleName,
        getRoleId:getRoleId,
        canAccess:function(toState){
        	if(toState){
        		if(angular.isString(toState)){
        			var ars=toState.replace(/\s/g,'').split(',');
        		}else if (angular.isArray(toState.roles) && toState.roles.length) {
	                var role = getRoleName();
	                return (toState.roles.indexOf(role) >= 0);
	            }
        	}
            return true;
        },
        getUserId:function(){
            return (user && user.user_id) ? user.user_id : '';
        },
        isLoggedIn: function () {
            return (user && user.user_id) ? user : false;
        },

        isStudent: function () {
            return (user && user.user_id && user.user_type == '0');
        },
        isAdmin: function(){
            return (user && user.user_id && user.user_type=='1');
        },
        isTeacher: function(){
            return (user && user.user_id && user.user_type=='2');
        },
        isManager: function(){
            return (user && user.user_id && user.user_type=='3');
        },
        isDirector: function(){
            return (user && user.user_id && user.user_type=='4');
        },
        post: function (url, data, type) {
            return $http.post(API_URL + url, data);
        }
    }
}])



.factory('Lookups', ['Auth', '_', '$rootScope','moment','$filter','store', function (Auth, _, $rootScope, moment,$filter,store) {
    /*group, level, type, subject*/
    var lookups = { server_date: new Date(), role: [], group: [], level:[], type:[],subject:[],teacher:[],title:[]};
    var _hasChanged=false;
    function query() {
    	_hasChanged=false;
        return Auth.post('getlookups', {}).success(function (result) {
            if (result) {
                _.each(result.data, function (v, k) {
                    if (_.isArray(lookups[k])) {
                        v.unshift(0);
                        v.unshift(0);
                        lookups[k].length = 0;
                        lookups[k].splice.apply(lookups[k], v);
                    } else {
                        lookups[k] = v;
                    }
                })
               
                lookups.client_date = new moment();
                lookups.server_date = new moment(lookups.server_date || lookups.client_date);  
                store.set('lookups', lookups);
            }
        });
    }

    return {
    	hasChanged:function(){
    		return (_hasChanged===true);
    	},
    	setChanged:function(){
    		_hasChanged=true;
    	},
    	fetch:function(){
    		var tm=store.get('lookups');
    		if(tm){
    			angular.forEach(tm,function(v,k){if(k in lookups) lookups[k]=v});
    		}
    	},
        load: query,
        getLookups:function(){
            return lookups
        },
        getRole: function (id, fd) {
            if (id !== undefined) {
                if (!fd) fd = 'name';
                return _.find(lookups.role, function (it) { return (it.role_id == id) });
            }
            return lookups.role;
        },
        getTeacher: function (id, fd) {
            if (id !== undefined) {
                if (!fd) fd = 'name';
                return _.find(lookups.teacher, function (it) { return (it.user_id == id) });
            }
            return lookups.teacher;
        },
        getGroup: function (id, fd) {
            if (id !== undefined) {
                if (!fd) fd = 'name';
                return _.find(lookups.group, function (it) { return (it.group_id == id) });
            }
            return lookups.group;
        },
        getTitle: function (id, fd) {
            if (id !== undefined) {
                if (!fd) fd = 'name';
                return _.find(lookups.title, function (it) { return (it.name == id) });
            }
            return lookups.title;
        },
        getLevel: function (id, fd) {
            if (id !== undefined) {
                if (!fd) fd = 'name';
                return _.find(lookups.level, function (it) { return (it.level_id == id) });
            }
            return lookups.level;
        },
        getType: function (id, fd) {
            if (id !== undefined) {
                if (!fd) fd = 'name';
                return _.find(lookups.type, function (it) { return (it.type_id == id) });
            }
            return lookups.type;
        },
        getSubject: function (id, fd) {
            if (id !== undefined) {
                if (!fd) fd = 'name';
                return _.find(lookups.subject, function (it) { return (it.subject_id == id) });
            }
            return lookups.subject;
        }

    }
}])
.filter('nl2br', ['$sce', function ($sce) {
	 return function (str) {

		 str=str.replace(/<.{0,}>/gm,'');
		 str=str.replace(/\r\n/g,"\n");
		 str=str.replace(/\r/g,"\n");
		 str=str.replace(/\n/g,"<br/>");
		 return str;

	 }
}])
.filter('roleFilter', ['$rootScope', function ($rootScope) {
	 return function (items) {
		 var tmp=[];
		 var role=$rootScope.getRoleName();
		 angular.forEach(items, function (it){
			 if(it && (it['role']!==undefined)){
				 
			 }else{
				 tmp.push(it);
			 }
		 });
		 
		 return str;

	 }
}])
.filter('html_link', ['moment', function (moment) {
	 return function (str) {
		str=String(str);
		str=str.trim();
		str=str.replace(/\r\n/g,"\n");
		str=str.replace(/\r/g,"\n");
		var ars=str.split("\n");
		if(ars.length){
			var elm=angular.element('<div></div>');
			for(var i=0;i<ars.length;i++){
				str=ars[i];
				if(str){
					if(str.match(/^www[.][\w\d]{1,}[.][\w|\d]{1,}/i)){
						str='http://' + str;
					}
					if(str.match(/^https{0,1}:/i)){
						var a=angular.element('<a></a>');
						a.attr('href', str);
						a.attr('target','_blank');
						a.text(str);
						elm.append(a);
						elm.append('<br/><br/>');
					}else{
						var s=angular.element('<strong></strong>');
						s.text(str);
						elm.append(s).append('<br/>');
					}
				}
			}
			str=elm.html();
			elm.empty();
			return str;
		}
		return '';
	 }
}])
.filter('date_version', ['moment', function (moment) {
    return function (dt) {
        var str = '';
        if(dt){
        	var d=new moment(dt);
        	if(d.isValid()){
        		str=d.format('X');
        	}
        	d=null;
        }
        return str;
    }
}])
.filter('thai_date', ['moment', function (moment) {
    var INC_YEAR = 543;
    return function (dt, format) {
        
        var str = '';
        if (dt) {
            
            var d = new moment(dt);
            if (d) {
                if (format == undefined) format = 'full';
                var skip = 0;
                var fm=String(format).toLowerCase();
                switch (fm) {
                	case 'full':
                		format = 'D MMMM ';
                		break;
                    case '0':
                    case 'days':
                        format = 'D MMMM ';
                        break;
                    case '1':
                    case 'months':
                        format = 'MMMM ';
                        break;
                    case '2':
                    case 'years':
                        format = '';
                        break;
                    case 'short':
                        format = 'D MMM ';
                        break;
                    case 'fromnow':
                        str = d.fromNow();
                        skip = 1;
                        break;
                    case 'tonow':
                        str = d.toNow();
                        skip = 1;
                        break;
                    case 'calendar':
                        str = d.calendar();
                        skip = 1;
                        break;
                    default:
                        skip = 2;
                        break;
                }
                if (skip) {
                    if (skip == 2) {
                        if (format) str = d.format(format);
                    }
                }else{
                    if (format) str = d.format(format);
                    str = str + (d.year() + 543).toString();
                    if(fm=='full'){
                    	if(d.hour() || d.minute() || d.seconds()){
                    		str=str + d.format(' เวลา H:mm:ss น.'); 
                    	}
                    }
                }
                if (str) {
                    str = str.replace('เวลา 0 นาฬิกา 0 นาที', '');
                    //str = str.replace('0 นาฬิกา', '');
                    str = str.replace('0 นาที', '');
                    str = str.replace('0 วินาที', '');
                    if ((/^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}$/).test(str)) {
                        format = 'D MMMM ';
                        if (format) str = d.format(format);
                        str = str + (d.year() + INC_YEAR).toString();
                    }
                }
                d = null;
            }
            
        }
        return str;
    }
}])

.filter('lookup_role', ['Lookups', function (Lookups) {
    return function (id, fd) {
        var str = '';
        if (id !== undefined) {
            if (!fd) fd = 'name';
            var it = Lookups.getRole(id);
            if (it && (it[fd] !== undefined)) str = it[fd];
        }
        return str;
    }
}])

.filter('lookup_group', ['Lookups', function (Lookups) {
    return function (id, fd) {
        var str = '';
        if (id !== undefined) {
            if (!fd) fd = 'name';
            var it = Lookups.getGroup(id);
            if (it && (it[fd] !== undefined)) str = it[fd];
        }
        return str;
    }
}])
.filter('lookup_teacher', ['Lookups', function (Lookups) {
    return function (id, fd) {
        var str = '';
        if (id !== undefined) {
            if (!fd) fd = 'first_name';
            var it = Lookups.getTeacher(id);
            if (it && (it[fd] !== undefined)) {
                str = it['title']+ ' ' + it[fd] + ' ' + it['last_name'];
            }
        }
        return str;
    }
}])
.filter('lookup_level', ['Lookups', function (Lookups) {
    return function (id, fd) {
        var str = '';
        if (id !== undefined) {
            if (!fd) fd = 'name';
            var it = Lookups.getLevel(id);
            if (it && (it[fd] !== undefined)) str = it[fd];
        }
        return str;
    }
}])

.filter('lookup_type', ['Lookups', function (Lookups) {
    return function (id, fd) {
        var str = '';
        if (id !== undefined) {
            if (!fd) fd = 'name';
            var it = Lookups.getType(id);
            if (it && (it[fd] !== undefined)) str = it[fd];
        }
        return str;
    }
}])

.filter('lookup_subject', ['Lookups', function (Lookups) {
    return function (id, fd) {
        var str = '';
        if (id !== undefined) {
            if (!fd) fd = 'name';
            var it = Lookups.getSubject(id);
            if (it && (it[fd] !== undefined)) str = it[fd];
        }
        return str;
    }
}])

.controller('LoginCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$rootScope', 'cfpLoadingBar','$http','$modal', function ($scope, $timeout, Auth, $state, Lookups, $rootScope, cfpLoadingBar,$http,$modal) {
    $scope.userid = '';
    $scope.password = '';
    $scope.alert = false;
    $scope.errorMesssage = '';
    $scope.checking = false;
  
    $scope.show = function () {
        if (!$scope.alert) {
            $scope.alert = true;
            if ($scope._timeout) $timeout.cancel($scope._timeout);
            $scope._timeout=$timeout($scope.hide, 3000);
        }
        $scope.checking = false;
    }

    $scope.hide = function () {
        if ($scope.alert) {
            $scope.alert = false;
            delete $scope._timeout;
        }
       
    } 
    
    var formModal = null;
    var formModal2 = null;
    $scope.saveRegister = function (it) {
  
    	if(it){
    		var d=angular.extend({},it);
    		delete d.password2;
            Auth.post('register', d).success(function (result) {
            	if(result && result.data){
            		formModal.$promise.then(formModal.hide);
            		$scope.registerSuccesss();
            	}
            });
    	}
    }
    $scope.registerSuccesss=function(){
        if (!formModal2) formModal2 = $modal({ scope: $scope,  backdrop: false, template: 'custom.form.regiter.success.html', placement: "top", html: true, show: false });
        formModal2.$promise.then(formModal2.show);
    }
    $scope.register = function () {
      	
        $scope.userid = '';
        $scope.password = '';
        $scope.alert = false;
        $scope.errorMesssage = '';
        $scope.checking = false;    	
        $scope.editingItem={};
    
    	Lookups.load().success(function () {
            cfpLoadingBar.complete();
            if (!formModal) formModal = $modal({ scope: $scope,  backdrop: false, template: 'custom.form.regiter.html', placement: "top", html: true, show: false });
            $scope.editingItem={user_type:'0',active:'0',password:'',user_id:''};
            formModal.$promise.then(formModal.show);
        });
    }
    $scope._roles=[];
    $scope.getRoles=function(){
    	$scope._roles.length=0;
    	angular.forEach(Lookups.getRole(), function (v){
    		if(v.role_id!='1'){
    			$scope._roles.push(v);
    		}
    	});
    	return $scope._roles;
    }
    $scope.login = function () {
        if ($scope.userid && $scope.password) {
            $scope.hide();
            var btn = $('#login-button:first');
     
          
            $scope.checking = true;
            Auth.post('login', { user_number: $scope.userid, password: $scope.password }).success(function (result) {
                $scope.checking = false;
                    Auth.setUser(result.data.user, result.data.api_key);
                    cfpLoadingBar.start();
                    Lookups.load().success(function () {
                        cfpLoadingBar.complete();
                        $state.go('user.home');
                    });
            }).error(function (result) {

                var str = '';
                if (result.error && result.message) {
                    str = result.message;
                } else {
                    str = 'ขออภัยเกิดความขัดข้อง ไม่สามารถเชื่อต่อกับ API ได้';
                }

                $scope.checking = false;
                $scope.errorMessage = str;
                if(str) $scope.show();

            })
        }

    }
}])

.controller('ProfileCtrl', ['$scope', '$timeout', '$rootScope','Auth','$state','$modal','_', function ($scope, $timeout, $rootScope, Auth, $state,$modal,_) {
	$scope.editingItem=null;
	$scope.original=null;
    Auth.post('profile/get', {user_id:$rootScope.getUserId()}).success(function (result) {
    	$scope.editingItem=result.data;
    	$scope.original=angular.copy(result.data);
    });
    $scope.saveProfile=function(editingItem){
    	if(editingItem){
    	    Auth.post('profile/update',editingItem).success(function (result) {
    			if(result.data){
    				Auth.setUser(result.data);
    				$scope.original=angular.copy(result.data);
    			}
    			//$state.go('user.search');
    			$scope.restore();
    	    });
    	}
    }
    $scope.incUser=function(){
    	if(!$rootScope.INC_USER) $rootScope.INC_USER=0;
    	$rootScope.INC_USER++;
    }
    $scope.uptime='';
    $scope.changePhoto=function(){
    	$scope.uptime='&up='+ Number(new Date());
    }
    $scope.goBack=function(){
    	$state.go('user.search');
    }    
    
    $scope.validateFile=function($file){
    	if($file.size>(1024 * 1024)){
    		$scope.sizeError='ขนาดไฟล์ภาพที่อัพโหลดได้ ไม่เกิน 1 Megabytes';
    		$timeout(function(){$scope.sizeError=null;},3000);
    		return false;
    	}
    	$scope.sizeError=null;
    	return true;
    }
    $scope.setEdit=function(v){
    	$scope.editing=v;
    }
    $scope.restore=function(){
    	if($scope.editing){
    		$scope.editing=false;
    		angular.extend($scope.editingItem,$scope.original); 	
    	}
    }
    $scope.doCancel=function(){
    	if($scope.editing){
    		$scope.editing=false;
    		angular.extend($scope.editingItem,$scope.original); 
    		return;
    	}
    	$scope.goBack();
    }
    
    $scope.changePass=function(){
    	$scope.restore();
       $scope.editingItem2={};
       if(!$scope._modal){
    	   $scope._modal = $modal({ scope: $scope, title: '', backdrop: 'static', template: 'custom.form.password.html', placement: "top", html: true, show: false });
       }
   	   $scope._modal.$promise.then($scope._modal.show);
    }
    
    $scope.savePass=function($hlr){
    	if($scope.editingItem2){
    		$scope._hlr=$hlr;
    	    Auth.post('profile/changepassword',$scope.editingItem2).success(function (result) {
    			if(result.data){
    				$.notify('รหัสผ่านถูกเปลี่ยนแแล้ว','success');
    				if($scope._hlr) $scope._hlr();
    			}
    	    });
    	}
    }
}])
.controller('LogoutCtrl', ['$scope', '$timeout', '$rootScope', function ($scope, $timeout, $rootScope) {

	$timeout(function(){
		$rootScope.logOut();
	})
}])

.factory('Search', ['$http', function ($http) {
    var options = {evaluate:'0'};
    return {
        getOptions: function () { return options }
    }
}])

.controller('SearchCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', 'Search','_', function ($scope, $timeout, Auth, $state, Lookups, Search,_) {
	Lookups.load();
	
    $scope.options = Search.getOptions();

    $scope.search = function () {
        $state.go('user.search');
    }
    $scope.manage = function () {
        $state.go('admin.media');
    }
    $scope.isTeacherOrAdmin = function () {
        return true;
    }
    $scope.gotoSearch = function () {
        $state.go('user.search');
    }
    $scope.setFilter=_.debounce(function(){
    	if($scope.Table){
   
    		$scope.Table.getScope().searchText=$scope.searchText;
    		$scope.Table.getScope().setFilter(true);
    	}
    },200); 
    
    $scope.setTable=function(tb){
    	$scope.Table=tb;
    }
    $scope.changeGroup=function(it){
    	if(it){
    		$scope._subject=_.filter(Lookups.getSubject(),function(t){
    			return (t['group_id']==it);
    		});
    		$scope._teacher=_.filter(Lookups.getTeacher(),function(t){
    			return (t['group_id']==it);
    		});    		
    	}else{
    		$scope._subject=Lookups.getSubject();
    		$scope._teacher=Lookups.getTeacher();
    	}
    	$scope.setFilter();
    }
    $scope.changeTeacher=function(it){
    	if(it){
    		it=Lookups.getTeacher(it)['level_id'];
    		$scope._level=_.filter(Lookups.getLevel(),function(t){
    			return (t['level_id']==it);
    		});
  		
    	}else{
    		$scope._level=Lookups.getLevel();
    	}
    	$scope.setFilter();
    }    
    $scope._subject=[];
    $scope.getSubject=function(){
    	return $scope._subject;
    }
    $scope._teacher=[];
    $scope.getTeacher=function(){
    	return $scope._teacher;
    }
    $scope.getLevel=function(){
    	return $scope._level;
    }
    $scope.changeGroup($scope.options.group);
    $scope.changeTeacher($scope.options.teacher);
    

}])


.controller('GroupCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', 'Search','_', function ($scope, $timeout, Auth, $state, Lookups, Search,_) {

    this.changeGroup=$scope.changeGroup=function(it){
    	if(it){
    		$scope._subject=_.filter(Lookups.getSubject(),function(t){
    			return (t['group_id']==it);
    		});
    	}else{
    		$scope._subject=Lookups.getSubject();
    	}
    }
   
    $scope._subject=[];
    this.getSubject=$scope.getSubject=function(){
    	return $scope._subject;
    }

    if($scope.editingItem) $scope.changeGroup($scope.editingItem.group_id);


}])


.controller('UserCommentCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal','$rootScope','$window','API_URL', function ($scope, $timeout, Auth, $state, Lookups, $modal,$rootScope,$window, API_URL) {

    $scope.canEditComment=function(it){
    	if(it && it.comment_id){
    		var user_id=$rootScope.getUserId();
    		return (it.user_id==user_id) || (it.author_id==user_id) || $rootScope.isAdmin();
    	}
    	return false;
    }
    $scope.callHideComment=function(it){
    	if($scope.$parent.comment_tb){
    		var data={'comment_id':$scope._commentItem['comment_id'],'hide':'1'};
    		$scope.$parent.comment_tb.callUpdateItem(data);
    	}
    	return false;
    }
    $scope.isVisible=function(it){
    	if(it['hide']==1){
    		return true;
    	}
    	return false;
    }
    $scope.callRemoveComment=function(it){
    	if($scope.$parent.comment_tb){
    		$scope.$parent.comment_tb.callRemoveItem($scope._commentItem);
    	}
    	return false;
    }    
    $scope.callEditComment=function(it){
    	if($scope.$parent.comment_tb){
    		$scope.$parent.comment_tb.callEditItem($scope._commentItem);
    	}
    	return false;
    }  
    
    $scope.selectComment=function(it){
    	 $scope._commentItem=it;
    }
   
}])

.controller('MediaCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal','$rootScope','$window','API_URL', function ($scope, $timeout, Auth, $state, Lookups, $modal,$rootScope,$window, API_URL) {

	$scope.viewMedia=function(it){
    	Auth.post('view_media',{media_id:it.media_id}).success(function(data){
    		$scope.update_stat(data);
    	});
    	
    }
    $scope.likeMedia=function(it){
    	Auth.post('like_media',{media_id:it.media_id}).success(function(data){
    		$scope.update_stat(data);
    	});    	

    }
    $scope.unlikeMedia=function(it){
    	Auth.post('unlike_media',{media_id:it.media_id}).success(function(data){
    		$scope.update_stat(data);
    	});

    }
 
    $scope.approveMedia=function(it){
    	alert('approve '+ it['media_id']);
    }
    /*
    $scope.ratingMedia=function(it){
    	if(it && it.statistics){
	    	var rating=it.statistics.ratings;
	    	Auth.post('rating_media',{media_id:it.media_id, rating:rating}).success(function(data){
	    		$scope.update_stat(data);
	    	}); 
    	}
    }
    */

    $scope.downloadMedia=function(it){
    	if(it && it.media_id){
    		var fm=$rootScope.downloadIFrame || $('iframe#downloader:first','body');
    		if(fm.length<1){
    			fm=$('<iframe border="0"></iframe>');
    			fm.attr('id','downloader').height(0).width(0).css('display:hidden');
    			$rootScope.downloadIFrame=fm;
    		}
    		$('body').append(fm);
    		var url=API_URL+ 'download/'+it.media_id;
    		fm.attr('src', url);
    		$timeout(function(){
    			fm.remove();
    		},3000);
    	}

    }  
    $scope.isDownloadAble=function(it){
    	if(it && it.url){
    		if(((it.url || '').search(/^https{0,1}:/i)<0)) return true;
    		return ((it.url || '').search(/\.(mp4|pdf|mp3|ppt|swf|doc|xls|avi)$/i)>=0);
    	}
    	return false;
    }
    $rootScope.fetchViewMedia=function(){
    	
    	var it=$rootScope.selectedMedia;
    	if(it && it['media_id']){
    		
        	Auth.post('searh_media/get',{media_id:it.media_id}).success(function(data){
        		if(data.data){
        			$rootScope.selectedMedia=angular.extend($rootScope.selectedMedia, data.data);
        			$rootScope.viewMedia($rootScope.selectedMedia);
        			$rootScope.viewMedia($rootScope.selectedMedia);	
        			$scope.fetch();
        		}
        	});  
    	}
    }
    $scope.fetch=function(){
    	var it=$rootScope.selectedMedia;
    	if(it && it['media_id']){
    		
        	Auth.post('searh_media/stat',{media_id:it.media_id}).success(function(data){
        		$scope.update_stat(data);
        		
        	});  
    	}
    }
    $scope.update_stat=function(data){
    	var it=$rootScope.selectedMedia;
		if(data && data.data){
			if(!it.statistics)it.statistics={};
			angular.forEach(data.data,function(a,b){
				it.statistics[b]=a;
			});
			$rootScope.viewMedia($rootScope.selectedMedia);		
		
		};
    }
    $timeout($scope.fetch);
}])

.controller('HomeCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal','$rootScope','$window','API_URL', function ($scope, $timeout, Auth, $state, Lookups, $modal,$rootScope,$window, API_URL) {
	$scope.new_items=[];
	$scope.top_items=[];
	$scope.openMedia=function(it){
		$rootScope.viewMedia(it);
	}
	$scope.fetch=function(it){
    	Auth.post('searh_media/home',{}).success(function(data){
    		if(data.data){
    			angular.forEach(data.data,function(v,k){
    				$scope[k]=v;
    			});
    		}
    	});    	
    }
    $timeout($scope.fetch);
}])
.controller('FlowUploadCtrl', ['$scope', '$modal','$timeout', 'Auth', '$state', 'Lookups', 'Search', function ($scope,$modal, $timeout, Auth, $state, Lookups, Search) {

	$scope.thumb_ver=0;
    this.linkScope=function(s){
    	$scope._scope=s;
    }
    this.updateThumb=function(flow){
		$timeout(function(){

			if(flow && flow.files.length && $scope._scope.editingItem && flow.files[0].response){
				$scope._scope.editingItem.thumb=flow.files[0].response;
				$scope._scope.saveForm(false)
				.success(function(){
					$scope.thumb_ver++;
					flow.cancel();
					 $.notify('อัพโหลดภาพตัวอย่าเสร็จแล้ว','success');
				});
			}
		});
    }
    $scope._flowState=0;
    this.setFlowState=function(i){
    	$scope._flowState=i;
    }
    this.getFlowState=function(){
    	return $scope._flowState;
    }
    this.getFlow=function(){
    	return $scope._flow;
    }
    this.setFlow=function(flow){
    	
    	$scope._flow=flow;

    }
    this.checkFlow=function(){
    	if($scope._flow){
    		var er=false;
    		if($scope._flow.files){
	    		for(var i=0;i<$scope._flow.files.length;i++){
	    			if($scope._flow.files[i].error){
	    				er=true;
	    				break;
	    			}
	    		}
    		}
    		if(er){
    			$scope._flowState=-1;
    		}else{
    			$scope._flowState=0;
    			$timeout(function(){
    				if($scope._scope.editingItem && $scope._flow.files[0].response){
    					$scope._scope.editingItem.url=$scope._flow.files[0].response;
						$scope._scope.saveForm(false)
						.success(function(){
							$scope._flow.cancel();
							 $.notify('อัพโหลดเสร็จแล้ว','success');
						});
    				}
    			});
    		}
    	}
    }
    this.retry=function(){
    	if($scope._flow){
    		if($scope._flowState==-1){
	    		if($scope._flow.files){
		    		for(var i=0;i<$scope._flow.files.length;i++){
		    			if($scope._flow.files[i].error){
		    				$scope._flow.files[i].retry();
		    				$scope._flowState=2;
		    			}
		    		}
	    		}
    		}else{
    			$scope._flow.resume();
    		}
    	}
    }
    this.getScope=function(){
    	return $scope;
    }
    $scope.uploadFlow=function(fl){
    	if(!$scope._dlg){
    	$scope._dlg=$modal({ scope: $scope, title: '', backdrop: false, template: 'upload-agreement.html', placement: "top", html: true, show: false });
    	}
    	$scope.$xflow=fl;
    	$scope._dlg.$promise.then($scope._dlg.show);
    }
    $scope.accept_upload=function(){
    	$scope.$xflow.upload();
    }
}])
.controller('UserEvalCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal','$rootScope','_','API_URL','$popover', function ($scope, $timeout, Auth, $state, Lookups, $modal,$rootScope,_, API_URL,$popover) {

   $scope.items=[
     {id:1, field:'score1', name:'สื่อมีเนื้อหาสาระที่ดี ถูกต้อง เหมาะสม', score:null},
     {id:2, field:'score2', name:'สื่อมีภาพประกอบที่ชัดเจน น่าสนใจ', score:null},
     {id:3, field:'score3', name:'สื่อส่งเสริมทักษะ กระบวนการคิดของผู้เรียน', score:null},
     {id:4, field:'score4', name:'สื่อมีการผสมผสาน หลายชนิด (Multimedia)', score:null},
     {id:5, field:'score5', name:'สื่อสามารถโต้ตอบกับผู้เรียน / ให้ผู้เรียนมีส่วนร่วมในการเรียนรู้', score:null},
   ];
   $scope.ready=false;
   $scope.editMode=false;
   $scope.score_total=0;
   $scope.check_count=0;
   $scope.score_group={'0':0,'1':0,'2':0,'3':0,'4':0};
   $scope.score_result=0;
   $scope.score_result_text='';
   $scope.isEdit=false;
   $scope.my_comment='';
   $scope.setEdit=function(){
	   if($scope.editMode){
		   $scope.editMode=false;
	   	   $scope.isEdit=true;
	   }
   }
   $scope.setScore=function(it,n){
	   if(!$scope.editMode){
		   if(it) it.score=n;
	   }
   }
   $scope.calTotal=function(){
	   $scope.score_total=_.reduce($scope.items,function(s,it){
		   if(it['score']) s+=parseInt(it['score']);
		   return s;
	   },0);
	  var c=0;
	   _.extend($scope.score_group,{'0':0,'1':0,'2':0,'3':0,'4':0},_.countBy($scope.items, function(it){
		   if(it['score']!=null){
			   c++;
			   return (it['score']).toString();
		   }
		   return 'x';
	   }));
	   $scope.check_count=c;
	   $scope.ready=(c==$scope.items.length);
	   if(c){
		   $scope.score_result=$scope.score_total / ($scope.check_count * 4) * 4;
	   }else{
		   $scope.score_result=0;
	   }
	   
	   if($scope.score_result<1){
		   $scope.score_result_text='ต้องปรับปรุง';
	   }else if($scope.score_result<2){
		   $scope.score_result_text='ควรปรับปรุง';
	   }else if($scope.score_result<3){
		   $scope.score_result_text='พอใช้';
	   }else if($scope.score_result<4){
		   $scope.score_result_text='ดี';
	   }else{
		   $scope.score_result_text='ดีมาก';
	   }
   }
   $scope.$watch(
           "items",
           function( newValue, oldValue ) {
               $scope.calTotal();
           },true
   );
   $scope.saveEvaluate=function(hlr){
	   if($scope.ready && $rootScope.selectedMedia){
		   var p={user_id:$rootScope.getUserId()};
		   p.media_id=$rootScope.selectedMedia.media_id;
		   _.each($scope.items, function(it){
			   p[it.field]=it.score;
		   });

		   
		   p["avg"]= $scope.score_result;
	        return Auth.post('user_evaluate/update', p).success(function (result) {
	            if (result) {
	                if(hlr)hlr();
	                $rootScope.fetchViewMedia();
	            }
	        });		   

	   }
   }
   $scope.loadEvaludate=function(){
	   if($rootScope.selectedMedia){
		   var p={user_id:$rootScope.getUserId()};
		   p.media_id=$rootScope.selectedMedia.media_id;
		   
	        return Auth.post('user_evaluate/get', p).success(function (result) {
	            if (result && result.data) {
	            	$scope.editMode=(result.data.media_id && result.data.user_id);
	
	                _.each($scope.items ,function(it){
	                	var k=it['field'];
	                	if(k in result.data) it['score']=parseInt(result.data[k]);
	                })
	            }
	        });	
	   }
   }
   $scope.accept_save=function(){
	   $scope.saveEvaluate($scope._confirmHlr); 
   }
   $scope.confirmSave=function(elm, func){
	   
	    if($scope.isEdit){
	    	if (!$scope._confirmUpload) {
	    	$scope._confirmUpload = $popover(angular.element(elm),  { scope: $scope, autoClose:true,trigger:'manual',placement:"top", template: "custom.confirm.eval.html", show: true});
	    	}
	    	$scope._confirmHlr=func;
	    	$scope._confirmUpload.$promise.then($scope._confirmUpload.show);
	    }else{
	    	$scope.saveEvaluate(func);
	    }
   }   
   $scope.loadEvaludate();
}])
.controller('UserEval2Ctrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal','$rootScope','_','API_URL','$popover', function ($scope, $timeout, Auth, $state, Lookups, $modal,$rootScope,_, API_URL,$popover) {

   $scope.items=[];
   $scope.ready=false;
   $scope.score_total=0;
   $scope.check_count=0;
   $scope.score_group={'0':0,'1':0,'2':0,'3':0,'4':0};
   $scope.score_result=0;
   $scope.score_result_text='';
   $scope.editMode=false;
   $scope.isEdit=false;
   $scope.my_comment='';
   $scope.setEdit=function(){
	   if($scope.editMode){
		  
		   $scope.editMode=false;
	   	   $scope.isEdit=true;
		  
	   }
   }
   $scope.setScore=function(it,n){
	   if(!$scope.editMode){
		   if(it && (it.group!==true))it.score=n;
	   }
   }   
   $scope.tdClass=function(it,i){
	   if(it.group===true) return 'info';
	   return '';
   }   
   $scope.calTotal=function(){
	   var ln=0;
	   $scope.score_total=_.reduce($scope.items,function(s,it){
		   if(it.group!==true){
		   if(it['score']) s+=parseInt(it['score']);
		   ln++;
		   }
		   return s;
	   },0);
	  var c=0;
	   _.extend($scope.score_group,{'0':0,'1':0,'2':0,'3':0,'4':0},_.countBy($scope.items, function(it){
		   if(it.group!==true){
		   if(it['score']!=null){
			   c++;
			   return (it['score']).toString();
		   }
		   }
		   return 'x';
	   }));
	   $scope.check_count=c;
	   $scope.ready=(c==ln);
	   if(c){
		   $scope.score_result=$scope.score_total / ($scope.check_count * 4) * 4;
	   }else{
		   $scope.score_result=0;
	   }
	   
	   if($scope.score_result<1){
		   $scope.score_result_text='ต้องปรับปรุง';
	   }else if($scope.score_result<2){
		   $scope.score_result_text='ควรปรับปรุง';
	   }else if($scope.score_result<3){
		   $scope.score_result_text='พอใช้';
	   }else if($scope.score_result<4){
		   $scope.score_result_text='ดี';
	   }else{
		   $scope.score_result_text='ดีมาก';
	   }
   }
   $scope.$watch(
           "items",
           function( newValue, oldValue ) {
               $scope.calTotal();
           },true
   );
   
   $scope.saveEvaluate=function(hlr){
	   if($scope.ready && $rootScope.selectedMedia){
		   var p={user_id:$rootScope.getUserId(),items:[]};
		   p.media_id=$rootScope.selectedMedia.media_id;
		   p.avg_score=$scope.score_result;

		   p.comment=$scope.my_comment;
		   _.each($scope.items, function(it){
			   if(it.id){
				   p.items.push({id:it.id,  score:it.score});
			   }
		   });

	        return Auth.post('user_evaluate_form/update', p).success(function (result) {
	            if (result) {
	                if(hlr)hlr();
	                $rootScope.fetchViewMedia();
	            }
	        });		   

	   }
   }
   $scope.loadEvaluate=function(){
	   if($rootScope.selectedMedia){
		   var p={user_id:$rootScope.getUserId()};
		   p.media_id=$rootScope.selectedMedia.media_id;
		 
	        return Auth.post('user_evaluate_form/get', p).success(function (result) { 
	        	
	            if (result && result.data.items) {
	            	if(result.data.form){
	            	$scope.my_comment=result.data.form.comment || ''; 
	            	}
	            	$scope.items=result.data.items;
	            	$scope.editMode=(!!result.data.form);
	 
	            }
	        });	
	   }
   }
   $scope.accept_save=function(){
	   $scope.saveEvaluate($scope._confirmHlr); 
   }
   $scope.confirmSave=function(elm, func){

	    if($scope.isEdit){
	    	if (!$scope._confirmUpload) {
	    	$scope._confirmUpload = $popover(angular.element(elm),  { scope: $scope, autoClose:true,trigger:'manual',placement:"top", template: "custom.confirm.eval.html", show: true});
	    	}
	    	$scope._confirmHlr=func;
	    	$scope._confirmUpload.$promise.then($scope._confirmUpload.show);
	    }else{
	    	$scope.saveEvaluate(func);
	    }
   }    
   $scope.loadEvaluate();
}])
.controller('TeacherEvalCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal','$rootScope','_','API_URL','$popover', function ($scope, $timeout, Auth, $state, Lookups, $modal,$rootScope,_, API_URL,$popover) {

   $scope.items=[
     {id:1,  name:'สื่อมีเนื้อหาสาระที่ดี ถูกต้อง เหมาะสม'},
     {id:2,  name:'สื่อมีภาพประกอบที่ชัดเจน น่าสนใจ'},
     {id:3,  name:'สื่อส่งเสริมทักษะ กระบวนการคิดของผู้เรียน'},
     {id:4,  name:'สื่อมีการผสมผสาน หลายชนิด (Multimedia)'},
     {id:5,  name:'สื่อสามารถโต้ตอบกับผู้เรียน / ให้ผู้เรียนมีส่วนร่วมในการเรียนรู้'},
   ];
   $scope.ready=false;
   $scope.score_total=0;
   $scope.check_count=0;
   $scope.score_group={'0':0,'1':0,'2':0,'3':0,'4':0};
   $scope.score_result=0;
   $scope.score_result_text='';
   $scope.count=0;


   $scope.calTotal=function(){
	   var sum=_.reduce($scope.items,function(s,it){
		   
			   s['0']+=parseInt(it['score0']);
			   s['1']+=parseInt(it['score1']);
			   s['2']+=parseInt(it['score2']);
			   s['3']+=parseInt(it['score3']);
			   s['4']+=parseInt(it['score4']);
		   
		   return s;
	   },{'0':0,'1':0,'2':0,'3':0,'4':0});
	   _.extend($scope.score_group,sum);
	   var c=_.reduce($scope.score_group,function(all,it, k){
		   all.count+=it;
		   all.score+=(it * parseInt(k));

		   return all}
	   ,{count:0,score:0});
	   
	  
	  $scope.check_count=c.count;
	  $scope.score_total=c.count * 4;
	  $scope.ready=false;
	  $scope.rawscore=c.score;
	   if($scope.check_count){
		   $scope.score_result=(c.score / $scope.score_total) * 4;
	   }else{
		   $scope.score_result=0;
	   }
	   
	   if($scope.score_result<1){
		   $scope.score_result_text='ต้องปรับปรุง';
	   }else if($scope.score_result<2){
		   $scope.score_result_text='ควรปรับปรุง';
	   }else if($scope.score_result<3){
		   $scope.score_result_text='พอใช้';
	   }else if($scope.score_result<4){
		   $scope.score_result_text='ดี';
	   }else{
		   $scope.score_result_text='ดีมาก';
	   }
   }
   $scope.$watch(
           "items",
           function( newValue, oldValue ) {
               $scope.calTotal();
           },true
   );

   $scope.loadEvaludate=function(){
	   
	   if($rootScope.teacherMedia){
		   var p={user_id:$rootScope.getUserId()};
		   p.media_id=$rootScope.teacherMedia.media_id;
		  
	        return Auth.post('teacher_evaluate/get', p).success(function (result) {
	            if (result && result.data) {
	            	$scope.count=result.count;
	                _.each($scope.items ,function(it){
	                	var it2=_.find(result.data,function(itf){
	                		return (it.id==itf.id);
	                	});
	                	if(it2){
	                		_.extend(it,it2);
	                	}
	                })
	            }
	        });	
	   }
   }
  
   $scope.loadEvaludate();
}])
.controller('TeacherEval2Ctrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal','$rootScope','_','API_URL','$popover', function ($scope, $timeout, Auth, $state, Lookups, $modal,$rootScope,_, API_URL,$popover) {

   $scope.items=[];
   $scope.ready=false;
   $scope.score_total=0;
   $scope.check_count=0;
   $scope.score_group={'0':0,'1':0,'2':0,'3':0,'4':0};
   $scope.score_result=0;
   $scope.score_result_text='';
   $scope.count=0;


   $scope.tdClass=function(it,i){
	   if(it.group===true) return 'info';
	   return '';
   }   
   $scope.calTotal=function(){
	   var ln=0;
	   $scope.score_total=_.reduce($scope.items,function(s,it){
		   if(it.group!==true){
		   if(it['score']) s+=parseInt(it['score']);
		   ln++;
		   }
		   return s;
	   },0);
	 
	 var sum= _.reduce($scope.items, function(all,it){
		  if(!it.group){
		  all['0']+=parseInt(it.score0);
		  all['1']+=parseInt(it.score1);
		  all['2']+=parseInt(it.score2);
		  all['3']+=parseInt(it.score3);
		  all['4']+=parseInt(it.score4);
		  }
		  return all;
	  },{'0':0,'1':0,'2':0,'3':0,'4':0});
	 

	   _.extend($scope.score_group,{'0':0,'1':0,'2':0,'3':0,'4':0},sum);
	   
	  var c=_.reduce($scope.score_group,function(all,it, k){
		   all.count+=it;
		   all.score+=(it * parseInt(k));

		   return all}
	   ,{count:0,score:0});
	  
	  $scope.check_count=c.count;
	  $scope.score_total=c.count * 4;
	  $scope.ready=false;
	  $scope.rawscore=c.score;
	   if($scope.check_count){
		   $scope.score_result=(c.score / $scope.score_total) * 4;
	   }else{
		   $scope.score_result=0;
	   }
	   
	   if($scope.score_result<1){
		   $scope.score_result_text='ต้องปรับปรุง';
	   }else if($scope.score_result<2){
		   $scope.score_result_text='ควรปรับปรุง';
	   }else if($scope.score_result<3){
		   $scope.score_result_text='พอใช้';
	   }else if($scope.score_result<4){
		   $scope.score_result_text='ดี';
	   }else{
		   $scope.score_result_text='ดีมาก';
	   }
   }
   $scope.$watch(
           "items",
           function( newValue, oldValue ) {
               $scope.calTotal();
           },true
   );

   $scope.loadEvaludate=function(){
	   if($rootScope.teacherMedia){
		   var p={user_id:$rootScope.getUserId()};
		   p.media_id=$rootScope.teacherMedia.media_id;
	        return Auth.post('teacher_evaluate_form/get', p).success(function (result) { 
	            if (result && result.data.items) {
	            	$scope.count=result.data.count;
	            	$scope.items=result.data.items;
	            	$scope.comments=result.data.comments;
	            }
	        });	
	   }
   }
   
   $scope.loadEvaludate();
}])
.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });
                event.preventDefault();
            }
        });
    };
})
.directive('validateEmail', function() {
  var EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;

  return {
    require: 'ngModel',
    restrict: '',
    link: function(scope, elm, attrs, ctrl) {
      if (ctrl) {
          ctrl.$validators.email = function(modelValue) {
          return ctrl.$isEmpty(modelValue) || EMAIL_REGEXP.test(modelValue); 
        };
      }
    }
  };
})
.directive('validateFile', function () {
	
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            if (ctrl) {
            	var maxsize=1012 * 1024;
            	var EXT_REGEXP = /\.(xls|xlsx)$/i;
                ctrl.$validators.filesize = function(modelValue) {
                	if(modelValue && modelValue.size){
                		return (modelValue.size<=maxsize);
                	}
                	return false; 
                }
                ctrl.$validators.filetype = function(modelValue) {
                	if(modelValue && modelValue.name){
                		return modelValue.name && EXT_REGEXP.test(modelValue.name);
                	}
                	return false; 
                }                
            elm.bind('change', function () {
            	if(elm[0].files && elm[0].files.length){
            		
            		ctrl.$setViewValue(elm[0].files[0]);
            	}else{
            		ctrl.$setViewValue(null);
            	}
            });                
            }
          }
    };
})
.directive('validateNotEmpty', function() {

  return {
    require: 'ngModel',
    restrict: '',
    link: function(scope, elm, attrs, ctrl) {
      if (ctrl) {
    	  var allow0=(attrs.validateNotEmpty=="true");
          ctrl.$validators.notempty = function(modelValue) {
          var b=false;
          if(angular.isString(modelValue)){
        	  modelValue=modelValue.trim();
          }
          if(modelValue){
        	  if(angular.isNumber(modelValue)){
        		  if(allow0 && (modelValue==0)){
        			  b=true;
        		  }else{
        			  b=modelValue>0;
        		  }
        	  }else if(angular.isString(modelValue)){
        		  if(allow0 && (modelValue=="0")){
        			  b=true;
        		  }else{
        			  b=(modelValue!="0") && (b!="-1");
        		  }
        	  }else{
        		  b=true;
        	  }
          }
          return b; 
        };
      }
    }
  };
})
.directive('validateMatch', ['$filter', '$timeout','$parse', function ($filter, $timeout,$parse) {
    /* can't use with track by ...*/
    return {
        require: '?ngModel',
        restrict: 'A',
        link: function(scope, elem, attrs, ctrl) {
            if(!ctrl) {
                if(console && console.warn){
                    console.warn('Match validation requires ngModel to be on the element');
                }
                return;
            }

            var matchGetter = $parse(attrs.match);
            var caselessGetter = $parse(attrs.matchCaseless);

            scope.$watch(getMatchValue, function(){
                ctrl.$$parseAndValidate();
            });

            ctrl.$validators.match = function(){
              var match = getMatchValue();
              if(caselessGetter(scope) && angular.isString(match) && angular.isString(ctrl.$viewValue)){
                return ctrl.$viewValue.toLowerCase() === match.toLowerCase();
              }
              return ctrl.$viewValue === match;
            };

            function getMatchValue(){
                var match = matchGetter(scope);
                if(angular.isObject(match) && match.hasOwnProperty('$viewValue')){
                    match = match.$viewValue;
                }
                return match;
            }
        }
    }
}])
.directive('inputSelected', ['$filter', '$timeout','$parse', function ($filter, $timeout,$parse) {
    /* can't use with track by ...*/
    return {
        require: '?ngModel',
        restrict: 'A',
        link: function(scope, elem, attrs, ctrl) {
            if(!ctrl) {
                return;
            }
            ctrl.$validators.selected = function(){
              return (ctrl.$viewValue != null) && (ctrl.$viewValue !== "");
            };


        }
    }
}])
.directive('selectValueType', ['$filter', '$timeout', function ($filter, $timeout) {
    /* can't use with track by ...*/
    return {
        restrict: 'A',
        replace: false,
        require: '?ngModel',
        link: function (scope, element, attr, ngModel) {
            if (ngModel) {
                var type = (attr.selectValueType || '').toLowerCase();
                if (type == 'number' || type == 'string') {                 
                    $timeout(function () {
                        if (ngModel.$modelValue != undefined) {
                            if ((type == 'number') && (!angular.isNumber(ngModel.$modelValue))) {
                                ngModel.$setViewValue(Number(ngModel.$modelValue));
                            } else if ((type == 'string') && (!angular.isString(ngModel.$modelValue))) {
                                ngModel.$setViewValue(String(ngModel.$modelValue));
                            }
                        }
                    }, 0);
                }
            }
        }
    }
}])

.controller('TeacherMediaCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal', '$popover','Lookups', '_',function ($scope, $timeout, Auth, $state, Lookups, $modal, $popover,$Lookups,_) {
	if(Lookups.hasChanged()) Lookups.load();
    $scope.Lookups = Lookups;
    $scope.pkField = 'media_id';
    $scope.apiName = 'media';
    $scope.editItem = null;

    $scope.saveForm = function (item, idx) {
    	alert($scope.apiName)
        if (item) {
            var d=_.extendOwn({}, item);
            Auth.post($scope.apiName + '/update', d).success(function (data) {
                if(idx){
                	$scope.editingItem = null;
                	$scope.goBack();
                }
            });
        }
    }

    $scope.goBack = function () {
        $state.go('teacher.media')
    }

    $scope.go = function (a, b,c) {
        if (a) {
        
        	if(b){
	            var d = {};
	            d[$scope.pkField] = b[$scope.pkField];
	            $scope.editingItem = null;
	            Auth.post($scope.apiName + '/get', d).success(function (data) {
	                $scope.editingItem = data.data;
	               
	                $state.go(a);
	  
	            });
        	}else{
        		$scope.editingIem=null;
        		if(c){
        			$scope.editingIem=c;
        		}
        		console.dir($scope.editingIem);
        		$state.go(a);

        	}

        }
    }




}])

.controller('TeacherMediaSumaryCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal', '$popover','Lookups', '_','API_URL',function ($scope, $timeout, Auth, $state, Lookups, $modal, $popover,$Lookups,_,API_URL) {
	
    $scope.loadSumary = function (media_id) {
    	var d={media_id};
    	
        Auth.post('media_sumary', d).success(function (data) {
            $scope.sumary = data.data;
        });
    }
}])
.controller('TeacherMediaEvalCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal', '$popover','Lookups', '_','$rootScope',function ($scope, $timeout, Auth, $state, Lookups, $modal, $popover,$Lookups,_,$rootScope) {
    $scope._subject=[];
    $scope._level=[];
    $scope.changeGroup=function(it){
    	if(it){
    		$scope._subject=_.filter(Lookups.getSubject(),function(t){
    			return (t['group_id']==it);
    		});
    		
    	}else{
    		$scope._subject=Lookups.getSubject();
    	}

    }
    $scope.changeTeacher=function(it){
    	if(it){
    		it=Lookups.getTeacher(it)['level_id'];
    		$scope._level=_.filter(Lookups.getLevel(),function(t){
    			return (t['level_id']==it);
    		});
  		
    	}else{
    		$scope._level=Lookups.getLevel();
    	}

    }     
  
    $scope.changeTeacher($rootScope.getUserId());
    $scope.changeGroup();

    
    $scope.viewEvaluate=function(it){
    	if(it){
    		$rootScope.teacherMedia=it;
    		$rootScope.storeData('teacherMedia',$rootScope.teacherMedia);
    		$state.go('teacher.media.evaluate');
    	}
    } 
    $scope.backEvaluate=function(){

    		$state.go('teacher.media');

    } 
}])
.controller('AdminMediaEvalCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal', '$popover','Lookups', '_','$rootScope',function ($scope, $timeout, Auth, $state, Lookups, $modal, $popover,$Lookups,_,$rootScope) {
    $scope._subject=[];
    $scope._level=[];
    $scope.changeGroup=function(it){
    	if(it){
    		$scope._subject=_.filter(Lookups.getSubject(),function(t){
    			return (t['group_id']==it);
    		});
    		
    	}else{
    		$scope._subject=Lookups.getSubject();
    	}

    }
    $scope.changeTeacher=function(it){
    	if(it){
    		it=Lookups.getTeacher(it)['level_id'];
    		$scope._level=_.filter(Lookups.getLevel(),function(t){
    			return (t['level_id']==it);
    		});
  		
    	}else{
    		$scope._level=Lookups.getLevel();
    	}

    }     
  
    $scope.changeTeacher();
    $scope.changeGroup();


}])
.controller('DirectorCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal', '$popover','Lookups', '_','$rootScope','Search',function ($scope, $timeout, Auth, $state, Lookups, $modal, $popover,$Lookups,_,$rootScope,Search) {


	$scope.options = Search.getOptions();
	
	$scope.setFilter=_.debounce(function(){
    	if($scope.Table){
    		$scope.Table.getScope().searchText=$scope.searchText;
    		$scope.Table.getScope().setFilter(true);

    	}
    	$scope.loadStat();
    },200); 
	
	$scope.loadStat=function(){
		
	}
    $scope.setTable=function(tb){
    	$scope.Table=tb;
    }
    $scope.changeGroup=function(it){
    	if(it){
    		$scope._subject=_.filter(Lookups.getSubject(),function(t){
    			return (t['group_id']==it);
    		});
    		$scope._teacher=_.filter(Lookups.getTeacher(),function(t){
    			return (t['group_id']==it);
    		});    		
    	}else{
    		$scope._subject=Lookups.getSubject();
    		$scope._teacher=Lookups.getTeacher();
    	}
    	$scope.setFilter();
    }
    $scope.changeTeacher=function(it){
    	if(it){
    		it=Lookups.getTeacher(it)['level_id'];
    		$scope._level=_.filter(Lookups.getLevel(),function(t){
    			return (t['level_id']==it);
    		});
  		
    	}else{
    		$scope._level=Lookups.getLevel();
    	}
    	$scope.setFilter();
    }    
    $scope._subject=[];
    $scope.getSubject=function(){
    	return $scope._subject;
    }
    $scope._teacher=[];
    $scope.getTeacher=function(){
    	return $scope._teacher;
    }
    $scope.getLevel=function(){
    	return $scope._level;
    }
    $scope.more_details=false;
    $scope.showDetails=function(v){
    	$scope.more_details=!$scope.more_details;
    }
    $scope.changeGroup($scope.options.group);
    $scope.changeTeacher($scope.options.teacher); 

}])
.controller('AdminUserCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal', '$popover','Lookups', '_','$http','API_URL','$filter',function ($scope, $timeout, Auth, $state, Lookups, $modal, $popover,$Lookups,_,$http,API_URL,$filter) {
	$scope._iportModal=null;
	$scope.working=false;
	$scope.importingItem={user_type:'0', active:'1','password':'password'};
	$scope.newImport=function(){
		$scope.hideMessage();
		$scope.working=false;		
		if(!$scope._iportModal){
			$scope._iportModal = $modal({ scope: $scope,  backdrop: 'static', template: 'custom.import.user.html', placement: "top", html: true, show: false });
		}
		$scope._iportModal.$promise.then($scope._iportModal.show);
	}
	$scope.getFileTitle=function(f){
		if(f && f.name && f.size){
			return f.name + ' (' + $filter('number')(f.size/1024,1) + ' KB)' 
		}
		return '';
	}
	$scope.hideMessage=function(){
		if($scope.result){
			$scope.result=null;
		}
	}
	$scope.close=function(){
		$scope.hideMessage();
		$scope.working=false;
	}
	$scope.startImport=function(hlr){
		$scope.hideMessage();
		$scope.working=true;
		$scope.startImportHlr=hlr;
		var fm=new FormData();
		angular.forEach($scope.importingItem,function(v,k){
			fm.append(k,v);
		});

		$http({
			method: 'POST',
			url: API_URL + 'import_user/xls',
			headers: {
			'Content-Type': undefined
			},
			data: fm,
			transformRequest: function(data) { return data; }
			}).success(function(data){
				$scope.result=data.data;
				$scope.working=false;
			}).error(function(data){
				$scope.result=null;
				$scope.working=false;
			});
		
	}
	Lookups.load();
}])
.controller('AdminImportUserCtrl', ['$scope', '$timeout', 'Auth', '$state', 'Lookups', '$modal', '$popover','Lookups', '_',function ($scope, $timeout, Auth, $state, Lookups, $modal, $popover,$Lookups,_) {


}])

.controller('AdminEvaluateGroupCtrl', ['$scope', '$rootScope','$state',function ($scope, $rootScope,$state) {
    $scope.goBack = function () {
        $state.go('admin.evaluate_group')
    }

    $scope.go = function (a,b) {
    	if(a && b){
    	$rootScope.storeData('admin.evaluate_group',b);
        $state.go(a);
    	}
    }

}])
.controller('AdminEvaluateTopicCtrl', ['$scope', '$rootScope','$state',function ($scope, $rootScope,$state) {
	$scope.group_item=$rootScope.fetchData('admin.evaluate_group');
    $scope.goBack = function () {
        $state.go('admin.evaluate_group')
    }
}])

.controller('FilterCtrl', ['$scope', '$rootScope', '$state', function ($scope, $rootScope, $state) {
    $scope.show_filter = true;
    $scope.filterIsShow=function(){
        return $scope.show_filter;
    }
    $scope.toggleFilter = function (v) {
        if (v !== undefined) {
            $scope.show_filter = !!v;
        } else {
            $scope.show_filter = !$scope.show_filter;
        }
    }
}])

.directive('customDateField', ['$filter','$timeout','_', function ($filter,$timeout,_) {
    return {
        restrict: 'CEA',
        replace: false,
        require: '?ngModel',
        scope: {xmodel:'@dataModel', startDate: '=', endDate: '=', mode: "@",placeholder:"@", weekDaysOnly: "@" },

        template: '<div class="input-group date"><input type="text" placeholder="{{placeholder}}" class="form-control"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span></div>',
        link: function (scope, element, attr, ngModel) {
            var _mode = parseInt(scope.mode || 0);
            var _startMode = 0;
            var _minMode = 0;
            var fstr = '';
            if (scope.mode == 2) {
                fstr = "yyyy";
                _startMode = 2;
                _minMode = 2;
            } else if (scope.mode == 1) {
                fstr = "MM yyyy";
                _startMode = 1;
                _minMode = 1;
            } else {
                fstr = "d MM yyyy";
                _startMode = 0;
                _minMode = 0;
                scope.mode = 0;
            }
            var opt = {
                format: fstr,
                todayBtn: "linked",
                startView: _startMode,
                minViewMode: _minMode,
                orientation: "top left",
                clearBtn: true,
                language: "th",
                autoclose: true,
                todayHighlight: true,
                toggleActive: true,
                forceParse: false
            };
            if (scope.weekDaysOnly) {
                opt.daysOfWeekDisabled = "0,6";
            }

            var thfun = $filter('thai_date');

            var textInput = element.find('input:first');
            var element2=element.find('.input-group.date:first');
            var trotle=_.debounce(function () {
                if (ngModel) {

                    var dt = element2.datepicker('getDate');
                    var v = '';
                    if (dt) {
                        if (scope.mode == 1 || scope.mode == 2) {
                            if (dt.getDate() != 1) {
                                dt.setDate(1);
         
                                element2.datepicker('setDate', dt);
                               
                            }
                        }
                        v = $filter('date')(dt, 'yyyy-MM-dd');
                    }
                   
                    if (ngModel.$viewValue != v) {
                    
                    	ngModel.$setViewValue(v,false);
                    	
   

                        //if (scope.change) scope.change();
                        //if (dt) textInput.val(thfun(dt, scope.mode));
                    };
                }
            },100)
            
            
            element2.datepicker(opt).on('changeDate', trotle)
           
            if (ngModel) {
            	$timeout(function(){
                ngModel.$render = function () {
                    var dt = null;
                    if (ngModel.$modelValue) dt = new Date(ngModel.$modelValue);
                    element2.datepicker('setDate', dt);
                    element2.datepicker('update');

                   // if (dt) textInput.val(thfun(dt, scope.mode));
                }
            	},100);
            }
           
            textInput.keydown(function (e) {
                e.preventDefault();
            })
            
            var v = null;
            if (scope.startDate) {
                v = new Date(scope.startDate);
            }
            element2.datepicker('setStartDate', v);
            v = null;
            if (scope.endDate) {
                v = new Date(scope.endDate);
            }
            element2.datepicker('setEndDate', v);

        }
    }
}])

.directive('autofocus', ['$timeout','$rootScope',function($timeout,$rootScope) {
      return {
        restrict: 'A',
        link: function($scope, element) {
        	
          $rootScope._autoFocusElement=element[0];
          $timeout(function(){
        	  if($rootScope._autoFocusElement){
        		  if(angular.isElement($rootScope._autoFocusElement)){
        			  try{
        				 $rootScope._autoFocusElement.focus();
        			  }catch(err) {}
        		  }
        		  $rootScope._autoFocusElement=null;
        	  }
          },100);
        }
      }
}])
.directive('customMedia', ['$filter', '$timeout','$window','$compile',function ($filter, $timeout,$window, $compile) {
    return {
        restrict: 'A', 

        replace: false, 
        link: function (scope, element, attrs) {
        	scope.isAudio=function(url){
        		return ((url || '').search(/\.(m4a|mp3|fla|webma|rtmpa|oga)$/i)>=0);
        	} 
        	scope.isVideo=function(url){
        		return ((url || '').search(/\.(flv|mp4|m4v|mov|webmv|rtmpv|ogv)$/i)>=0);
        	}          	
        	scope.isSwf=function(url){
        		return ((url || '').search(/\.swf$/i)>=0);
        	}  
        	scope.size={w:0,h:0};
        	scope.toUrl=function(str){
        		if(str.search(/^http[s]{0,1}:\/\//i)>=0){
        			return str;
        		}else{
        			str='uploads/' + scope.author + '/' + str;
        			if(scope.version){
        				if(str.indexOf('?')>0){
        					str=str + "&";
        				}else{
        					str=str + "?";
        				}
        				str=str + 'v=' + $filter('date_version')(scope.version);
        			}
        			return str;
        		}
        	} 

        	var ext=(scope.isAudio(scope.url)) ? 'mp3':'m4v';
        	var media={
    				title: scope.title || '',
    				artist: $filter('lookup_teacher')(scope.author) || '',
    		};
        	if(scope.poster){
        		scope.poster=scope.toUrl(scope.poster);
        		media['poster']=scope.poster;
        	}
        	media[ext]=scope.toUrl(scope.url);

        	var str=media[ext];
        	if(str){
        		if(str.search('www.youtube.com')>=0){
	        		var match=str.match(/[.]com\/embed\/([\w\d]{1,})/);
	        		if(!match) match=str.match(/\bv=([\w|\d]{1,})/);
	        		if(match){
				        var you=$('<div><div class="embed-responsive embed-responsive-16by9"><img class="img" onerror="this.src=\'images/thumb.png\'" width="100%" src="' + scope.poster + '"/><iframe   class="hidden embed-responsive-item" src="//www.youtube.com/embed/'+ match[1] + '?autoplay=0"  frameborder="0" allowfullscreen></iframe></div> <div id="btn-play" class="btn" style="position:relative;top:-200px;left:260px;z-index:100;color:#2fef2f"><span class="fa fa-youtube-play fa-5x"></span></div></div>');
				        $(element).empty().append(you);
				       // $compile(element.contents())(scope);
				       
				        $('#btn-play',you).bind('click',function(evt){
				        	var btn=$(evt.currentTarget);
				        	btn.unbind('click');
				        	scope.start();
				        	var tar=$('iframe',you);
				        	var img=$('img',you);
				        	var str=tar.attr('src');
				        	var str2=str.replace('?autoplay=0','?autoplay=1');
				        	if(str!=str2){
				        		tar.attr('src',str2); 
				        	}
				        	
				        	btn.hide(); 
				        	img.hide();
				        	$timeout(function(){
				        		tar.removeClass('hidden');
				        	},1000);
				        });
				        
				        return;
	        		}
        		}else if(!(scope.isAudio(scope.url) || scope.isVideo(scope.url))){
			        var you=$('<div><div class="embed-responsive embed-responsive-16by9" style="border:0px solid #cfcfcf;"><img class="img" onerror="this.src=\'images/thumb.png\'" width="100%" src="' + scope.poster + '"/></div> <div id="btn-play" class="btn" style="position:relative;top:-200px;left:260px;z-index:100;color:#2fef2f"><span class="fa fa-youtube-play fa-5x"></span></div></div>');
			        $(element).empty().append(you);
			        $('#btn-play',you).bind('click',function(evt){
			        	var btn=$(evt.currentTarget);
			        	//btn.unbind('click');
			        	scope.start();
			        	$window.open(scope.toUrl(scope.url),'_blank');
			        })
			        return;
        		}
        	}
        	$("#jquery_jplayer_1",element).jPlayer({
        		ready: function () {
        			$(this).jPlayer("setMedia", media);
        		},
        		swfPath: "jplayer",
        		solution: "flash, html",
        		supplied: "mp3, mp4, m4v",
        		size: {
        			width: "640px",
        			height: "360px",
        			cssClass: "jp-video-360p"
        		},
        		useStateClassSkin: true,
        		autoBlur: false,
        		smoothPlayBar: true,
        		keyEnabled: true,
        		remainingDuration: true,
        		toggleDuration: true
        	}); 
        	
        	$("#jquery_jplayer_1",element).bind($.jPlayer.event.play,function(){
        		if(!scope._started){
        			scope._started=true;
        			scope.start();
        		}
        	});
        	

        },

        scope: {
            url: '@',
            title:'@',
            poster: '@',
            author:'@',
            version:'@',
            start:'&'
        },
        templateUrl: 'views/custom_media_player.html' 
    }
}])


.directive("starRating", ['$timeout','_',function($timeout,_) {
  return {
    restrict : "EA",
    template : "<ul class='rating' ng-class='{readonly: readonly}'>" +
               "  <li ng-repeat='star in stars' ng-class='star' xxng-click='toggle($index)'>" +
               "    <i class='fa  {{size}}' ng-class='star.filled'></i>" +
               "  </li>" +
               "</ul>",
    scope : {
      ratingValue : "=ngModel",
      max : "=", //optional: default is 5
      size : "@starSize", 
      onRatingSelected : "&change",
      readonly: "="
    },
    link : function(scope, elem, attrs) {
      if (scope.max == undefined) { scope.max = 5; }
      if (scope.size == undefined) { scope.size=''; }
      scope.stars = [];

      var updateStars=_.debounce(function() {
    	var v=parseFloat(scope.ratingValue);
        scope.stars.length=0;
        scope.mod=Math.floor(v);
        var n=Math.min(scope.max, scope.mod);
        for (var i = 0; i < scope.max; i++) {
        	var str='';
        	if(i<n){
        		str='fa-star';
        	}else if((i==scope.mod)){
        		
        			str=((scope.mod==v))?'fa-star-o':'fa-star-half-empty';

        	}else{
        		str='fa-star-o';
        	}
        	scope.stars.push({filled : str}); 
        }
      },100);

      scope.toggle = function(index) {
        if (scope.readonly != true){
          var i=index + 1;
          if((i==1) && (i==scope.ratingValue))i=0;
          if(scope.ratingValue != i){
	          scope.ratingValue = i;
	          $timeout(function(){
	        	  scope.onRatingSelected(i);
	          });
          }
        }
      };
      scope.$watch("ratingValue", function(oldVal, newVal) {
    	  updateStars(); 
      });
      
      updateStars();
    }
  }
}])

.directive('elementResize',function ($window, _) {
        return {

        	restrict: 'EA',
            scope: {
            	size:'=size'
            },
            link: function (scope, $element, attrs, ctrl) {
            	scope.size={};
            	var hlr=_.debounce(function(){
            		scope.$apply(function(){
                    scope.size.windowWidth=w.width();
                    scope.size.windowHeight=w.height();
                    scope.size.width=$element.width();
                    scope.size.height=$element.height();  
            		});
            	},100);
            	hlr();
                var w = angular.element($window);
                w.bind('resize', function () {
                	hlr();
                });
            }
        }
})

.directive('iosToggle', function () {
	 var toggle = ['$scope', '$timeout', function ($scope, $timeout) {
         $scope.toggle = function toggle() {
             if (!$scope.disabled) {
                 $scope.model=($scope._model.$viewValue==$scope.trueValue)?$scope.falseValue:$scope.trueValue;
                 if($scope._model) $scope._model.$setViewValue($scope.model);
             }
         };
     }];	
        return {
            template: '<div ng-click="toggle()" ng-class="{coActive: model === trueValue, disabled: disabled}"></div>',
            restrict: 'EA',
            require: '?ngModel',
            controller: toggle,
            scope: {
                model: '=ngModel',
                disabled: '=ngDisabled',
                trueValue: '=ngTrueValue',
                falseValue: '=ngFalseValue'
            },
            link: function (scope, $element, attrs, ctrl) {
                scope.trueValue = scope.trueValue || true;
                scope.falseValue = scope.falseValue || false;

                // Add class to outer element (no replace)
                $element.addClass('ios-toggle');
                if(scope.disabled){
                	$element.addClass('disabled');
                }else{
                	$element.removeClass('disabled');
                }
                scope._model=ctrl;
            }
        };
    });
