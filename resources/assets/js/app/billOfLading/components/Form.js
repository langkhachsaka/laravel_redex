import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import Dropzone from 'react-dropzone';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import {toastr} from 'react-redux-toastr'
import Select2Input from "../../../theme/components/Select2Input";
import CustomerConstant from "../../customer/meta/constant";
import CourierCompanyConstant from "../../courierCompany/meta/constant";
import AppConfig from "../../../config";
import Validator from "../../../helpers/Validator";
import UserConstant from "../../user/meta/constant";
import TextInput from "../../../theme/components/TextInput";


class Form extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedFile: null,
        };
    }

    componentDidMount() {
        const {model} = this.props;

        if (model) {
            delete model.end_date; // trick
            this.props.initialize(model);
        }
    }

    handleSubmit(formProps) {
        const {model} = this.props;
        const formData = new FormData();

        _.forOwn(formProps, (value, key) => {
            formData.append(key, value);
        });
        if (this.state.selectedFile) formData.append('file', this.state.selectedFile);

        if (model) {
            return ApiService.post(Constant.resourcePath(model.id), formData)
                .then(({data}) => {
                    this.props.setListState(({models}) => {
                        return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                    });
                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        } else {
            return ApiService.post(Constant.resourcePath(), formData)
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

    renderFileInput({fields, meta: {touched, error}}) {
        const {model} = this.props;
        return (
            <div className={`form-group ${error ? 'error' : ''}`}>
                <h5>Tệp đính kèm <span className="text-danger">*</span></h5>
                <Dropzone
                    style={{border: '1px dashed', padding: '10px 20px'}}
                    multiple={false}
                    className="drop-zone"
                    accept=".xls,.xlsx"
                    onDrop={(acceptedFiles) => {
                        this.props.change("file_name", acceptedFiles[0].name);
                        this.setState({selectedFile: acceptedFiles[0]});
                    }}
                >
                    {!this.state.selectedFile && <div className="drop-zone-text">
                        {model && model.file_name ? model.file_name : 'Kéo thả hoặc chọn tệp đính kèm'}
                    </div>}
                    {!!this.state.selectedFile && <div className="drop-zone-uploaded-text">
                        <i className="ft-file-text"/> {this.state.selectedFile.name}
                    </div>}
                </Dropzone>
                {touched && error && <div className="help-block">{error}</div>}
            </div>
        );
    }

    render() {
        const formDisabled = _.get(this.props.userPermissions, 'bill_of_lading.form_disabled', {});
        const {model, handleSubmit, submitting, pristine} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <Field
                    name="seller_id"
                    component={Select2Input}
                    select2Data={model && model.seller ? [{id: model.seller.id, text: model.seller.name}] : []}
                    select2Options={{
                        placeholder: formDisabled.seller_id ? this.props.authUser.name : '',
                        ajax: {
                            url: AppConfig.API_URL + UserConstant.resourcePath("list?role=" + UserConstant.ROLE_SELLER),
                            delay: 250
                        }
                    }}
                    label="Nhân viên CSKH"
                    disabled={formDisabled.seller_id}
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="customer_id"
                    component={Select2Input}
                    select2Data={model && model.customer ? [{
                        id: model.customer.id,
                        text: model.customer.name
                    }] : []}
                    select2Options={{
                        ajax: {
                            url: AppConfig.API_URL + CustomerConstant.resourcePath("list"),
                            delay: 250
                        }
                    }}
                    select2OnSelect={(e) => {
                        if (e.params.data.customer_addresses.length) {
                            this.props.change("customer_billing_name", e.params.data.customer_addresses[0].name);
                            this.props.change("customer_billing_address", e.params.data.customer_addresses[0].address);
                            this.props.change("customer_billing_phone", e.params.data.customer_addresses[0].phone);
                            this.props.change("customer_shipping_name", e.params.data.customer_addresses[0].name);
                            this.props.change("customer_shipping_address", e.params.data.customer_addresses[0].address);
                            this.props.change("customer_shipping_phone", e.params.data.customer_addresses[0].phone);
                        }
                    }}
                    label="Khách hàng"
                    required={true}
                    validate={[Validator.required]}
                />

                <div className="row">
                    <div className="col-sm-6">
                        <h4>Thông tin người mua</h4>

                        <div className="row">
                            <div className="col-sm-4">
                                <Field
                                    name="customer_billing_name"
                                    component={TextInput}
                                    label="Tên"
                                    required={true}
                                    validate={[Validator.required]}
                                />
                            </div>
                            <div className="col-sm-4">
                                <Field
                                    name="customer_billing_address"
                                    component={TextInput}
                                    label="Địa chỉ"
                                    required={true}
                                    validate={[Validator.required]}
                                />
                            </div>
                            <div className="col-sm-4">
                                <Field
                                    name="customer_billing_phone"
                                    component={TextInput}
                                    label="Điện thoại"
                                    required={true}
                                    validate={[Validator.required]}
                                />
                            </div>
                        </div>
                    </div>
                    <div className="col-sm-6">
                        <h4>Thông tin người nhận</h4>

                        <div className="row">
                            <div className="col-sm-4">
                                <Field
                                    name="customer_shipping_name"
                                    component={TextInput}
                                    label="Tên"
                                    required={true}
                                    validate={[Validator.required]}
                                />
                            </div>
                            <div className="col-sm-4">
                                <Field
                                    name="customer_shipping_address"
                                    component={TextInput}
                                    label="Địa chỉ"
                                    required={true}
                                    validate={[Validator.required]}
                                />
                            </div>
                            <div className="col-sm-4">
                                <Field
                                    name="customer_shipping_phone"
                                    component={TextInput}
                                    label="Điện thoại"
                                    required={true}
                                    validate={[Validator.required]}
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <h4>Thông tin hàng hoá</h4>

                <Field
                    name="courier_company_id"
                    component={Select2Input}
                    select2Data={model && model.courier_company ? [{
                        id: model.courier_company.id,
                        text: model.courier_company.name
                    }] : []}
                    select2Options={{
                        ajax: {
                            url: AppConfig.API_URL + CourierCompanyConstant.resourcePath("list"),
                            delay: 250
                        }
                    }}
                    label="Công ty chuyển phát"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="file_name"
                    component={this.renderFileInput.bind(this)}
                    label="Tệp đính kèm"
                    required={true}
                    validate={[Validator.required]}
                />

               {/* <Field
                    name="bill_of_lading_code"
                    component={TextInput}
                    label="Mã vận đơn"
                />*/}


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
        authUser: auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'BillOfLadingForm'
})(Form))
