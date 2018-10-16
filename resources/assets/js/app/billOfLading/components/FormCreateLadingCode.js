import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import ConstantLadingCode from "../../ladingCode/meta/constant";
import {toastr} from 'react-redux-toastr'
import TextInput from "../../../theme/components/TextInput";
import Validator from "../../../helpers/Validator";


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

class FormCreateLadingCode extends Component {

    constructor(props) {
        super(props);

    }

    componentDidMount() {
        if(this.props.model.lading_codes.length > 0) {
            this.props.initialize({
                lading_codes: this.props.model.lading_codes.map(lc => lc.code),
            });
        } else {
            let lading_codes = [''];
            this.props.initialize({
                lading_codes: lading_codes,
            });
        }

    }


    handleSubmit(formProps) {
        const {action} = this.props;

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
            if(action=='new'){
                return ApiService.post(ConstantLadingCode.resourcePath('store-lading-code-for-bill-of-lading/'+this.props.model.id), formProps)
                    .then(({data}) => {
                        console.log(data.data.id);
                        this.props.setDetailState(({models}) => {
                            return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                        });

                        toastr.success(data.message);
                        this.props.actions.closeMainModal();
                    });
            } else {
                return ApiService.post(ConstantLadingCode.resourcePath('update-lading-code-for-bill-of-lading/'+this.props.model.id), formProps)
                    .then(({data}) => {
                    console.log(data.data.id);
                        this.props.setDetailState(({models}) => {
                            return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                        });

                        toastr.success(data.message);
                        this.props.actions.closeMainModal();
                    });
            }
        }/*
        return ApiService.post(ConstantLadingCode.resourcePath(), formProps)
            .then(({data}) => {
                this.props.setDetailState(prevState => {
                    const bills = prevState.models.map(item => item.id === data.data.id ? data.data : item);
                    return {models: bills};
                });

                toastr.info(data.message);
                this.props.actions.closeMainModal();
            });*/
    }



    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;

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

FormCreateLadingCode.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setDetailState: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'FormCreateLadingCode'
})(FormCreateLadingCode))
