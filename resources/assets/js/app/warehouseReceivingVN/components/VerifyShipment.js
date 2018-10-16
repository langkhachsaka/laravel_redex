import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import Select2Input from "../../../theme/components/Select2Input";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import SelectInput from "../../../theme/components/SelectInput";
import {Redirect} from "react-router-dom";


const TextInput = ({input, label,unit, type, disabled, placeholder, required, meta: {touched, error, invalid}}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        {label}{input && required ? (<span className="text-danger">*</span>) : ''}
         <input {...input}  type="text" disabled={disabled} style={{display :'inline', width : '100px',marginBottom:'20px'}}  placeholder={placeholder} className="form-control"/> {unit}
        {touched && error && <div className="help-block">{error}</div>}
    </div>

);

class VerifyShipment extends Component {

    constructor(props) {
        super(props);

        this.state = {
            redirectToListVerifyLadingCode : null,
            model : this.props.model,
        };
    }

    componentDidMount() {
        const {model} = this.props;
        this.props.change('weight', this.state.model.real_weight);
        this.props.change('length', this.state.model.length);
        this.props.change('width', this.state.model.width);
        this.props.change('height', this.state.model.height);
    }

    handleSubmit(formProps) {
        return ApiService.post(Constant.resourcePath("storeShipment/"+this.state.model.shipment_code), formProps)
            .then(({data}) => {
                this.setState({
                    redirectToListVerifyLadingCode : data.data,
                });
                this.props.actions.closeMainModal();
            });
    }




    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;
        if (this.state.redirectToListVerifyLadingCode) {
            return <Redirect to={"/warehouse-receiving-vn/" + this.state.redirectToListVerifyLadingCode}/>;
        }
        return (

            <div>
                <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                    <div className="row">
                        <div className="col-sm-3"></div>
                        <div className="col-sm-6">
                            <div style={{ textAlign:'center',paddingBottom: '20px'}}>
                                Mã lô hàng
                                <input  type="text" style={{display :'inline', width : '200px',marginLeft:'20px'}}
                                        disabled="disabled"
                                        value={this.state.model.shipment_code} className="form-control"/>
                            </div>
                            <fieldset className="fiedset-verify-customer-order bgc-grey">
                                <legend  className="legend-account-deposited"></legend>
                                <fieldset className="fiedset-verify-customer-order bgc-grey">
                                    <legend  className="legend-account-deposited"></legend>
                                    <Field
                                        label="Khối lượng :"
                                        name="weight"
                                        component={TextInput}
                                        unit="kg"
                                        validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                                    />
                                </fieldset>
                                <legend  className="legend-account-deposited"></legend>
                                <fieldset className="fiedset-verify-customer-order bgc-grey">
                                    <legend  className="legend-account-deposited">Kích thước</legend>
                                    <div className="row">
                                        <div className={"col-md-8"}></div>
                                        <div className={"col-md-4"}>
                                            <Field
                                                name="length"
                                                component={TextInput}
                                                placeholder="Dài"
                                                unit="cm"
                                                validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                                            />
                                            <Field
                                                name="width"
                                                component={TextInput}
                                                placeholder="Rộng"
                                                unit="cm"
                                                validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                                            />
                                            <Field
                                                name="height"
                                                component={TextInput}
                                                placeholder="Cao"
                                                unit="cm"
                                                validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                                            />
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset className="fiedset-verify-customer-order bgc-grey">
                                    <legend  className="legend-account-deposited"></legend>
                                    <Field
                                        label="Đóng gói"
                                        name="pack"
                                        component={SelectInput}
                                    >
                                        <option value="0" key={0}>-----</option>
                                        <option value="1" key={1}>Đóng gỗ</option>
                                        <option value="2" key={2}>Nẹp bìa</option>
                                    </Field>
                                </fieldset>
                            </fieldset>
                            <div style={{display : 'inline', float : 'right'}}>
                                <button type="submit" name="submit" value="submit" className="btn btn-lg btn-success" >
                                    <i className="fa fa-fw fa-check"/>
                                    Bắt đầu
                                </button>
                            </div>
                        </div>
                    </div>

                </form>

        </div>
        );
    }
}

VerifyShipment.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    errorData: PropTypes.object,
    setListState: PropTypes.func,
};

function mapStateToProps({auth}) {
    return {
        authUser: auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'WarehouseReceivingCNForm'
})(VerifyShipment))
