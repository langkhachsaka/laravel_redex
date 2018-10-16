import CommonConstant from '../../common/meta/constant';

const resourceName = 'shipment';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    NEW_BILL_OF_LADING_CODE_PREFIX : 'NEW_',
    DELETE_BILL_OF_LADING_CODE_PREFIX : 'DEL_',

    STATUS_NEW_SHIPMENT : 1,
    STATUS_TRANSPORTED : 2,
    STATUS_TRANSPORTED : 3,
    STATUS_RECIEVED_UNMATCH :  4,
    STATUSES: [
        {id: 1, text: "Lô hàng mới", hidden: ''},
        {id: 2, text: "Đã vận chuyển", hidden: ''},
        {id: 3, text: "Đã nhận", hidden: 'hidden'},
        {id: 4, text: "Đã nhận - Không khớp hàng", hidden: 'hidden'},
    ],

    STATUS_DISABLE : [3,4],
    TRANSPORT_TYPES: [
        {id: 1, text: "Nhanh"},
        {id: 2, text: "Thường"},
    ],

};

export default Constant;
