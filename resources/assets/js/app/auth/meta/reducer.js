import _ from 'lodash';

import AuthConstant from './constant';
import {
    clearToken,
    clearUserInfo,
    clearUserPermissions,
    setToken,
    setUserInfo,
    setUserPermissions
} from "../../../helpers/Auth";

const initialState = {
    token: null,
    isAuthenticated: false,
    user: null,
    permissions: {},
    notifications: [],
    newNotificationCount: 0,
};


export default function (state, action) {
    state = state || initialState;

    switch (action.type) {
        case AuthConstant.ACTION_LOG_IN_SUCCESS:
            setToken(action.token);
            setUserInfo(action.user);
            setUserPermissions(action.permissions);

            return _.assign({}, state, {
                isAuthenticated: true,
                token: action.token,
                user: action.user,
                permissions: action.permissions,
            });

        case AuthConstant.ACTION_VERIFY_TOKEN_SUCCESS:
            return _.assign({}, state, {
                isAuthenticated: true,
                token: action.token,
                user: action.user,
                permissions: action.permissions,
            });

        case AuthConstant.ACTION_LOG_IN_FAILURE:
            return _.assign({}, state, initialState);

        case AuthConstant.ACTION_LOG_OUT:
            clearToken();
            clearUserInfo();
            clearUserPermissions();
            return _.assign({}, state, initialState);

        case AuthConstant.ACTION_RECEIVE_NOTIFICATION:
            return _.assign({}, state, {
                notifications: action.notifications,
                newNotificationCount: action.newNotificationCount,
            });

        default:
            return state;
    }
}
