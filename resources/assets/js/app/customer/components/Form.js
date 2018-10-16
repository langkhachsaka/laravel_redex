import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import PasswordInput from "../../../theme/components/PasswordInput";
import axios from "axios";
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";


class Form extends Component {

    constructor(props) {
        super(props);

        this.state = {
            provinces: [],
            districts: [],
            wards: [],
        };
    }

    componentDidMount() {
        const {model} = this.props;

        if (model) {
            this.props.initialize(model);
        } else {
            // address only for new customer
            axios.get("get-tinh-thanh-pho", {baseURL: AppConfig.ROOT_URL})
                .then(response => {
                    this.setState({provinces: response.data.data})
                })
        }
    }

    handleSubmit(formProps) {
        const {model} = this.props;

        if (model) {
            return ApiService.post(Constant.resourcePath(model.id), formProps)
                .then(({data}) => {
                    this.props.setListState(({models}) => {
                        return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                    });
                    toastr.success(data.message);

                    this.props.actions.closeMainModal();
                });
        } else {
            return ApiService.post(Constant.resourcePath(), formProps)
                .then(({data}) => {
                    this.props.setListState(({models}) => {
                        models.unshift(data.data);
                        return {models: models};
                    });
                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        }
    }

    render() {
        const formUpdateDisabled = _.get(this.props.userPermissions, 'customer.form_update_disabled', {});
        const {model, handleSubmit, submitting, pristine} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <Field
                    name="name"
                    component={TextInput}
                    label="Tên"
                    required={true}
                    validate={[Validator.required, Validator.noSpecialCharacter]}
                />

                <Field
                    name="username"
                    component={TextInput}
                    label="Tên tài khoản"
                    required={true}
                    validate={[Validator.required, Validator.username]}
                />
                {!model && <div>
                    <Field
                        name="password"
                        component={PasswordInput}
                        label="Mật khẩu"
                        required={true}
                        validate={[Validator.required]}
                    />
                    <Field
                        name="password_confirmation"
                        component={PasswordInput}
                        label="Xác nhận mật khẩu"
                        required={true}
                        validate={[Validator.required]}
                    />
                </div>}

                <Field
                    name="email"
                    component={TextInput}
                    label="Email"
                    required={true}
                    validate={[Validator.required, Validator.email]}
                />

                <Field
                    name="order_deposit_percent"
                    component={TextInput}
                    label="Tạm ứng đơn hàng(%)"
                    validate={[Validator.number, Validator.greaterThan0, Validator.lessOrEqual100]}
                />

                {!model && <div>
                    <h3>Thông tin liên hệ</h3>
                    <Field
                        name="phone"
                        component={TextInput}
                        label="Số điện thoại"
                        required={true}
                        validate={[Validator.required, Validator.phoneNumber]}
                    />
                    <Field
                        name="provincial_id"
                        component={Select2Input}
                        select2Data={this.state.provinces.map(province => ({id: province.matp, text: province.name}))}
                        label="Tỉnh/Thành phố"
                        required={true}
                        validate={[Validator.required]}
                        select2OnSelect={(e) => {
                            this.setState({
                                districts: [],
                                wards: [],
                            });
                            this.props.change("district_id", "");
                            this.props.change("ward_id", "");
                            axios.get("get-quan-huyen", {baseURL: AppConfig.ROOT_URL, params: {matp: e.target.value}})
                                .then(response => {
                                    this.setState({districts: response.data.data})
                                })
                        }}
                    />
                    <Field
                        name="district_id"
                        component={Select2Input}
                        select2Data={this.state.districts.map(district => ({id: district.maqh, text: district.name}))}
                        label="Quận/Huyện"
                        required={true}
                        validate={[Validator.required]}
                        select2OnSelect={(e) => {
                            this.setState({
                                wards: [],
                            });
                            this.props.change("ward_id", "");
                            axios.get("get-phuong-xa", {baseURL: AppConfig.ROOT_URL, params: {maqh: e.target.value}})
                                .then(response => {
                                    this.setState({wards: response.data.data})
                                })
                        }}
                    />
                    <Field
                        name="ward_id"
                        component={Select2Input}
                        select2Data={this.state.wards.map(ward => ({id: ward.xaid, text: ward.name}))}
                        label="Phường/Xã"
                        required={true}
                        validate={[Validator.required]}
                    />
                    <Field
                        name="address"
                        component={TextInput}
                        label="Địa chỉ"
                        required={true}
                        validate={[Validator.required]}
                    />
                </div>}

                {!!model && formUpdateDisabled.password && <div>
                    <h4>Đổi mật khẩu</h4>
                    <Field
                        name="password"
                        component={PasswordInput}
                        label="Mật khẩu mới"
                    />
                    <Field
                        name="password_confirmation"
                        component={PasswordInput}
                        label="Xác nhận mật khẩu mới"
                    />
                </div>}


                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting || pristine}>
                        <i className="fa fa-fw fa-check"/>
                        {model ? 'Cập nhật' : 'Thêm mới'}
                    </button>
                </div>

            </form>
        );
    }
}

Form.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setListState: PropTypes.func,
};

function mapStateToProps({auth}) {
    return {
        userPermissions: auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'CustomerForm'
})(Form))
