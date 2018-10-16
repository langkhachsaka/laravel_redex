import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types';
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr';
import TextArea from "../../../theme/components/TextArea";
import TextInput from "../../../theme/components/TextInput";
import {Redirect} from "react-router-dom";


const CheckboxInput = ({input, label, checked, className}) => (
    <div className={className}>
        <label>{label} :</label>  <input {...input} type="checkbox" checked={checked} className="checkbox-item-verify"/>
    </div>
);

class CustomerServiceForm extends Component {

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

                <fieldset className="fiedset-verify-customer-order bgc-grey">
                    <legend  className="legend-account-deposited"></legend>
                    <div className="row">

                        <div className="col-sm-2">
                            Shop: <input  type="text" disabled style={{display :'inline', width : "auto", marginLeft:'20px'}} value={this.state.length ? this.state.length  : ''} className="form-control"/>
                        </div>
                        <div className="col-sm-3">
                            Mã giao dịch: <input  type="text" disabled style={{display :'inline', width : "auto", marginLeft:'20px'}} value={this.state.width ? this.state.width : ''} className="form-control"/>
                        </div>
                        <div className="col-sm-3">
                            Mã vận đơn: <input  type="text" disabled style={{display :'inline', width : "auto", marginLeft:'20px'}} value={this.state.height ? this.state.height : ''} className="form-control"/>
                        </div>
                        <div className="col-sm-3">
                            Mã đơn hàng: <input  type="text" disabled style={{display :'inline', width : "auto", marginLeft:'20px'}} value={this.state.length ? this.state.length : ''} className="form-control"/>
                        </div>
                    </div>
                    <div className={"row"}>
                        <div className={"col-sm-7"}>
                            Ảnh khiếu nại
                        </div>
                        <div className={"col-sm-5"}>
                            Tình trạng khiếu nại
                        </div>
                    </div>
                    <div className={"row"}>
                        <div className={"col-sm-3"}>
                            Ảnh đặt mua
                        </div>
                        <div className={"col-sm-9"}>
                            <div className={"row"}>
                                <div className={"col-sm-3 "}>
                                    <Field
                                        label="Sai cỡ"
                                        name="error_size"
                                        component={CheckboxInput}
                                    />
                                </div>
                                <div className={"col-sm-3"}>
                                    <Field
                                        label="Sai hàng"
                                        name="error_product"
                                        component={CheckboxInput}
                                    />
                                </div>
                                <div className={"col-sm-3"}>
                                    <Field
                                        label="Sai màu"
                                        name="error_collor"
                                        component={CheckboxInput}
                                    />
                                </div>
                                <div className={"col-sm-3"}>
                                    <Field
                                        label="Thiếu hàng"
                                        name="inadequate_product"
                                        component={CheckboxInput}
                                    />
                                </div>

                            </div>
                            <Field
                                name="note"
                                label={'Ghi chú'}
                                component={TextArea}
                                rows="3"
                            />
                        </div>
                    </div>

                    CSKH & KH Confirm :
                    <fieldset className="fiedset-verify-customer-order bgc-grey">
                        <legend  className="legend-account-deposited"></legend>
                        <div className={"row"}>
                            <div className={"col-sm-2 txt-align-right"}>
                                <Field
                                    label="Sai cỡ"
                                    name="error_size"
                                    className={"vertical-align-mid"}
                                    component={CheckboxInput}
                                />
                            </div>
                            <div className={"col-sm-9"}>
                                <Field
                                    label={""}
                                    component={TextInput}
                                    name={"comment_error_size"}
                                />
                            </div>
                        </div>
                        <div className={"row"}>
                            <div className={"col-sm-2 txt-align-right"}>
                                <Field
                                    label="Sai hàng"
                                    name="error_product"
                                    className={"vertical-align-mid"}
                                    component={CheckboxInput}
                                />
                            </div>
                            <div className={"col-sm-9"}>
                                <Field
                                    label={""}
                                    component={TextInput}
                                    name={"comment_error_product"}
                                />
                            </div>
                        </div>
                        <div className={"row"}>
                            <div className={"col-sm-2 txt-align-right"}>
                                <Field
                                    label="Sai màu"
                                    name="error_collor"
                                    className={"vertical-align-mid"}
                                    component={CheckboxInput}
                                />
                            </div>
                            <div className={"col-sm-9"}>
                                <Field
                                    label={""}
                                    component={TextInput}
                                    name={"comment_error_collor"}
                                />
                            </div>
                        </div>
                        <div className={"row"}>
                            <div className={"col-sm-2 txt-align-right"}>
                                <Field
                                    label="Thiếu hàng"
                                    name="inadequate_product"
                                    className={"vertical-align-mid"}
                                    component={CheckboxInput}
                                />
                            </div>
                            <div className={"col-sm-9"}>
                                <Field
                                    label={""}
                                    component={TextInput}
                                    name={"comment_inadequate_product"}
                                />
                            </div>
                        </div>
                    </fieldset>

                </fieldset>

            </form>
        );
    }
}

CustomerServiceForm.propTypes = {
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
    form: 'AdminComplaintForm'
})(CustomerServiceForm))
