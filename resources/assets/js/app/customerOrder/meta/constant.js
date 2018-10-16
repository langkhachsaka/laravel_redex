import CommonConstant from '../../common/meta/constant';

const resourceName = 'customer-order';
const resourceItemName = 'customer-order-item';
const resourceLadingCodeName = 'lading-code';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    resourceItemPath: function (p = null) {
        if (!p) return resourceItemName;

        return resourceItemName + '/' + p;
    },

    resourceItemUpdateBillCodePath: function (p = null) {
        if (!p) return resourceItemName;

        return resourceItemName + '/update-bill-code/' + p;
    },

    resourceLadingCodePath: function (p = null) {
        if (!p) return resourceLadingCodeName;

        return resourceLadingCodeName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    MORPH_TYPE_ORDER_ITEM_VN: "Modules\\CustomerOrder\\Models\\CustomerOrderItem",

    ORDER_STATUSES: [
        {id: 0, text: "Chờ duyệt"},
        {id: 1, text: "Đã duyệt mua"},
        {id: 2, text: "Đã huỷ"},
        {id: 3, text: "Đã đặt cọc"},
        {id: 4, text: "Đang giao hàng"},
        {id: 5, text: "Khiếu nại"},
        {id: 6, text: "Hoàn thành"},
    ],

    ORDER_ITEM_MAX_IMAGES: 5,



    DELIVERY_TYPES: [
        {id: 1, text: "Thường"},
        {id: 2, text: "Nhanh"},
    ],
    INSURANCE_TYPES: [
        {id: 1, text: "Không"},
        {id: 2, text: "Có"},
    ],
    REINFORCED_TYPES: [
        {id: 1, text: "Nẹp bìa"},
        {id: 2, text: "Đóng gỗ"},
    ],

};

export default Constant;
