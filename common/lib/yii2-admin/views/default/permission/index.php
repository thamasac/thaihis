<?php

use dee\angular\NgView;

/* @var $this yii\web\View */
/* @var $widget NgView */
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('rbac-admin', 'Permissions')?></h3>
    </div>
    <div class="panel-body">
        <alert ng-repeat="alert in alerts" type="{{alert.type}}" close="closeAlert($index)"
               dismiss-on-timeout="{{alert.timeout}}">{{alert.msg}}</alert>
        <div class="grid-view">
            <table class="table table-striped">
		<thead>
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
                        <button class="btn btn-sm btn-primary" ng-click="openModal()">
                            <span class="fa fa-plus"></span> </button>
                    </td>
                </tr>
            </thead>
                <tbody>
                    <tr ng-repeat="model in filtered.slice(provider.offset, provider.offset + provider.itemPerPage)">
                        <td width="35px">{{provider.offset + $index + 1}}</td>
                        <td><span class="label label-success">{{model.name}}</span></td>
                        <td>{{model.description}}</td>
                        <td width="60px">
                            <a ng-href="#/permission/{{model.name | escape}}"><span class="glyphicon glyphicon-eye-open"></span></a>
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