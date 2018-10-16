import CommonConstant from '../../common/meta/constant';

const resourceName = 'notification';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    MORPH_TYPE_ORDER_VN: "Modules\\CustomerOrder\\Models\\CustomerOrder",
    MORPH_TYPE_ORDER_CN: "Modules\\ChinaOrder\\Models\\ChinaOrder",
    MORPH_TYPE_BILL_OF_LADING: "Modules\\BillOfLading\\Models\\BillOfLading",
    MORPH_TYPE_COMPLAINT: "Modules\\Complaint\\Models\\Complaint",
    MORPH_TYPE_TASK: "Modules\\Task\\Models\\Task",

    STATUS_UNREAD: 0,
    STATUS_READ: 1,

};

Constant.getItemLink = function (id, type) {
    let link = "#";
    switch (type) {
        case Constant.MORPH_TYPE_ORDER_VN:
            link = "/customer-order/" + id;
            break;
        case Constant.MORPH_TYPE_ORDER_CN:
            link = "/china-order/" + id;
            break;
        case Constant.MORPH_TYPE_BILL_OF_LADING:
            link = "/bill-of-lading/" + id;
            break;
        case Constant.MORPH_TYPE_COMPLAINT:
            link = "/complaint/" + id;
            break;
        case Constant.MORPH_TYPE_TASK:
            link = "/task/" + id;
            break;
    }

    return link;
};

export default Constant;
