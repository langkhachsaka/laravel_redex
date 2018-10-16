import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../../ladingCode/meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'


const LadingCodeInput = ({input, onRemoveItem, showRemoveButton, meta: {touched, error, invalid}}) => (

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


const renderLadingCodesField = ({fields, meta: {error}}) => (
    <div className={`form-group ${error ? 'error' : ''}`}>
        {fields.map((ladingCode, index) =>
            <div key={index}>
                <Field
                    name={ladingCode}
                    component={LadingCodeInput}
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
            <div className="drop-zone-text">Thêm mã vận đơn</div>
        </button>
    </div>
);

class FormUpdateLadingCode extends Component {

    componentDidMount() {
        this.props.initialize({
            lading_codes: this.props.billCode.lading_codes.map(lc => lc.code),
        });
    }

    handleSubmit(formProps) {
        let isDistinct = true;
        for(let i = 0; i < formProps.lading_codes.length -1; i ++ ){
            for(let j = i + 1; j < formProps.lading_codes.length; j ++ ){
                if(formProps.lading_codes[i] === formProps.lading_codes[j]){
                    isDistinct = false;
                }
            }
        }
        if(!isDistinct){
            toastr.warning('Mã vận đơn trùng lặp');
        } else {
            return ApiService.post(Constant.resourcePath(this.props.billCode.bill_code), formProps)
                .then(({data}) => {
                    this.props.setDetailState(({model}) => {
                        model.bill_codes = model.bill_codes.map(billCode => {
                            if (billCode.bill_code === this.props.billCode.bill_code) {
                                billCode.lading_codes = data.data;
                            }

                            return billCode;
                        });

                        return {model: model};
                    });

                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        }
    }

    render() {
        const {handleSubmit, submitting} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <h5>Mã vận đơn <span className="text-danger">*</span></h5>
                <FieldArray
                    name="lading_codes"
                    component={renderLadingCodesField}
                    required={true}
                    validate={[Validator.required]}
                />

                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                        <i className="fa fa-fw fa-check"/> Cập nhật
                    </button>
                </div>

            </form>
        );
    }
}

FormUpdateLadingCode.propTypes = {
    handleSubmit: PropTypes.func,
    submitting: PropTypes.bool,
    billCode: PropTypes.object,
    setDetailState: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'FormUpdateLadingCode'
})(FormUpdateLadingCode))
