import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm,formValueSelector} from 'redux-form';
import PropTypes from 'prop-types';
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr';
import TextArea from "../../../theme/components/TextArea";
import {Redirect} from "react-router-dom";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import SelectInput from "../../../theme/components/SelectInput";


const CheckboxInput = ({input, label, checked, className}) => (
    <div className={className}>
        <label>{label} :</label>  <input {...input} type="checkbox" checked={checked} className="checkbox-item-verify"/>
    </div>
);
const TextInput = ({input,unit, label, type, disabled, placeholder, required, meta: {touched, error, invalid}}) => (

    <div className={` ${touched && invalid ? 'error' : ''}`}>
        {label}{input && required ? (<span className="text-danger">*</span>) : ''}
         <input {...input}  type="text" disabled={disabled} style={{display :'inline', width : '70%',marginBottom:'20px'}}  className="form-control"/>{unit}
        {touched && error && <div className="help-block">{error}</div>}
    </div>

);

class ReturnCommodityForShop extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedFile: null,
            redirectToDetailId: null,
            total_customer_pay : 0,
        };
    }

    componentDidMount() {
        const {model} = this.props;
        if(model.status = Constant.STATUS_ORDER_OFFICER_PROCESSED){
            this.setState({
                total_customer_pay : model.total_customer_pay,
            })
        }
        let initData = this.props.initValues || {};
        if (model) {
            initData = _.assign(initData, model);
        }
        this.props.initialize(initData);
    }

    itemCalcMoney = {};
    calcMoneyCustomerPay(key,value){
        const {rate,model} = this.props;
        let sum1 = 0;
        let sum2 = 0;
        this.itemCalcMoney[key] = value;
        if(this.itemCalcMoney.ship_inland_fee && this.itemCalcMoney.shop_pay){
            sum1 = Math.round(this.itemCalcMoney.ship_inland_fee * rate * (100- this.itemCalcMoney.shop_pay) / 100);
            this.props.change(model.case + ".customer_pay_percent",(100- this.itemCalcMoney.shop_pay)+ "%");
        }
        if(this.itemCalcMoney.fee_ship_vn_cn && this.itemCalcMoney.redex_support){
            sum2 = Math.round(this.itemCalcMoney.fee_ship_vn_cn - this.itemCalcMoney.redex_support);
            this.props.change(model.case + ".customer_pay_money",sum2);
        }
        this.setState({
            total_customer_pay : sum2 + sum1,
        });
        this.props.change(model.case + ".total_customer_pay",sum1 + sum2)
    }
    changeData(model,rootModel){
        if(rootModel.status >= Constant.STATUS_CUSTOMER_SERVICE_PROCESSED){
            this.props.change(model.case + ".verify_customer_order_item_note", rootModel.verify_customer_order_item.note);
            this.props.change(model.case + ".customer_comment", model.customer_comment);
            this.props.change(model.case + ".customer_solution", model.solution);
        }
        if(rootModel.status >= Constant.STATUS_ORDER_OFFICER_PROCESSED) {
            this.props.change(model.case + '.redex_comment',model.redex_comment);
            this.props.change(model.case + '.date_of_delivery',model.date_of_delivery);
            this.props.change(model.case + '.date_return_money',model.date_return_money);
            this.props.change(model.case + '.fee_ship_vn_cn',model.fee_ship_vn_cn);
            this.props.change(model.case + '.money_shop_return',model.money_shop_return);
            this.props.change(model.case + '.note',model.note);
            this.props.change(model.case + '.order_office_solution',model.order_office_solution);
            this.props.change(model.case + '.redex_solution',model.redex_solution);
            this.props.change(model.case + '.redex_support',model.redex_support);
            this.props.change(model.case + '.ship_inland_fee',model.ship_inland_fee);
            this.props.change(model.case + '.shop_pay',model.shop_pay);
            this.props.change(model.case + '.sum_weight_back',model.sum_weight_back);
            this.props.change(model.case + '.sum_weight_delivery',model.sum_weight_delivery);
            this.props.change(model.case + '.total_customer_pay',model.total_customer_pay);
            this.props.change(model.case + '.customer_pay_percent',100 - model.shop_pay);
            this.props.change(model.case+ '.customer_pay_money',model.fee_ship_vn_cn - model.redex_support);
        }
    }
    render() {
        if (this.state.redirectToDetailId) {
            return <Redirect to={"/complaint/" + this.state.redirectToDetailId}/>;
        }
        const {model,rootModel,rate, confirmed} = this.props;
        this.changeData(model,rootModel);
        return (

                <fieldset className="fiedset-verify-customer-order bgc-grey">
                    <legend  className="legend-account-deposited">Trả hàng lại cho Shop</legend>
                    <table style={{width: "90%"}}>
                        <tbody>
                            <tr>
                                <td>
                                    Số lượng đặt:
                                </td>
                                <td>
                                    <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "100%"}}
                                            value={rootModel.customer_order_item ? rootModel.customer_order_item.quantity   : ''} className="form-control"/>
                                </td>
                                <td>
                                    Số lượng về:
                                </td>
                                <td>
                                    <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "100%"}}
                                            value={rootModel.verify_customer_order_item ? rootModel.verify_customer_order_item.quantity_verify   : ''} className="form-control"/>
                                </td>
                                <td>
                                    Số lượng thiếu:
                                </td>
                                <td>
                                    <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "100%"}}
                                            value={rootModel.customer_order_item.quantity - rootModel.verify_customer_order_item.quantity_verify} className="form-control"/>
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <div className={"margin-bottom-10px"}>
                                        Tổng KL về:
                                    </div>
                                </td>
                                <td>
                                    <Field
                                        name={model.case + ".sum_weight_delivery" }
                                        label={''}
                                        component={TextInput}
                                        disabled={confirmed}
                                        validate={[Validator.requireFloat]}
                                    />
                                </td>
                                <td>
                                    <div className={"margin-bottom-10px"}>
                                        Khối lượng trả:
                                    </div>
                                </td>
                                <td>
                                    <Field
                                        name={model.case + ".sum_weight_back"}
                                        label={''}
                                        component={TextInput}
                                        disabled={confirmed}
                                        validate={[Validator.requireFloat]}
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Field
                        name={model.case + ".verify_customer_order_item_note"  }
                        label={''}
                        disabled={true}
                        component={TextArea}
                        rows="3"
                        validate={[Validator.required]}
                    />
                    <table style={{width : "100%"}}>
                        <tbody>
                            <tr>
                                <td colSpan={4}>
                                    Ý kiến Redex
                                </td>
                                <td className={"text-align-right"}>
                                    Tỷ giá :
                                </td>
                                <td>
                                    <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginBottom: "20px"}} value={rate} className="form-control"/>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td className={"text-align-right"}>
                                    <div className={'margin-bottom-10px'}>
                                        Ship nội địa phát sinh:
                                    </div>
                                </td>
                                <td>
                                    <Field
                                        name={model.case + ".ship_inland_fee"  }
                                        label={''}
                                        onChange = {e => {
                                            this.calcMoneyCustomerPay('ship_inland_fee',e.target.value);
                                        }}
                                        component={TextInput}
                                        disabled={confirmed}
                                        unit={' ¥'}
                                        validate={[Validator.requireFloat]}
                                    />
                                </td>
                                <td className={"text-align-right"}>
                                    <div className={'margin-bottom-10px'}>
                                        Shop chịu:
                                    </div>
                                </td>
                                <td>
                                    <Field
                                        name={model.case + ".shop_pay"  }
                                        label={''}
                                        onChange = {e => {
                                            this.calcMoneyCustomerPay('shop_pay',e.target.value);
                                        }}
                                        component={TextInput}
                                        disabled={confirmed}
                                        unit={' %'}
                                        validate={[Validator.requireFloat,Validator.greaterOrEqual(0),Validator.lessOrEqual(100)]}
                                    />
                                </td>
                                <td className={"text-align-right"}>
                                    <div className={'margin-bottom-10px'}>
                                        Khách chịu:
                                    </div>
                                </td>
                                <td>
                                    <Field
                                        name={model.case + ".customer_pay_percent"  }
                                        label={''}
                                        disabled={true}
                                        unit={' %'}
                                        component={TextInput}
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td className={"text-align-right"}>
                                    <div className={'margin-bottom-10px'}>
                                        Phí ship VN-TQ:
                                    </div>
                                </td>
                                <td>
                                    <Field
                                        name={model.case + ".fee_ship_vn_cn"  }
                                        label={''}
                                        onChange = {e => {
                                            this.calcMoneyCustomerPay('fee_ship_vn_cn',e.target.value);
                                        }}
                                        unit ={' vnđ'}
                                        validate={[Validator.requireFloat,Validator.greaterThan0]}
                                        component={TextInput}
                                        disabled={confirmed}
                                    />
                                </td>
                                <td className={"text-align-right"}>
                                    <div className={'margin-bottom-10px'}>
                                        Redex hỗ trợ:
                                    </div>
                                </td>
                                <td>
                                    <Field
                                        name={model.case + ".redex_support"  }
                                        label={''}
                                        disabled={confirmed}
                                        onChange = {e => {
                                            this.calcMoneyCustomerPay('redex_support',e.target.value);
                                        }}
                                        unit={' vnđ'}
                                        validate={[Validator.requireFloat,Validator.greaterOrEqual0]}
                                        component={TextInput}
                                    />
                                </td>
                                <td className={"text-align-right"}>
                                    <div className={'margin-bottom-10px'}>
                                        Khách chịu:
                                    </div>
                                </td>
                                <td>
                                    <Field
                                        name={model.case + ".customer_pay_money"  }
                                        label={''}
                                        disabled={true}
                                        unit={' vnđ'}
                                        component={TextInput}
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td colSpan={5} className={"text-align-right"}>
                                    Tổng số tiền khách chịu :
                                </td>
                                <td>
                                    <input  type="text" disabled style={{display :'inline',     background: "cornflowerblue", width : "auto", maxWidth: "70%"}} value={this.state.total_customer_pay} className="form-control"/>
                                    <span>{' '+ 'vnđ'}</span>
                                    <div hidden={true}>
                                        <Field
                                            name={model.case + ".total_customer_pay"  }
                                            label={''}
                                            disabled={confirmed}
                                            unit={' vnđ'}
                                            component={TextInput}
                                        />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <Field
                        name={model.case + ".redex_comment"}
                        label={''}
                        component={TextArea}
                        rows="3"
                        disabled={confirmed}
                        validate={[Validator.required]}
                    />
                    <div className={"row"}>
                        <div className={"col-sm-3 top-10px"}>
                            Ý kiến Khách Hàng
                        </div>
                        <div className={"col-sm-5"}>
                            <Field
                                name={model.case + ".customer_solution" }
                                component={SelectInput}
                                disabled={true}
                                validate={[Validator.required]}>
                                <option value="" key={0}>-- Chọn phương án giải quyết --</option>
                                {[Constant.CASE_ERROR_SIZE, Constant.CASE_ERROR_COLLOR, Constant.CASE_ERROR_PRODUCT].indexOf(model.case) != -1 && Constant.COMPLAINT_SOLUTIONS_ERROR.map(stt =>
                                    <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                                {[Constant.CASE_ERROR_INADEQUATE_PRODUCT].indexOf(model.case) != -1 && Constant.COMPLAINT_SOLUTIONS_INADEQUATE.map(stt =>
                                    <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                            </Field>
                        </div>
                    </div>
                    <Field
                        name={model.case + ".customer_comment"}
                        label={''}
                        disabled={true}
                        component={TextArea}
                        rows="3"
                    />
                    <div className={"row"}>
                        <div className={"col-sm-3 top-10px"}>
                            NV Order xử lý với Shop
                        </div>
                        <div className={"col-sm-5"}>
                            <Field
                                name={model.case + ".order_office_solution"}
                                component={SelectInput}
                                disabled={confirmed}>
                                <option value="" key={0}>-- Chọn phương án giải quyết --</option>
                                {[Constant.CASE_ERROR_SIZE, Constant.CASE_ERROR_COLLOR, Constant.CASE_ERROR_PRODUCT].indexOf(model.case) != -1 && Constant.COMPLAINT_SOLUTIONS_ERROR.map(stt =>
                                    <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                                {[Constant.CASE_ERROR_INADEQUATE_PRODUCT].indexOf(model.case) != -1 && Constant.COMPLAINT_SOLUTIONS_INADEQUATE.map(stt =>
                                    <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                            </Field>
                        </div>
                    </div>
                    <div className={"row"}>
                        <div className={"col-sm-3 top-10px"}>
                            Số tiền shop bồi hoàn
                        </div>
                        <div className={"col-sm-3"}>
                            <Field
                                label={''}
                                component={TextInput}
                                name={model.case +".money_shop_return"}
                                validate={[Validator.requireFloat]}
                                unit={' vnđ'}
                                disabled={confirmed}
                            />
                        </div>
                        <div className={"col-sm-3 top-10px"} style={{textAlign:"right"}}>
                            Ngày shop hoàn tiền
                        </div>
                        <div className={"col-sm-3 "} >
                            <Field
                                label={''}
                                component={DatePickerInput}
                                onDateChange={(date) => {
                                    this.props.change(model.case + ".date_return_money", date.format("YYYY-MM-DD"));
                                }}
                                validate={[Validator.required]}
                                disabled={confirmed}
                                name={model.case + ".date_return_money"}
                            />
                        </div>
                    </div>
                    <Field
                        name={model.case + ".note"}
                        label={''}
                        component={TextArea}
                        disabled={confirmed}
                        rows="3"
                        validate={[Validator.required]}
                    />
                </fieldset>
        );
    }
}

ReturnCommodityForShop.propTypes = {
    model: PropTypes.object,
    fee_ship_inland : PropTypes.number,
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
    form: 'OrderringOfficeConfirm'
})(ReturnCommodityForShop))
