import CommonConstant from '../../common/meta/constant';

const resourceName = 'china-order';
const resourceItemName = 'china-order-item';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    resourceItemPath: function (p = null) {
        if (!p) return resourceItemName;

        return resourceItemName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    ORDER_STATUSES: [
        {id: 0, text: "Chờ duyệt"},
        {id: 1, text: "Đã duyệt mua"},
        {id: 2, text: "Đang giao dịch"},
        {id: 3, text: "Giao dịch xong"},
        {id: 4, text: "Khiếu nại"},
        {id: 5, text: "Hoàn thành"},
    ],
};

export default Constant;
