import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'


const BillCodeInput = ({input, onRemoveItem, showRemoveButton, meta: {touched, error, invalid}}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        <div className="input-group">
            <input {...input} className="form-control"/>
            {showRemoveButton && <div className="input-group-append">
                <button className="btn btn-danger" type="button" onClick={() => onRemoveItem()}><i className="ft-x"/>
                </button>
            </div>}
        </div>
        {touched && error && <div className="help-block">{error}</div>}
    </div>

);


const renderBillCodesField = ({fields, meta: {error}}) => (
    <div className={`form-group ${error ? 'error' : ''}`}>
        {fields.map((billCode, index) =>
            <div key={index}>
                <Field
                    name={billCode}
                    component={BillCodeInput}
                    className="form-control"
                    validate={[Validator.required]}
                    onRemoveItem={() => fields.remove(index)}
                    showRemoveButton={fields.length > 1}
                />
            </div>
        )}
        {error && <div className="help-block">{error}</div>}
        <button
            type="button"
            className="drop-zone btn btn-sm btn-success"
            onClick={() => fields.push("")}
        >
            <div className="drop-zone-text">Thêm mã giao dịch</div>
        </button>
    </div>
);

class Form extends Component {

    componentDidMount() {
        this.props.initialize({bill_codes: [""]});
    }

    handleSubmit(formProps) {
        return ApiService.post(Constant.resourcePath(), formProps)
            .then(({data}) => {
                toastr.success(data.message);
                this.props.reset();
            });
    }

    render() {
        const {model, handleSubmit, submitting} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <div className="row">
                    <div className="col-sm-6">
                        <Field
                            name="code"
                            component={TextInput}
                            label="Mã vận đơn"
                            required={true}
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="col-sm-6">
                        <h5>Mã giao dịch <span className="text-danger">*</span></h5>
                        <FieldArray
                            name="bill_codes"
                            component={renderBillCodesField}
                            required={true}
                            validate={[Validator.required]}
                        />
                    </div>
                </div>

                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                        <i className="fa fa-fw fa-check"/>
                        {model ? 'Cập nhật' : 'Thêm mới'}
                    </button>
                </div>

            </form>
        );
    }
}

Form.propTypes = {
    handleSubmit: PropTypes.func,
    submitting: PropTypes.bool,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'LadingCodeForm'
})(Form))
