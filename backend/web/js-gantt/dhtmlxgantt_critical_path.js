
gantt._formatLink=function(t){var e=[],n=this._get_link_target(t),a=this._get_link_source(t);if(!a||!n)return e;if(gantt.isChildOf(a.id,n.id)&&gantt._isProject(n)||gantt.isChildOf(n.id,a.id)&&gantt._isProject(a))return e;for(var i=this._getImplicitLinks(t,a,function(t){return 0}),r=gantt.config.auto_scheduling_move_projects,s=this._isProject(n)?this.getSubtaskDates(n.id):{start_date:n.start_date,end_date:n.end_date},o=this._getImplicitLinks(t,n,function(t){return r?t.$target.length?0:gantt.calculateDuration(gantt.roundDate(s.start_date),gantt.roundDate(t.start_date)):0;
}),_=0;_<i.length;_++)for(var l=i[_],d=0;d<o.length;d++){var g=o[d],h=l.lag+g.lag,c={id:t.id,type:t.type,source:l.task,target:g.task,lag:(t.lag||0)+h};e.push(gantt._convertToFinishToStartLink(g.task,c,a,n))}return e},gantt._getImplicitLinks=function(t,e,n){var a=[];return this._isProject(e)?this.eachTask(function(t){this._isProject(t)||a.push({task:t.id,lag:n(t)})},e.id):a.push({task:e.id,lag:0}),a},gantt._getDirectDependencies=function(t,e){for(var n=[],a=[],i=e?t.$source:t.$target,r=0;r<i.length;r++){
var s=this.getLink(i[r]);this.isTaskExists(s.source)&&this.isTaskExists(s.target)&&n.push(this.getLink(i[r]))}for(var r=0;r<n.length;r++)a=a.concat(this._formatLink(n[r]));return a},gantt._getInheritedDependencies=function(t,e){var n=[],a=[];if(this.isTaskExists(t.id)){this._eachParent(function(t){this._isProject(t)&&a.push.apply(a,this._getDirectDependencies(t,e))},t.id,this);for(var i=0;i<a.length;i++){var r=e?a[i].source:a[i].target;r==t.id&&n.push(a[i])}}return n},gantt._getDirectSuccessors=function(t){
return this._getDirectDependencies(t,!0)},gantt._getInheritedSuccessors=function(t){return this._getInheritedDependencies(t,!0)},gantt._getDirectPredecessors=function(t){return this._getDirectDependencies(t,!1)},gantt._getInheritedPredecessors=function(t){return this._getInheritedDependencies(t,!1)},gantt._getSuccessors=function(t){return this._getDirectSuccessors(t).concat(this._getInheritedSuccessors(t))},gantt._getPredecessors=function(t){return this._getDirectPredecessors(t).concat(this._getInheritedPredecessors(t));
},gantt._convertToFinishToStartLink=function(t,e,n,a){var i={target:t,link:gantt.config.links.finish_to_start,id:e.id,lag:e.lag||0,source:e.source,preferredStart:null},r=0;switch(e.type){case gantt.config.links.start_to_start:r=-n.duration;break;case gantt.config.links.finish_to_finish:r=-a.duration;break;case gantt.config.links.start_to_finish:r=-n.duration-a.duration;break;default:r=0}return i.lag+=r,i},gantt.config.highlight_critical_path=!1,gantt._criticalPathHandler=function(){gantt.config.highlight_critical_path&&gantt.render();
},gantt.attachEvent("onAfterLinkAdd",gantt._criticalPathHandler),gantt.attachEvent("onAfterLinkUpdate",gantt._criticalPathHandler),gantt.attachEvent("onAfterLinkDelete",gantt._criticalPathHandler),gantt.attachEvent("onAfterTaskAdd",gantt._criticalPathHandler),gantt.attachEvent("onAfterTaskUpdate",gantt._criticalPathHandler),gantt.attachEvent("onAfterTaskDelete",gantt._criticalPathHandler),gantt._isCriticalTask=function(t,e){if(t&&t.id){var n=e||{};if(this._isProjectEnd(t))return!0;n[t.id]=!0;for(var a=this._getDependencies(t),i=0;i<a.length;i++){
var r=this.getTask(a[i].target);if(this._getSlack(t,r,a[i])<=0&&!n[r.id]&&this._isCriticalTask(r,n))return!0}return!1}},gantt.isCriticalTask=function(t){return gantt.assert(!(!t||void 0===t.id),"Invalid argument for gantt.isCriticalTask"),this._isCriticalTask(t,{})},gantt.isCriticalLink=function(t){return this.isCriticalTask(gantt.getTask(t.source))},gantt.getSlack=function(t,e){for(var n=[],a={},i=0;i<t.$source.length;i++)a[t.$source[i]]=!0;for(var i=0;i<e.$target.length;i++)a[e.$target[i]]&&n.push(e.$target[i]);
for(var r=[],i=0;i<n.length;i++){var s=this.getLink(n[i]);r.push(this._getSlack(t,e,this._convertToFinishToStartLink(s.id,s,t,e)))}return Math.min.apply(Math,r)},gantt._getSlack=function(t,e,n){var a=this.config.types,i=null;i=this._get_safe_type(t.type)==a.milestone?t.start_date:t.end_date;var r=e.start_date,s=0;s=+i>+r?-this.calculateDuration(r,i):this.calculateDuration(i,r);var o=n.lag;return o&&1*o==o&&(s-=o),s},gantt._getProjectEnd=function(){var t=gantt.getTaskByTime();return t=t.sort(function(t,e){
return+t.end_date>+e.end_date?1:-1}),t.length?t[t.length-1].end_date:null},gantt._isProjectEnd=function(t){return!this._hasDuration(t.end_date,this._getProjectEnd())},gantt._getSummaryPredecessors=function(t){var e=[];return this._eachParent(function(t){this._isProject(t)&&(e=e.concat(gantt._getDependencies(t)))},t),e},gantt._getDependencies=function(t){var e=this._getSuccessors(t).concat(this._getSummaryPredecessors(t));return e};
//# sourceMappingURL=../sources/ext/dhtmlxgantt_critical_path.js.map