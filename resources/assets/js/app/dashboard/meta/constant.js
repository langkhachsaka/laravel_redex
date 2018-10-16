const resourceName = 'dashboard';
const resourceStatisticalName = 'statistical';
const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    resourceStatisticalPath: function (p = null) {
        if (!p) return  resourceStatisticalName;

        return resourceStatisticalName + '/' + p;
    }

};

export default Constant;
