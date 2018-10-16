import CommonConstant from '../../common/meta/constant';

const resourceName = 'warehouse-receiving-cn';
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

    STATUS_MATCH : 1,
    STATUS_UNMATCH : 2,
    MATCH_STATUSES: [
        {id: 1, text: "Khớp"},
        {id: 2, text: "Chưa khớp"},
    ],
    TRANSPORT_TYPES: [
        {id: 1, text: "Nhanh"},
        {id: 2, text: "Thường"},
    ],
};

export default Constant;
