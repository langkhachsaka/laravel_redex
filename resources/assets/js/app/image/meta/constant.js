const resourceName = 'image';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

};

export default Constant;
