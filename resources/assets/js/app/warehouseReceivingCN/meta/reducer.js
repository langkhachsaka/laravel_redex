import _ from 'lodash';

import Constant from './constant';
import CommonConstant from "../../common/meta/constant";

const initialState = {
    search: {
        params: {},
        meta: {
            page: CommonConstant.PAGE_DEFAULT,
            per_page: CommonConstant.PAGE_SIZE_DEFAULT,
        }
    }
};

export default function (state, action) {
    state = state || initialState;
    let newState = _.cloneDeep(state);

    switch (action.type) {
        case Constant.ACTION_SEARCH:
            newState.search.params = action.params;
            newState.search.meta.page = CommonConstant.PAGE_DEFAULT; // reset page to #1

            return newState;

        case Constant.ACTION_CHANGE_PAGE:
            newState.search.meta.page = action.page;

            return newState;

        case Constant.ACTION_CHANGE_PAGE_SIZE:
            newState.search.meta.per_page = action.pageSize;
            newState.search.meta.page = CommonConstant.PAGE_DEFAULT; // reset page to #1

            return newState;

        case Constant.ACTION_CLEAR_STATE:
            return initialState;

        default:
            return state;
    }
}
