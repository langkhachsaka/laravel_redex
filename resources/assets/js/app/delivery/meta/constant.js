import CommonConstant from '../../common/meta/constant';

const resourceName = 'delivery';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },
    confirmPath: function (p) {
        return resourceName + '/confirm/' + p;
    },

    detailPath: function(p){
        return resourceName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

};

export default Constant;
