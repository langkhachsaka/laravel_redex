import _ from 'lodash';

import {clearToken, clearUserInfo, clearUserPermissions, getToken} from "../helpers/Auth";
import AppConfig from '../config';
import axios from "axios";
import {toastr} from "react-redux-toastr";
import {SubmissionError} from 'redux-form'
import CommonConstant from "../app/common/meta/constant";
import store from "../store";
import {verifyToken} from "../app/auth/meta/action";
import Constant from "../app/auth/meta/constant";


export default class ApiService {

    static get(path, params, options = {}) {
        options.method = 'get';
        options.url = path;
        options.params = params;

        return this.request(options);
    }

    static post(path, data, options = {}) {
        options.method = 'post';
        options.url = path;
        options.data = data;

        return this.request(options);
    }

    static delete(path, params, options = {}) {
        options.method = 'delete';
        options.url = path;
        options.params = params;

        return this.request(options);
    }

    static request(requestConfig) {
        const defaultConfig = {
            //url: '/user',
            //method: 'get',
            baseURL: AppConfig.API_URL,
            // transformRequest: [function (data, headers) {
            //     return data;
            // }],
            // transformResponse: [function (data) {
            //     return data;
            // }],
            headers: {'Authorization': 'Bearer ' + getToken()},
            // headers: {'X-Requested-With': 'XMLHttpRequest'},
            // params: {
            //     ID: 12345
            // },
            // data: {
            //     firstName: 'Fred'
            // },
            // timeout: 1000,
            // responseType: 'json',
            validateStatus: function (status) {
                // return status >= 200 && status < 300; // default
                return status >= 200 && status < 600; // default
            },
        };

        return axios(_.merge(defaultConfig, requestConfig))
            .then(response => {

                if (response.status === 200 && response.data.status === CommonConstant.RESPONSE_STATUS_SUCCESS) {
                    return response;
                }

                if (response.status === 422) {
                    toastr.warning(response.data.message);
                    throw new SubmissionError(response.data.data)

                }

                if (response.status === 401) { //
                    store.dispatch({type: Constant.ACTION_LOG_OUT});
                    toastr.error("Bạn cần phải đăng nhập để thực hiện chức năng này");
                } else if (response.status === 403) {
                    toastr.error("Bạn không có quyền truy cập vào trang này");
                    return response; // Need check response code
                } else if (response.status >= 400) {
                    //toastr.error("Lỗi hệ thống");
                    toastr.error(response.data.message);
                }

                if (response.status >= 300) {
                    /*eslint no-throw-literal: 0*/

                }

                throw "Response Error";
            });

    }
};
