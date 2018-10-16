import CommonConstant from '../../common/meta/constant';

const resourceName = 'verify-lading-code';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    STATUS_NOT_YET_CONFIRM : 2,
    STATUS_REPORTED : 3,

    MATCH_STATUSES: [
        {id: 1, text: "Đã khớp"},
        {id: 2, text: "Không khớp"},
        {id: 3, text: "Đã báo cáo"},
    ],
    ORDER_ITEM_MAX_IMAGES : 5,
};

export default Constant;
