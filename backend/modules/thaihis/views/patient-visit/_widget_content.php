<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$template_content = '';
$sub_widget_id = $valTab['widget_id'];
$widget_ops = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($sub_widget_id);
$template_content .= "<div id='show-content-tab$key_gen-$widget_id'>";
$template_content .= $this->render($widget_ops['widget_render'], ['widget_config' => $widget_ops, 'modal' => $modal]);
$template_content .= "</div>";

echo $template_content;