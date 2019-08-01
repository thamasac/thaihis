if(!window.ganttModules){
    window.ganttModules = {};
}

ganttModules.wbs = (function(gantt) {
    var pathCash = {};

    var getTaskPath = function (id) {
        if (!gantt.isTaskExists(id)) return "";
        if (pathCash[id]) return pathCash[id];

        var parent = gantt.getParent(id),
            path = "";

        if (parent !== gantt.config.root_id){
            
            if(getTaskPath(parent) && getTaskPath(parent) !== '')path = getTaskPath(parent) + ".";
            else path = "";
        }

        path += (gantt.getTaskIndex(id) + 1);
        pathCash[id] = path;
        return path;
    };

    var clearCash = function(){
        pathCash = {};
    };

    gantt.attachEvent("onParse", clearCash);
    gantt.attachEvent("onRowDragEnd", clearCash);
    gantt.attachEvent("onAfterTaskAdd", clearCash);
    gantt.attachEvent("onAfterTaskDelete", clearCash);
    gantt.attachEvent("onAfterTaskUpdate", clearCash);

    return {
        getTaskPath: getTaskPath
    }
})(gantt);