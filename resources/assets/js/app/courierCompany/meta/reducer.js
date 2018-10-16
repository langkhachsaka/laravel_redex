import _ from 'lodash';

import Constant from './constant';

const initialState = {};

export default function (state, action) {
    state = state || initialState;
    let newState = _.cloneDeep(state);

    switch (action.type) {

        case Constant.ACTION_CLEAR_STATE:
            return initialState;

        default:
            return state;
    }
}
