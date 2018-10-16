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
import DatePickerInput from "../../../theme/components/DatePickerInput";
import SelectInput from "../../../theme/components/SelectInput";


const CheckboxInput = ({input, label, checked, className}) => (
    <div className={className}>
        <label>{label} :</label>  <input {...input} type="checkbox" checked={checked} className="checkbox-item-verify"/>
    </div>
);

class ShopReturnMoneyForMissingProduct extends Component {

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
        const {model,rootModel, confirmed} = this.props;
        this.changeData(model,rootModel);
        return (

                <fieldset className="fiedset-verify-customer-order bgc-grey">
                    <legend  className="legend-account-deposited">Shop hoàn tiền hàng thiếu</legend>
                    <div className={"row"}>
                        <div className="col-sm-3">
                            Số lượng đặt: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}}
                                                  value={rootModel.customer_order_item ? rootModel.customer_order_item.quantity   : ''} className="form-control"/>
                        </div>
                        <div className="col-sm-3">
                            Số lượng về: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}}
                                                 value={rootModel.verify_customer_order_item ? rootModel.verify_customer_order_item.quantity_verify   : ''} className="form-control"/>
                        </div>
                        <div className="col-sm-3">
                            Số lượng thiếu: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}}
                                                    value={rootModel.customer_order_item.quantity - rootModel.verify_customer_order_item.quantity_verify} className="form-control"/>
                        </div>

                    </div>

                    <Field
                        name={model.case + ".verify_customer_order_item_note"  }
                        label={''}
                        disabled={true}
                        component={TextArea}
                        rows="3"
                    />
                    <div className={"row"}>
                        <div className={"col-sm-3 top-10px"}>
                            Ý kiến Redex
                        </div>
                        <div className={"col-sm-5"}>
                            <Field
                                name={model.case + ".redex_solution"}
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
                                disabled={confirmed}
                                validate={[Validator.requireFloat]}
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
                                disabled={confirmed}
                                name={model.case + ".date_return_money"}
                                validate={[Validator.required]}
                            />
                        </div>
                    </div>
                    <Field
                        name={model.case + ".note"}
                        label={''}
                        component={TextArea}
                        rows="3"
                        disabled={confirmed}
                        validate={[Validator.required]}
                    />
                </fieldset>
        );
    }
}

ShopReturnMoneyForMissingProduct.propTypes = {
    model: PropTypes.object,
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
})(ShopReturnMoneyForMissingProduct))
