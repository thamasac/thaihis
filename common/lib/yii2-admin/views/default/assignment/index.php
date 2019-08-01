<?php

use dee\angular\NgView;

/* @var $this yii\web\View */
/* @var $widget NgView */
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('rbac-admin', 'Assignment')?></h3>
    </div>
    <div class="panel-body">
        <div class="grid-view">
            <table class="table table-striped">
		<thead>
		    <tr>
			<td width="35px"></td>
			<td width="300px">
			    <div class="has-feedback">
				<input type="text" class="form-control input-sm" placeholder="Search" ng-model="q"
				       ng-change="search()">
				<span class="glyphicon glyphicon-search form-control-feedback"></span>
			    </div>
			</td>
			<td></td>
			<td></td>
		    </tr>
		</thead>
                <tbody>
                    <tr ng-repeat="model in rows">
                        <td width="35px;">{{(provider.page - 1) * provider.itemPerPage + $index + 1}}</td>
                        <td>{{model.username}}</td>
                        <td><a ng-repeat="role in model.assignments" ng-href="#{{role.type == 1?'role':'permission'}}/{{role.name}}">
                                <span class="label" ng-class="{'label-danger':role.type == 1,'label-success':role.type == 2}">{{role.name}}</span></a>
                        </td>
                        <td width="40px">
                            <a ng-href="#/assignment/{{model.id}}"><span class="glyphicon glyphicon-eye-open"></span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <pagination total-items="provider.totalItems" ng-model="provider.page"
                        max-size="3" items-per-page="provider.itemPerPage"
                        ng-change="provider.paging()" direction-links="false"
                        first-text="&laquo;" last-text="&raquo;"
                        class="pagination-sm" boundary-links="true"></pagination>
        </div>
    </div>
</div>