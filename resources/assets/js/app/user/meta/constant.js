import CommonConstant from '../../common/meta/constant';

const resourceName = 'user';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    ROLES: [
        {id: 10, text: "Admin"},
        {id: 20, text: "Quản lý CSKH"},
        {id: 21, text: "Quản lý đặt hàng"},
        {id: 22, text: "Quản lý bộ phận giao nhận"},
        {id: 30, text: "Nhân viên CSKH"},
        {id: 31, text: "Nhân viên đặt hàng"},
        {id: 32, text: "Nhân viên giao nhận TQ"},
        {id: 33, text: "Nhân viên giao nhận VN"},
        {id: 40, text: "Kế toán"},
    ],
    ROLE_SELLER: 30,
    ROLE_USER_PURCHASING: 31,
    ROLE_USER_WAREHOUSE_TQ: 32,
    ROLE_USER_WAREHOUSE_VN: 33,
};

export default Constant;
