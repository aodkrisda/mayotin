<!DOCTYPE html>
<html lang="en"  ng-app="App">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Haris Institude and Innovative Media Education</title>

	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="js/ng-table/ng-table.min.css">
	<link rel="stylesheet" type="text/css" href="js/angular-strap/angular-motion.css">
	<link rel="stylesheet" type="text/css" href="js/loading-bar/loading-bar.min.css">
	<link rel="stylesheet" type="text/css" href="js/date/css/bootstrap-datepicker3.css">
	<link rel="stylesheet" type="text/css" href="jplayer/skin/pink.flag/css/jplayer.pink.flag.min.css">

	
	<script src="js/lib/modernizr.min.js"></script>
	<script src="js/lib/underscore.min.js"></script>
	<script src="js/lib/jquery.min.js"></script>
	<script src="js/notify/notify.min.js"></script>
	<script src="js/moment/moment.min.js"></script>
	<script src="js/moment/th.js"></script>
	<script src="js/date/js/bootstrap-datepicker.min.js"></script>
	<script src="js/date/locales/bootstrap-datepicker.th.min.js"></script>

	<script src="js/lib/angular.min.js"></script>
	<script src="js/lib/angular-animate.min.js"></script>
	<script src="js/lib/angular-sanitize.min.js"></script>
	<script src="js/lib/angular-touch.min.js"></script>
	<script src="js/lib/angular-ui-router.min.js"></script>
	<script src="js/lib/angular-storage.min.js"></script>
	<script src="js/lib/chart.min.js"></script>
	<script src="js/lib/angular-chart.js"></script>
	<script src="js/loading-bar/loading-bar.min.js"></script>
	<script src="js/ng-table/ng-table.js"></script>
	<script src="js/angular-strap/angular-strap.min.js"></script>
	<script src="js/angular-strap/angular-strap.tpl.min.js"></script>
	<script src="js/custom.table.js"></script>
	<script src="js/lib/ng-flow-standalone.min.js"></script>
	<script src="jplayer/jquery.jplayer.min.js"></script>
	<script src="jplayer/add-on/jplayer.playlist.min.js"></script>

	<script src="js/highcharts/highcharts.js"></script>
	<script src="js/highcharts/highcharts-ng.min.js"></script>
	<script src="js/app.js"></script>

	<style>
		.img.img-responsive.xthumb {height:160px !important;}
		.text-help{font-size:1.1em}
		.ng-table th {text-align: left;}
		.ng-table th.text-center {text-align:center;}
		.ng-table th.text-right {text-align:right;}
		.ng-table th.text-left {text-align:left;}
		.ng-table th.sortable.sort-desc,.ng-table th.sortable.sort-asc {background-color:transparent;color:grey;}
		.modal-backdrop {opacity: 0.8;} 
		.aside-backdrop {opacity: 0.8;} 
		.drop {padding: 15px;border: 2px #f1f1f1 dashed;border-radius: 5px;line-height: 34px;}
		.drop.drag-over {background: #5CB85C;color: #fff;}
		form.ng-dirty .ng-invalid     {background-color:#ffefef;}
		ul.rating {margin: 0; padding: 0; display: inline-block; }
		ul.rating li { padding: 1px; color: #ddd; font-size: 20px; text-shadow: .05em .05em #aaa; list-style-type: none; display: inline-block; cursor: pointer; }
		ul.rating li.filled { color:rgba(0,255,0,0.5); }
		ul.rating.readonly li.filled { color: #666; }
		.fa.active{color:#00bb00;}
		.fa.inactive{color:#cc0000;}
.file-upload {
    position: relative;
    overflow: hidden;
    margin: 10px;
}
.file-upload input.upload {
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    padding: 0;
    font-size: 20px;
    cursor: pointer;
    opacity: 0;
    filter: alpha(opacity=0);
}
.row-centered {
    text-align:center;
}
.col-centered {
    display:inline-block;
    float:none;
    /* reset the text-align */
    text-align:left;
    /* inline-block space fix */
    margin-right:-4px;
}		
		
/* line 7, ../ios-toggle.scss */
.ios-toggle {
  display: inline-block;
  vertical-align: middle;
}
/* line 11, ../ios-toggle.scss */
.ios-toggle > div {
  margin: 0;
  padding: 0;
  border: none;
  display: inline-block;
  height: 35px;
  width: 59.23077px;
  position: relative;
  cursor: pointer;
  vertical-align: middle;
  background-color: #fafafa;
  border: 1px solid #d3d3d3;
  -moz-border-radius: 35px;
  -webkit-border-radius: 35px;
  border-radius: 35px;
  -moz-box-shadow: inset 0 0 0 1px #d3d3d3;
  -webkit-box-shadow: inset 0 0 0 1px #d3d3d3;
  box-shadow: inset 0 0 0 1px #d3d3d3;
  -moz-transition: border 0.25s 0.15s, box-shadow 0.25s 0.3s, padding 0.25s;
  -o-transition: border 0.25s 0.15s, box-shadow 0.25s 0.3s, padding 0.25s;
  -webkit-transition: border 0.25s, box-shadow 0.25s, padding 0.25s;
  -webkit-transition-delay: 0.15s, 0.3s, 0s;
  transition: border 0.25s 0.15s, box-shadow 0.25s 0.3s, padding 0.25s;
}
/* line 36, ../ios-toggle.scss */
.ios-toggle > div:after {
  content: '';
  display: block;
  height: 33px;
  position: absolute;
  left: 0;
  right: 22.23077px;
  top: 0;
  background-color: #fff;
  border: 1px solid #d3d3d3;
  -moz-border-radius: 33px;
  -webkit-border-radius: 33px;
  border-radius: 33px;
  -moz-box-shadow: inset 0 -3px 3px rgba(0, 0, 0, 0.025), 0 1px 4px rgba(0, 0, 0, 0.15), 0 4px 4px rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: inset 0 -3px 3px rgba(0, 0, 0, 0.025), 0 1px 4px rgba(0, 0, 0, 0.15), 0 4px 4px rgba(0, 0, 0, 0.1);
  box-shadow: inset 0 -3px 3px rgba(0, 0, 0, 0.025), 0 1px 4px rgba(0, 0, 0, 0.15), 0 4px 4px rgba(0, 0, 0, 0.1);
  -moz-transition: border 0.25s 0.15s, left 0.25s 0.1s, right 0.15s 0.175s;
  -o-transition: border 0.25s 0.15s, left 0.25s 0.1s, right 0.15s 0.175s;
  -webkit-transition: border 0.25s, left 0.25s, right 0.15s;
  -webkit-transition-delay: 0.15s, 0.1s, 0.175s;
  transition: border 0.25s 0.15s, left 0.25s 0.1s, right 0.15s 0.175s;
}
/* line 59, ../ios-toggle.scss */
.ios-toggle > div.coActive {
  border-color: #53d76a;
  -moz-box-shadow: inset 0 0 0 18.5px #53d76a;
  -webkit-box-shadow: inset 0 0 0 18.5px #53d76a;
  box-shadow: inset 0 0 0 18.5px #53d76a;
  -moz-transition: border 0.25s, box-shadow 0.25s, padding 0.25s 0.15s;
  -o-transition: border 0.25s, box-shadow 0.25s, padding 0.25s 0.15s;
  -webkit-transition: border 0.25s, box-shadow 0.25s, padding 0.25s;
  -webkit-transition-delay: 0s, 0s, 0.15s;
  transition: border 0.25s, box-shadow 0.25s, padding 0.25s 0.15s;
}
/* line 67, ../ios-toggle.scss */
.ios-toggle > div.coActive:after {
  border-color: #53d76a;
  left: 22.23077px;
  right: 0;
  -moz-transition: border 0.25s, left 0.15s 0.25s, right 0.25s 0.175s;
  -o-transition: border 0.25s, left 0.15s 0.25s, right 0.25s 0.175s;
  -webkit-transition: border 0.25s, left 0.15s, right 0.25s;
  -webkit-transition-delay: 0s, 0.25s, 0.175s;
  transition: border 0.25s, left 0.15s 0.25s, right 0.25s 0.175s;
}
.ios-toggle.disabled{
opacity:0.4;
}
.comment.disabled{
opacity:0.4;
}	
	</style>
<script type="text/ng-template" id="custom.confirm.delete.html">
    <div class="modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-label="Close" ng-click="$hide()"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">ยืนยันการลบข้อมูล</h4>
                </div>
                <div class="modal-body" ng-init="_confirm=false" style="padding-left:5em;padding-right:5em">
                	<strong class="text-info">มีการอ้างอิงถึงข้อมูลที่คุณกำลังจะลบ ถ้าคุณลบข้อมูลนี้ ข้อมูลส่วนอื่นๆที่อ้างอิงถึงข้อมูลนี้จะถูกลบออกด้วย หรือการอ้างอิงจะแสดงไม่ถูกต้อง</strong><br><br>
                  <div ios-toggle ng-model="_confirm" ng-true-value="true" ng-false-value="false" ></div>
                  <strong class="text-danger">ยืนยัน ต้องการลบข้อมูลนี้และข้อมูลที่อ้างอิงถึงทั้งหมด</strong>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" ng-disabled="!_confirm" ng-click="$hide();confirm_delete()">ลบข้อมูล</button>
                    <button type="button" class="btn btn-warning" ng-click="$hide()">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/ng-template" id="custom.confirm.delete2.html">
    <div class="modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-label="Close" ng-click="$hide()"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">การลบข้อมูล</h4>
                </div>
                <div class="modal-body">
                	
                  <strong class="text-info">คุณไม่สามารถลบข้อมูลนี้ได้ เพราะมีการอ้างอิงใช้งานอยู่</strong>

                </div>
                <div class="modal-footer">
                   
                    <button type="button" class="btn btn-info" ng-click="$hide()">ตกลง</button>
                </div>
            </div>
        </div>
    </div>
</script>
		<script type="text/ng-template" id="custom.confirm.popover.html">
			<div class="popover">
				<div class="arrow"></div>
				<h3 class="popover-title" >คุณต้องการลบข้อมูลใช่หรือไม่</h3>
				<div class="popover-content">
					<p class="text-center">
						<button type="button" class="btn btn-danger" ng-click="doRemove();$hide()">ลบข้อมูล</button>
						<button type="button" class="btn btn-primary" ng-click="$hide()">ยกเลิก</button>
					</p>
				</div>
			</div>
		</script>

		<script type="text/ng-template" id="custom.confirm2.popover.html">
			<div class="popover">
				<div class="arrow"></div>
				<h3 class="popover-title">คุณต้องการลบข้อมูลรายการที่เช็คทั้งหมด ใช่หรือไม่</h3>
				<div class="popover-content">
					<p class="text-center">
						<button type="button" class="btn btn-danger" ng-click="doRemoveChecked();$hide()">ลบทั้งหมด</button>
						<button type="button" class="btn btn-primary" ng-click="$hide()">ยกเลิก</button>
					</p>
				</div>
			</div>
		</script>

		<script type="text/ng-template" id="custom.dropdown.actions.html">
			<ul tabindex="-1" class="dropdown-menu" role="menu">
				<li> <a ng-click="selectAll()" href="javascript://void(0)"><span class="glyphicon glyphicon-check"></span> เลือกทั้งหมด</a></li>
				<li><a ng-click="selectInverse()" href="javascript://void(0)"><span class="glyphicon glyphicon-ok-sign"></span> เลือกกลับกัน</a></li>
				<li><a ng-click="selectNone()" href="javascript://void(0)"><span class="glyphicon glyphicon-ban-circle"></span> ยกเลิกการเลือกทั้งหมด</a></li>
				<li ng-if="hasChecked()" class="divider"></li>
				<li ng-if="hasChecked()"><a ng-click="removeChecked()" href="javascript://void()"><span class="glyphicon glyphicon-remove"></span> ลบข้อมูลที่เลือก</a></li>
			</ul>
		</script>
		<script type="text/ng-template" id="custom.dropdown.actions.user.html">
			<ul tabindex="-1" class="dropdown-menu" role="menu">
				<li> <a ng-click="selectAll()" href="javascript://void(0)"><span class="glyphicon glyphicon-check"></span> เลือกทั้งหมด</a></li>
				<li><a ng-click="selectInverse()" href="javascript://void(0)"><span class="glyphicon glyphicon-ok-sign"></span> เลือกกลับกัน</a></li>
				<li><a ng-click="selectNone()" href="javascript://void(0)"><span class="glyphicon glyphicon-ban-circle"></span> ยกเลิกการเลือกทั้งหมด</a></li>
				<li ng-if="hasChecked()" class="divider"></li>
				<li ng-if="hasChecked()"><a ng-click="removeChecked()" href="javascript://void(0)"><span class="glyphicon glyphicon-remove"></span> ลบข้อมูลที่เลือก</a></li>
				<li ng-if="hasChecked()"><a ng-click="statusChecked(1)" href="javascript://void(0)"><span class="fa fa-user"></span> อนุญาติให้ใช้งาน</a></li>
				<li ng-if="hasChecked()"><a ng-click="statusChecked(0)" href="javascript://void(0)"><span class="fa fa-user inactive"></span> ไม่อนุญาติให้ใช้งาน</a></li>
			</ul>
		</script>
		<script type="text/ng-template" id="custom.pages.html">
			<div class="ng-cloak ng-table-pager" ng-if="params.data.length"> <div ng-if="params.settings().counts.length" class="ng-table-counts btn-group pull-right"> <button ng-repeat="count in params.settings().counts" type="button" ng-class="{\'active\':params.count()==count}" ng-click="params.count(count)" class="btn btn-default"> <span ng-bind="count"></span> </button> </div> <ul class="pagination ng-table-pagination"> <li ng-class="{\'disabled\': !page.active && !page.current, \'active\': page.current}" ng-repeat="page in pages" ng-switch="page.type"> <a ng-switch-when="prev" ng-click="params.page(page.number)" href="">&laquo;</a> <a ng-switch-when="first" ng-click="params.page(page.number)" href=""><span ng-bind="page.number"></span></a> <a ng-switch-when="page" ng-click="params.page(page.number)" href=""><span ng-bind="page.number"></span></a> <a ng-switch-when="more" ng-click="params.page(page.number)" href="">&#8230;</a> <a ng-switch-when="last" ng-click="params.page(page.number)" href=""><span ng-bind="page.number"></span></a> <a ng-switch-when="next" ng-click="params.page(page.number)" href="">&raquo;</a> </li> </ul> </div>
		</script>


		<script type="text/ng-template" id="custom.pages.html">
			<div class="row panel-footer">
				<div class="col-sm-7">
					<div ng-if="params.data.length==0  && params.ready"><h2 style="padding:0px;margin:0px;color:#777777"><i class="fa fa-exclamation-triangle fa-lg"></i> ไม่พบข้อมูล</h2></div>

					<div ng-table-pager class="ng-cloak" ng-if="params.data.length">
						<ul class="pagination ng-table-pagination" style="margin:0px">
							<li ng-class="{disabled: !page.active && !page.current, active: page.current}" ng-repeat="page in pages" ng-switch="page.type">
								<a ng-switch-when="prev" ng-click="params.page(page.number)" href="">&laquo;</a>
								<a ng-switch-when="first" ng-click="params.page(page.number)" href=""><span ng-bind="page.number"></span></a>
								<a ng-switch-when="page" ng-click="params.page(page.number)" href=""><span ng-bind="page.number"></span></a>
								<a ng-switch-when="more" ng-click="params.page(page.number)" href="">&#8230;</a>
								<a ng-switch-when="last" ng-click="params.page(page.number)" href=""><span ng-bind="page.number"></span></a>
								<a ng-switch-when="next" ng-click="params.page(page.number)" href="">&raquo;</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-sm-5">
					<div class="btn-group pull-right">
						<button ng-repeat="it in params.settings().counts track by it" type="button" ng-class="{'active':params.count() == it}" ng-click="params.count(it)" class="btn btn-default"> {{it}}</button>
					</div>
				</div>
			</div>
		</script>

		<script type="text/ng-template" id="custom.checked.html">
			<span id="checked-actions" class="glyphicon glyphicon-th" data-animation="am-flip-x" bs-dropdown="actions" aria-haspopup="true" aria-expanded="false" data-template="custom.dropdown.actions.html"></span>
		</script>
		
		<script type="text/ng-template" id="custom.checked.user.html">
			<span id="checked-actions" class="glyphicon glyphicon-th" data-animation="am-flip-x" bs-dropdown="actions" aria-haspopup="true" aria-expanded="false" data-template="custom.dropdown.actions.user.html"></span>
		</script>		
  </head>
  	<body style="background:rgb(64,102,159)">


			<div class="text-center page-header well hidden-print" style="margin:0px;border-radius:0px;background:rgb(64,102,159);border:none;color:#ffffff">
				<h2 style="margin:0px;">ระบบจัดการสื่อการเรียนรู้มัลติมีเดีย<br><small style="color:#ffffff"">โรงเรียนปรินส์รอยแยลส์วิทยาลัย</small></h2>
			</div>
			<div class="hidden-print" id="xx-home" style="position:absolute;left:1em; top:1em;opacity:0.01">
				<i class="fa fa-university fa-5x" ></i>
			</div>
			<div  class="container-fluid" style="background:url('images/paper.jpg') repeat">
				<div ui-view></div>
				<div id="main_modal-dialog"></div>
			</div>


			<div id="#buttom" class="text-center page-header hidden-print" style="padding:1em;margin:0px;border-radius:0px;background:rgb(64,102,159);border:none;color:#ffffff">
				© 2015 โรงเรียนปรินส์รอยแยลส์วิทยาลัย 117 ถนนแก้วนวรัฐ ตำบลวัดเกต อำเภอเมือง เชียงใหม่ 
			</div>
	</body>

</html>