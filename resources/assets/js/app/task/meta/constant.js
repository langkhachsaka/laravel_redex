import CommonConstant from '../../common/meta/constant';

const resourceName = 'task';
const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    ORDER_DELETED_STATUS: 12,

    ACTION_SEARCH: CommonConstant.ACTION_SEARCH,
    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,


    TASK_STATUSES: [
        {id: 1 , text: "Đơn hàng được khởi tạo",         taskTypeDisplay : [1],  roleDisplay : [10,20,30]},
        {id: 2 , text: "Chờ chốt đơn hàng",              taskTypeDisplay : [1],  roleDisplay : [10,20,30]},
        {id: 3 , text: "Xử lý chốt đơn hàng",            taskTypeDisplay : [1],  roleDisplay : [10,20,30]},
        {id: 4 , text: "Đã chốt đơn hàng",               taskTypeDisplay : [1],  roleDisplay : [10,20,30]},
        {id: 5 , text: "Đã duyệt",                       taskTypeDisplay : [1,4],roleDisplay : [10,20,30,40]},
        {id: 6 , text: "Đơn hàng không được chấp nhận",  taskTypeDisplay : [1],  roleDisplay : [10,20,30]},
        {id: 7 , text: "Hủy đơn hàng",                   taskTypeDisplay : [1],  roleDisplay : [10,20,30]},
        {id: 8 , text: "Khiếu nại",                      taskTypeDisplay : [1],  roleDisplay : [10,20,30]},
        {id: 9 , text: "Xử lý khiếu nại",                taskTypeDisplay : [1],  roleDisplay : [10,20,30]},
        {id: 10, text: "Kết thúc khiếu nại",             taskTypeDisplay : [1],  roleDisplay : [10,20,30]},
        {id: 11, text: "Kết thúc đơn hàng",              taskTypeDisplay : [3],  roleDisplay : [10,33]},
        {id: 12, text: "Đã xóa đơn hàng",                taskTypeDisplay : [1],  roleDisplay : []},
        {id: 20, text: "Xử lý đặt cọc",                  taskTypeDisplay : [4],  roleDisplay : [10,40]},
        {id: 21, text: "Khách hàng đã đặt cọc",          taskTypeDisplay : [2,4],roleDisplay : [10,40,21,31]},

        {id: 22, text: "Đã đặt hàng",                    taskTypeDisplay : [2],  roleDisplay : [10,40,21,31]},
        {id: 23, text: "Đã đặt cọc đơn hàng TQ ",        taskTypeDisplay : [2],  roleDisplay : [10,40,21,31]},
        {id: 24, text: "Đặt hàng thành công",            taskTypeDisplay : [2,3],roleDisplay : [10,40,21,31]},

        {id: 30, text: "Đã nhận hàng",                   taskTypeDisplay : [3]  ,roleDisplay : [10,22,32,33]},
        {id: 31, text: "Đã xác nhận",                    taskTypeDisplay : [3]  ,roleDisplay : [10,22,32,33]},
        {id: 32, text: "Hàng không khớp",                taskTypeDisplay : [3]  ,roleDisplay : [10,22,32,33]},
        {id: 33, text: "Đã giao hàng",                   taskTypeDisplay : [3]  ,roleDisplay : [10,22,32,33]},
        {id: 34, text: "Hoàn thành đơn hàng",            taskTypeDisplay : [1,3],roleDisplay : [10,22,32,33]},

        {id: 40, text: "Chờ nhận hàng",                  taskTypeDisplay : [4]  ,roleDisplay : [10,22,32,33]},
        {id: 41, text: "Đang nhận hàng",                 taskTypeDisplay : [4]  ,roleDisplay : [10,22,32,33]},
        {id: 42, text: "Đã nhận hàng",                   taskTypeDisplay : [4]  ,roleDisplay : [10,22,32,33]},

        {id: 50, text: "Chưa kiểm hàng",                 taskTypeDisplay : [5]  ,roleDisplay : [10,22,32,33]},
        {id: 51, text: "Đang kiểm hàng",                 taskTypeDisplay : [5]  ,roleDisplay : [10,22,32,33]},
        {id: 52, text: "Đã kiểm hàng",                   taskTypeDisplay : [5]  ,roleDisplay : [10,22,32,33]},

        {id: 60, text: "Khiếu nại",                      taskTypeDisplay : [6]  ,roleDisplay : [10,22,32,33]},
        {id: 61, text: "Admin đã duyêt",                 taskTypeDisplay : [6]  ,roleDisplay : [10,22,32,33]},
        {id: 62, text: "NVCSKH đã xử lý",                taskTypeDisplay : [6]  ,roleDisplay : [10,22,32,33]},
        {id: 63, text: "NV Order đã xử lý",              taskTypeDisplay : [6]  ,roleDisplay : [10,22,32,33]},


    ],
    COMPLAINT_TASKS_STATUS : [60,61,62,63],
    TYPE_CUSTOMER_SERVICE : 1,
    TYPE_ORDERING : 2,
    TYPE_DELIVERING_AND_RECEIVING : 7,
    TYPE_ACCOUNTANT : 3,
    COMPLETE_STATUS :[42,52,63,72],
    TYPE_RECEIVE : 4,
    TYPE_VERIFY : 5,
    TYPE_COMPLAINT : 6,
    TYPE_DELIVERY: 7,


    TYPE_CUSTOMER_SERVICE_ROLE : [10, 20, 30],
    TYPE_ORDERING_ROLE : [10, 21, 31, 40],
    TYPE_DELIVERING_AND_RECEIVING_ROLE : [10, 22, 32, 33],
    TYPE_ACCOUNTANT_ROLE : [10, 40],
    TYPE_COMPLAINT_ROLE : [10,30,31],
    TYPE_VERIFY_ROLE : [10,33],
    TYPE_RECEIVE_ROLE : [10,33],
    TYPE_DELIVERY_ROLE : [10, 22, 32, 33],
};

export default Constant;
