import CommonConstant from '../../common/meta/constant';

const resourceName = 'warehouse-receiving-vn';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    STATUS_NOT_YET_CONFIRM : 1,
    STATUS_REPORTED : 5,

    MATCH_STATUSES: [
        {id: 1, text: "Chưa xác nhận"},
        {id: 2, text: "Đã xác nhận"},
        {id: 3, text: "Đã báo cáo"},
    ],

};

export default Constant;
