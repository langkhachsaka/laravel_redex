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
import DatePickerInput from "../../../theme/components/DatePickerInput";
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";
import ShopConstant from "../../shop/meta/constant";
import TextArea from "../../../theme/components/TextArea";
import moment from "moment";


class Form extends Component {

    componentDidMount() {
        const {model} = this.props;

        if (model) {
            this.props.initialize(model);
        } else {
            this.props.initialize({date_receiving: moment().format("YYYY-MM-DD")});
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
        const {model, handleSubmit, submitting, pristine} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <Field
                    name="date_receiving"
                    component={DatePickerInput}
                    onDateChange={(date) => {
                        this.props.change("date_receiving", date.format("YYYY-MM-DD"));
                    }}
                    required={true}
                    validate={[Validator.required]}
                    label="Ngày nhập"
                />

                <Field
                    name="bill_of_lading_code"
                    component={TextInput}
                    label="Mã vận đơn"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="invoice_code"
                    component={TextInput}
                    label="Mã hoá đơn"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="reason"
                    component={TextInput}
                    label="Lý do nhập"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="description"
                    component={TextInput}
                    label="Mô tả hàng"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="shop_id"
                    component={Select2Input}
                    select2Data={model && model.shop ? [{id: model.shop.id, text: model.shop.name}] : []}
                    select2Options={{
                        ajax: {
                            url: AppConfig.API_URL + ShopConstant.resourcePath("list"),
                            delay: 250
                        }
                    }}
                    label="Nguồn hàng"
                />

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

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'InventoryForm'
})(Form))
