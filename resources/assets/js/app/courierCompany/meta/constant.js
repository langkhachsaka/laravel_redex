import CommonConstant from '../../common/meta/constant';

const resourceName = 'courier-company';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

};

export default Constant;
