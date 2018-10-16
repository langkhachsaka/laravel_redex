import CommonConstant from '../../common/meta/constant';

const resourceName = 'complaint';
const resourceHistoryName = 'complaint-history';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    resourceHistoryPath: function (p = null) {
        if (!p) return resourceHistoryName;

        return resourceHistoryName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    ROLE_ADMIN : 10,
    ROLE_CUSTOMER_SERVICE_OFFICER: 30,
    ROLE_ORDERING_SERVICE_OFFICER: 31,

    SOLUTION_RECEIVE_PRODUCT : 1,
    SOLUTION_BACK_COMMODITY : 2,
    SOLUTION_SHOP_ADD_MISSING_PRODUCT : 3,
    SOLUTION_SHOP_RETURN_MONEY: 4,

    CASE_ERROR_SIZE : 1,
    CASE_ERROR_COLLOR : 2,
    CASE_ERROR_PRODUCT : 3,
    CASE_ERROR_INADEQUATE_PRODUCT :4,

    STATUS_NOT_YET_PROCESS : 0,
    STATUS_ADMIN_PROCESSED : 1,
    STATUS_CUSTOMER_SERVICE_PROCESSED : 2,
    STATUS_ORDER_OFFICER_PROCESSED : 3,

    COMPLAINT_STATUSES: [
        {id: 0, text: "Chờ xử lý"},
        {id: 1, text: "Đang xử lý"},
        {id: 2, text: "Đã xử lý"},
    ],

    COMPLAINT_SOLUTIONS_ERROR: [
        {id: 1, text: 'Nhận hàng và bồi hoàn tiền từ Shop'},
        {id: 2, text: 'Trả hàng lại cho Shop'},
    ],
    COMPLAINT_SOLUTIONS_INADEQUATE: [
        {id: 3, text: 'Shop bổ sung hàng thiếu'},
        {id: 4, text: 'Shop hoàn tiền hàng thiếu'},
    ],

    MORPH_TYPE_ORDER_VN: "Modules\\CustomerOrder\\Models\\CustomerOrder",
    MORPH_TYPE_BILL_OF_LADING: "Modules\\BillOfLading\\Models\\BillOfLading",

};

Constant.getOrderLink = function (id, type) {
    let link;
    switch (type) {
        case Constant.MORPH_TYPE_ORDER_VN:
            link = "/customer-order/" + id;
            break;
        case Constant.MORPH_TYPE_BILL_OF_LADING:
            link = "/bill-of-lading/" + id;
            break;
    }

    return link;
};

export default Constant;
