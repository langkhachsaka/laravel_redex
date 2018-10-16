import React, {Component} from 'react';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types';
import {bindActionCreators} from "redux";
import * as authActions from '../meta/action';
import _ from 'lodash';
import {Redirect} from "react-router-dom";
import {toastr} from 'react-redux-toastr'
import AppConfig from "../../../config";
import axios from "axios/index";
import imgLogo from "../../../images/logo.png"

class LoginForm extends Component {

    handleSubmit(formProps) {
        if (!formProps.username) {
            toastr.warning("Tên đăng nhập không được để trống");
            return;
        }
        if (!formProps.password) {
            toastr.warning("Mật khẩu không được để trống");
            return;
        }

        return axios.post(AppConfig.API_URL + 'auth/login', formProps)
            .then(({data}) => {
                this.props.actions.login(data);
                toastr.success(data.message);
            })
            .catch(({response: {data}}) => {
                toastr.warning(data.message);
            });
    }

    render() {
        const {handleSubmit, submitting} = this.props;
        const {isAuthenticated} = this.props;

        if (isAuthenticated) return (<Redirect to="/"/>);

        return (
            <div className="app-content content" style={{marginLeft: '0'}}>
                <div className="content-wrapper">
                    <div className="content-header row">
                    </div>
                    <div className="content-body">
                        <section className="flexbox-container">
                            <div className="col-12 d-flex align-items-center justify-content-center">
                                <div className="col-md-4 col-10 box-shadow-2 p-0">
                                    <div className="card border-grey border-lighten-3 m-0">
                                        <div className="card-header border-0 pt-0">
                                            <div className="card-title text-center">
                                                <div className="p-1">
                                                    <img src={imgLogo} alt="branding logo"/>
                                                </div>
                                            </div>
                                            <h6 className="card-subtitle line-on-side text-muted text-center font-small-3 pt-2 m-0">
                                                <span>Đăng nhập hệ thống</span>
                                            </h6>
                                        </div>
                                        <div className="card-content">
                                            <div className="card-body">
                                                <form className="form-horizontal form-simple"
                                                      onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                                                    <fieldset
                                                        className="form-group position-relative has-icon-left mb-0">
                                                        <Field
                                                            name="username"
                                                            component="input"
                                                            type="text"
                                                            placeholder="Tên đăng nhập"
                                                            className="form-control form-control-lg input-lg"
                                                        />
                                                        <div className="form-control-position">
                                                            <i className="ft-user"/>
                                                        </div>
                                                    </fieldset>

                                                    <fieldset className="form-group position-relative has-icon-left">
                                                        <Field
                                                            name="password"
                                                            component="input"
                                                            type="password"
                                                            placeholder="Mật khẩu"
                                                            className="form-control form-control-lg input-lg"
                                                        />
                                                        <div className="form-control-position">
                                                            <i className="ft-lock"/>
                                                        </div>
                                                    </fieldset>

                                                    <button type="submit" className="btn btn-info btn-lg btn-block"
                                                            disabled={submitting}>
                                                        <i className="ft-unlock"/> Login
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        );
    }
}

LoginForm.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
};

/**
 * Map the state to props.
 */
function mapStateToProps({auth}) {
    return {
        token: auth.token,
        isAuthenticated: auth.isAuthenticated
    }
}

/**
 * Map the actions to props.
 */
function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, authActions), dispatch)
    }
}

/**
 * Connect the component to the Redux store.
 */
export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'LoginForm'
})(LoginForm))
