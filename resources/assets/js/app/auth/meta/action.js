import {getToken, getUserInfo, getUserPermissions} from '../../../helpers/Auth';
import Constant from "./constant";
import ApiService from "../../../services/ApiService";
import {toastr} from "react-redux-toastr";


export function login(data) {
    return function (dispatch) {
        dispatch({
            type: Constant.ACTION_LOG_IN_SUCCESS,
            token: data.access_token,
            user: data.user,
            permissions: data.permissions,
        });
    };
}

export function verifyToken() {
    return (dispatch) => {
        const token = getToken();
        if (token) {
            dispatch({
                type: Constant.ACTION_VERIFY_TOKEN_SUCCESS,
                token: token,
                user: getUserInfo(),
                permissions: getUserPermissions(),
            });
        }
    };
}

export function logout() {
    return function (dispatch) {
        ApiService.post('auth/logout')
            .then(({data}) => {
                toastr.success(data.message);
            });

        dispatch({type: Constant.ACTION_LOG_OUT});
    };
}

export function updateNotification(notifications, newNotificationCount) {
    return {
        type: Constant.ACTION_RECEIVE_NOTIFICATION,
        notifications: notifications,
        newNotificationCount: newNotificationCount,
    };
}
