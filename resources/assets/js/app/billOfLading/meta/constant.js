import CommonConstant from '../../common/meta/constant';

const resourceName = 'bill-of-lading';

const resourceLadingCodeName = 'lading-code';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    resourceLadingCodePath: function (p = null) {
        if (!p) return resourceLadingCodeName;

        return resourceLadingCodeName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    MORPH_TYPE_BILL_OF_LADING: "Modules\\BillOfLading\\Models\\BillOfLading",

    ORDER_STATUSES: [
        {id: 0, text: "Chờ duyệt"},
        {id: 1, text: "Đã duyệt"},
        {id: 2, text: "Đang giao hàng"},
        {id: 3, text: "Khiếu nại"},
        {id: 4, text: "Hoàn thành"},
    ],

};

export default Constant;
