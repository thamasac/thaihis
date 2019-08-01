<?php

use dee\angular\NgView;

/* @var $this yii\web\View */
/* @var $widget NgView */
?>
<div class="panel panel-primary">
    <div class="panel-heading" style="position: relative;">
        <h3 class="panel-title"><?= Yii::t('rbac-admin', 'Menu')?></h3>
	<div class="panel-tools">
	    <button class="btn btn-sm btn-default" ng-click="openModal({})">
				<span class="fa fa-plus"></span></button>
	</div>
    </div>
    <div class="panel-body">
        <alert ng-repeat="alert in alerts" type="{{alert.type}}" close="closeAlert($index)"
               dismiss-on-timeout="{{alert.timeout}}">{{alert.msg}}</alert>
        <div class="grid-view">
            <table class="table table-striped">
		
                <thead>
                    <tr>
                        <th></th>
                        <th><?= Yii::t('rbac-admin', 'Name')?></th>
                        <th><?= Yii::t('rbac-admin', 'Parent')?></th>
                        <th><?= Yii::t('rbac-admin', 'Route')?></th>
                        <th><?= Yii::t('rbac-admin', 'Order')?></th>
			<th></th>
                    </tr>
		    <tr>
			<td width="30px"></td>
			<td width="300px">
			    <div class="has-feedback">
				<input type="text" class="form-control input-sm" placeholder="Search" ng-model="q"
				       ng-change="filter()">
				<span class="glyphicon glyphicon-search form-control-feedback"></span>
			    </div>
			</td>
			<td>
			    
			</td>
			<td></td>
		    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="model in filtered.slice(provider.offset, provider.offset + provider.itemPerPage)">
                        <td width="35px">{{provider.offset + $index + 1}}</td>
                        <td>{{model.name}}</td>
                        <td>{{model.parentName}}</td>
                        <td>{{model.route}}</td>
                        <td>{{model.order}}</td>
                        <td width="60px">
                            <a href ng-click="openModal(model)"><span class="glyphicon glyphicon-pencil"></span></a>
                            <a href ng-click="deleteItem(model)"><span class="glyphicon glyphicon-trash"></span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <pagination total-items="filtered.length" ng-model="provider.page"
                        max-size="3" items-per-page="provider.itemPerPage"
                        ng-change="provider.paging()" direction-links="false"
                        first-text="&laquo;" last-text="&raquo;"
                        class="pagination-sm" boundary-links="true"></pagination>
        </div>
    </div>
</div>