import CommonConstant from '../../common/meta/constant';

const resourceName = 'blog';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    TYPE_VN: 1,
    TYPE_TQ: 2,
    TYPES: [
        {id: 1, text: "Kho VN"},
        {id: 2, text: "Kho TQ"},
    ],

};

export default Constant;
