import CommonConstant from '../../common/meta/constant';

const resourceName = 'price-list';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    ACTION_CHANGE_PAGE: CommonConstant.ACTION_CHANGE_PAGE,
    ACTION_CHANGE_PAGE_SIZE: CommonConstant.ACTION_CHANGE_PAGE_SIZE,
    ACTION_CLEAR_STATE: CommonConstant.ACTION_CLEAR_STATE,

    WEIGHT_CASE: [
        {key: 'less_than_half', text: "Từ 0,5kg trở xuống"},
        {key: 'more_than_half', text: "Trên 0,5kg"},
        {key: 'more_than_5', text: "Từ 5kg trở lên"},
        {key: 'more_than_30', text: "Từ 30kg trở lên"},
        {key: 'less_than_30_is_wholesale', text: "Dưới 30kg"},
        {key: 'more_than_30_is_wholesale', text: "Từ 30kg trở lên"},
        {key: 'less_than_30_normal', text: "Dưới 30kg"},
        {key: 'more_than_30_normal', text: "Từ 30kg trở lên"},
    ],
};

export default Constant;
