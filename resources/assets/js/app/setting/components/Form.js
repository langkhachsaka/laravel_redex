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


class Form extends Component {

    componentDidMount() {
        this.props.initialize(this.props.model);
    }

    handleSubmit(formProps) {
        return ApiService.post(Constant.resourcePath(), formProps)
            .then(({data}) => {
                this.props.setListState({model: data.data});

                toastr.success(data.message);
                this.props.actions.closeMainModal();
            });
    }

    render() {
        const {handleSubmit, submitting, pristine} = this.props;
        const {settingName} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                {(!settingName || settingName === "error_weight") && <Field
                    name="error_weight"
                    component={TextInput}
                    label="Sai số về khối lượng"
                    required={true}
                    validate={[Validator.required]}
                />}

                {(!settingName || settingName === "error_size") && <Field
                    name="error_size"
                    component={TextInput}
                    label="Sai số về kích  thước"
                    required={true}
                    validate={[Validator.required]}
                />}

                {/*{(!settingName || settingName === "error_type") && <Field*/}
                    {/*name="error_type"*/}
                    {/*component={TextInput}*/}
                    {/*label="error_type"*/}
                    {/*required={true}*/}
                    {/*validate={[Validator.required]}*/}
                {/*/>}*/}

                {(!settingName || settingName === "factor_conversion") && <Field
                    name="factor_conversion"
                    component={TextInput}
                    label="Hệ số quy đổi"
                    required={true}
                    validate={[Validator.required]}
                />}

                {(!settingName || settingName === "discount_link") && <Field
                    name="discount_link"
                    component={TextInput}
                    label="Link chiết khấu"
                />}

                {(!settingName || settingName === "order_deposit_percent") && <Field
                    name="order_deposit_percent"
                    component={TextInput}
                    label="Tạm ứng đơn hàng (%)"
                />}


                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting || pristine}>
                        <i className="fa fa-fw fa-check"/> Cập nhật
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
    form: 'SettingForm'
})(Form))
