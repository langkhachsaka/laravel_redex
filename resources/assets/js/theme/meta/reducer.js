import _ from 'lodash';

import ThemeConstant from './constant';

const initialState = {
    title: "RedEx.vn",
    breadcrumb: [],
    modal: {
        isOpen: false,
        title: null,
        body: null,
    }
};


export default function (state, action) {
    state = state || initialState;

    switch (action.type) {
        case ThemeConstant.ACTION_CHANGE_TITLE:
            return _.assign({}, state, {
                title: action.title
            });

        case ThemeConstant.ACTION_CHANGE_BREADCRUMB:
            return _.assign({}, state, {
                breadcrumb: action.breadcrumb
            });

        case ThemeConstant.ACTION_OPEN_MAIN_MODAL:
            return _.assign({}, state, {
                modal: {
                    isOpen: true,
                    title: action.title,
                    body: action.body,
                }
            });

        case ThemeConstant.ACTION_CLOSE_MAIN_MODAL:
            return _.assign({}, state, {
                modal: {
                    isOpen: false,
                    title: null,
                    body: null,
                }
            });

        default:
            return state;
    }
}
