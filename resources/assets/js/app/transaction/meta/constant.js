import CommonConstant from "../../common/meta/constant";

const resourceName = 'transaction';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },
    resourcePaymentDetailPath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/payment-detail/' + p;
    },
    rechareDetailPath: function (p){
        return resourceName + '/recharge/' + p;
    },
    paymentConfirmPath: function(p){
        return resourceName + '/confirm/' + p;
    },
    depositConfirmPath: function(p){
        return resourceName + '/deposit/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    TRANSACTION_TYPE: [
        {id: 0, text: "Đặt cọc"}
    ],

    MORPH_TYPE_ORDER_VN: "Modules\\CustomerOrder\\Models\\CustomerOrder",
    MORPH_TYPE_BILL_OF_LADING: "Modules\\BillOfLading\\Models\\BillOfLading",
    CUSTOMER: "Modules\\Customer\\Models\\Customer"
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
