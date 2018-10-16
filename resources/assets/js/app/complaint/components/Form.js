import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types';
import _ from 'lodash';
import Dropzone from 'react-dropzone';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr';
import TextArea from "../../../theme/components/TextArea";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import Select2Input from "../../../theme/components/Select2Input";
import AppConfig from "../../../config";
import UserConstant from "../../user/meta/constant";
import SelectInput from "../../../theme/components/SelectInput";
import CustomerConstant from "../../customer/meta/constant";
import {Redirect} from "react-router-dom";


class Form extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedFile: null,
            redirectToDetailId: null,
        };
    }

    componentDidMount() {
        const {model} = this.props;

        let initData = this.props.initValues || {};
        if (model) {
            initData = _.assign(initData, model);
        }
        this.props.initialize(initData);
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
                    if (this.props.setDetailState) {
                        this.props.setDetailState({model: data.data});
                    } else if (this.props.setListState) {
                        this.props.setListState(({models}) => {
                            return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                        });
                    }

                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        } else {
            return ApiService.post(Constant.resourcePath(), formData)
                .then(({data}) => {
                    this.setState({redirectToDetailId: data.data.id});
                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        }
    }

    renderFileInput({fields, label, meta: {touched, error}}) {
        const {model} = this.props;
        return (
            <div className={`form-group ${error ? 'error' : ''}`}>
                <h5>{label}</h5>
                <Dropzone
                    style={{border: '1px dashed', padding: '10px 20px'}}
                    multiple={false}
                    className="drop-zone"
                    //accept=".xls,.xlsx"
                    onDrop={(acceptedFiles) => {
                        this.props.change("file_report_name", acceptedFiles[0].name);
                        this.setState({selectedFile: acceptedFiles[0]});
                    }}
                >
                    {!this.state.selectedFile && <div className="drop-zone-text">
                        {model && model.file_report_name ? model.file_report_name : 'Kéo thả hoặc chọn tệp'}
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
        if (this.state.redirectToDetailId) {
            return <Redirect to={"/complaint/" + this.state.redirectToDetailId}/>;
        }

        const formDisabled = _.get(this.props.userPermissions, 'complaint.form_disabled', {});
        const {model, handleSubmit, submitting, pristine} = this.props;
        const initValues = this.props.initValues || {};

        const customer = model ? model.customer : _.get(this.props, 'initValues.customer');

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                {initValues.ordertable_id &&
                <div className="form-group">
                    {initValues.ordertable_type === Constant.MORPH_TYPE_ORDER_VN && <h5>Mã đơn hàng VN</h5>}
                    {initValues.ordertable_type === Constant.MORPH_TYPE_BILL_OF_LADING && <h5>Mã đơn hàng vận chuyển</h5>}
                    <div className="controls">
                        <input className="form-control" value={initValues.ordertable_id} disabled={true}/>
                    </div>
                </div>}

                <Field
                    name="customer_id"
                    component={Select2Input}
                    select2Data={customer ? [{id: customer.id, text: customer.name}] : []}
                    select2Options={{
                        allowClear: true,
                        placeholder: '',
                        ajax: {
                            url: AppConfig.API_URL + CustomerConstant.resourcePath("list"),
                            delay: 250
                        }
                    }}
                    label="Khách hàng"
                />

                <Field
                    name="title"
                    component={TextInput}
                    label="Tiêu đề"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="content"
                    component={TextArea}
                    rows={8}
                    label="Nội dung"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="date_end_expected"
                    component={DatePickerInput}
                    onDateChange={(date) => {
                        this.props.change("date_end_expected", date.format("YYYY-MM-DD"));
                    }}
                    required={true}
                    validate={[Validator.required]}
                    label="Ngày mong muốn"
                />

                <Field
                    name="user_id"
                    component={Select2Input}
                    select2Data={model && model.user ? [{
                        id: model.user.id,
                        text: model.user.name
                    }] : []}
                    select2Options={{
                        ajax: {
                            url: AppConfig.API_URL + UserConstant.resourcePath("list"),
                            delay: 250
                        }
                    }}
                    label="Nhân viên xử lý"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="solution"
                    component={Select2Input}
                    select2Data={Constant.COMPLAINT_SOLUTIONS}
                    label="Phương án xử lý"
                />

                <Field
                    name="file_report_name"
                    component={this.renderFileInput.bind(this)}
                    label="Biên bản xử lý"
                />

                <Field
                    name="status"
                    component={SelectInput}
                    label="Trạng thái"
                    required={true}
                    validate={[Validator.required]}
                >
                    <option value="" key={0}>-</option>
                    {Constant.COMPLAINT_STATUSES.map(stt => <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                </Field>

                <Field
                    name="note"
                    component={TextArea}
                    label="Ghi chú"
                />


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
    form: 'ComplaintForm'
})(Form))
