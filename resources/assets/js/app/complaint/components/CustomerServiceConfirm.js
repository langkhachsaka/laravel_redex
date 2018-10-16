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
import SolutionReceiveReturnMoney from "./SolutionReceiveReturnMoney";
import ReturnCommodityForShop from "./ReturnCommodityForShop";
import ShopAddMissingProduct from "./ShopAddMissingProduct";
import ShopReturnMoneyForMissingProduct from "./ShopReturnMoneyForMissingProduct";
import SelectInput from "../../../theme/components/SelectInput";

const CheckboxInput = ({input, label, checked, className}) => (
    <div className={className}>
        <label>{label} :</label>  <input {...input} type="checkbox" checked={checked} className="checkbox-item-verify"/>
    </div>
);

class CustomerServiceConfirm extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedFile: null,
            redirectToDetailId: null,
            redirectToComplaintList : false,
        };
    }
    handleSubmit(formProps) {
        var formData = {};
        _.forOwn(formProps, (value, key) => {
            if(key == 'solution' || key == 'customer_comment'){
                formData[key]=value;
            }
        });
        const {model} = this.props;
        if(!this.validateFormData(formData)) return;
        return ApiService.post(Constant.resourcePath("customerServiceConfirm/"+model.id), formData)
            .then(({data}) => {
                toastr.success(data.message);
                this.setState({
                    isLoading: false,
                    redirectToComplaintList: true,
                });
            });
    }

    validateFormData(formData){
        const {model} = this.props;
        if(!formData['solution']){
            toastr.warning('Vui lòng chọn phương án giải quyết');
            return false;
        }
        if(model.error_size && !formData['solution'][Constant.CASE_ERROR_SIZE]){
            toastr.warning('Vui lòng chọn phương án giải quyết ở tab Sai Cỡ');
            return false;
        }
        if(model.error_collor && !formData['solution'][Constant.CASE_ERROR_COLLOR]){
            toastr.warning('Vui lòng chọn phương án giải quyết ở tab Sai Màu');
            return false;
        }
        if(model.error_product && !formData['solution'][Constant.CASE_ERROR_PRODUCT]){
            toastr.warning('Vui lòng chọn phương án giải quyết ở tab Sai Hàng');
            return false;
        }
        if(model.inadequate_product && !formData['solution'][Constant.CASE_ERROR_INADEQUATE_PRODUCT]){
            toastr.warning('Vui lòng chọn phương án giải quyết ở tab Thiếu Hàng');
            return false
        }
        return true;
    }
    componentDidMount() {
        const {model} = this.props;
        let initData = this.props.initValues || {};
        if (model) {
            initData = _.assign(initData, model);
        }
        this.props.initialize(initData);
    }

    changeData(model){
        if(model.status >= Constant.STATUS_CUSTOMER_SERVICE_PROCESSED) {
            model.case_complaint.map(item =>{
                this.props.change("customer_comment."+ item.case,item.customer_comment);
                this.props.change("solution."+item.case,item.solution);
            })
        }
    }


    render() {
        if(this.state.redirectToComplaintList ){
            return <Redirect to={"/complaint"}/>;
        }
        const {model,rate,text,first,case_error,admin_comment,handleSubmit} = this.props;
        this.changeData(model);
        this.props.change("admin_comment." + case_error,admin_comment);
        let listError = [];
        let firstError = 0;
        model.error_size ? firstError=1 : model.error_collor ? firstError=2 :
            model.error_product ? firstError=3 : model.inadequate_product ? firstError=4 : 0;
        let item = {};
        if (model.error_size == 1) {
            item = { admin_comment : model.comment_error_size, tab_id: 'error_size_tab',case_error:Constant.CASE_ERROR_SIZE,first: firstError == 1 ? true : false, text:"Sai cỡ"};
            listError.push(item);
        };
        if(model.error_collor  == 1){
            item = {admin_comment : model.comment_error_collor, tab_id: 'error_collor_tab',case_error:Constant.CASE_ERROR_COLLOR,first: firstError == 2 ? true : false, text:"Sai màu"};
            listError.push(item);
        };
        if(model.error_product  == 1){
            item = {admin_comment : model.comment_error_product, tab_id: 'error_product_tab',case_error:Constant.CASE_ERROR_PRODUCT,first: firstError == 3 ? true : false, text:"Sai hàng"};
            listError.push(item);
        };
        if(model.inadequate_product  == 1){
            item = {admin_comment : model.comment_inadequate_product, tab_id: 'inadequate_product_tab',case_error:Constant.CASE_ERROR_INADEQUATE_PRODUCT,first: firstError == 4 ? true : false, text:"Thiếu hàng"};
            listError.push(item);
        };
        listError.map(item => {
            this.props.change('admin_comment.' + item.case_error,item.admin_comment);
            this.props.change('verify_customer_item_note.' + item.case_error,model.verify_customer_order_item.note);
        });

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <div>
                    {listError.length > 1 &&
                    <ul className="nav nav-tabs" role="tablist">
                        { model.error_size == 1 && <li className="nav-item">
                            <a className="nav-link active" href="#error_size_tab" role="tab" data-toggle="tab">Sai cỡ</a>
                        </li>}
                        {model.error_collor == 1 && <li className="nav-item">
                            <a className="nav-link" href="#error_collor_tab" role="tab" data-toggle="tab">Sai màu</a>
                        </li>}
                        {model.error_product == 1 && <li className="nav-item">
                            <a className="nav-link" href="#error_product_tab" role="tab" data-toggle="tab">Sai hàng</a>
                        </li>}
                        {model.inadequate_product == 1 && <li className="nav-item">
                            <a className="nav-link" href="#inadequate_product_tab" role="tab" data-toggle="tab">Thiếu hàng</a>
                        </li>}
                    </ul> }
                    <div className="tab-content">
                        {listError.map(item => {
                                return (
                                    <div key={item.case_error} role="tabpanel" className={item.first ?"tab-pane fade in active show" : "tab-pane fade in"} id={item.tab_id}>
                                    <fieldset className="fiedset-verify-customer-order bgc-grey">
                                        <legend  className="legend-account-deposited">CSKH & KH Confirm về {item.text}:</legend>
                                        <div className={"row"}>
                                            <div className="col-sm-3">
                                                Số lượng đặt: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}} value={model.customer_order_item && model.customer_order_item.quantity ? model.customer_order_item.quantity : '' } className="form-control"/>
                                            </div>
                                            <div className="col-sm-3">
                                                Số lượng về: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}} value={model.verify_customer_order_item && model.verify_customer_order_item.quantity_verify ? model.verify_customer_order_item.quantity_verify : ''} className="form-control"/>
                                            </div>
                                            <div className="col-sm-3">
                                                Số lượng thiếu: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}}
                                                                        value={model.customer_order_item.quantity - model.verify_customer_order_item.quantity_verify} className="form-control"/>
                                            </div>
                                            <div className="col-sm-3">
                                                Chiết khấu: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}} value={model.customer_order_item && model.customer_order_item.discount_percent ? model.customer_order_item.discount_percent : '' +'%'} className="form-control"/>
                                            </div>
                                        </div>
                                        <Field
                                            name={"verify_customer_item_note." + item.case_error}
                                            label={''}
                                            component={TextArea}
                                            disabled={true}
                                            rows="3"
                                        />
                                        <Field
                                            name={"admin_comment." + item.case_error}
                                            label={'Ý kiến Admin'}
                                            disabled={true}
                                            component={TextArea}
                                            rows="3"
                                        />
                                        <div className={"row"}>
                                            <div className={"col-sm-3"}>
                                                Ý kiến Khách Hàng
                                            </div>
                                            <div className={"col-sm-5"}>
                                                <Field
                                                    name={"solution."+item.case_error}
                                                    component={SelectInput}
                                                    disabled={model.status==Constant.STATUS_ADMIN_PROCESSED ? false : true}>
                                                    <option value="" key={0}>-- Chọn phương án giải quyết --</option>
                                                    {[Constant.CASE_ERROR_SIZE, Constant.CASE_ERROR_COLLOR, Constant.CASE_ERROR_PRODUCT].indexOf(item.case_error) != -1 && Constant.COMPLAINT_SOLUTIONS_ERROR.map(stt =>
                                                        <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                                                    {[Constant.CASE_ERROR_INADEQUATE_PRODUCT].indexOf(item.case_error) != -1 && Constant.COMPLAINT_SOLUTIONS_INADEQUATE.map(stt =>
                                                        <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                                                </Field>
                                            </div>
                                        </div>
                                        <Field
                                            name={"customer_comment."+ item.case_error}
                                            label={''}
                                            disabled={model.status==Constant.STATUS_ADMIN_PROCESSED ? false : true}
                                            component={TextArea}
                                            rows="3"
                                        />
                                        <div style={{background:"white"}}>
                                            Comment CSKH & KH:
                                            <div className={"comment-cskh-kh"}>

                                            </div>
                                        </div>
                                    </fieldset>
                                        <hr/>
                                    {model.status == Constant.STATUS_ORDER_OFFICER_PROCESSED && this.props.authUser.roles.indexOf(Constant.ROLE_ADMIN) != -1 &&
                                    <div>
                                        {model.case_complaint && model.case_complaint.map(item2 =>{
                                            return (
                                                item2.case == item.case_error &&
                                                <div key={item.id}>
                                                    {item2.solution == Constant.SOLUTION_BACK_COMMODITY && <ReturnCommodityForShop model={item2} confirmed={model.status == Constant.STATUS_ORDER_OFFICER_PROCESSED ? true : false} rate={rate} rootModel={model}/>}
                                                    {item2.solution == Constant.SOLUTION_RECEIVE_PRODUCT && <SolutionReceiveReturnMoney model={item2} confirmed={model.status == Constant.STATUS_ORDER_OFFICER_PROCESSED ? true : false}  rootModel={model}/>}
                                                    {item2.solution == Constant.SOLUTION_SHOP_ADD_MISSING_PRODUCT && <ShopAddMissingProduct model={item2} confirmed={model.status == Constant.STATUS_ORDER_OFFICER_PROCESSED ? true : false}  rootModel={model}/>}
                                                    {item2.solution == Constant.SOLUTION_SHOP_RETURN_MONEY && <ShopReturnMoneyForMissingProduct model={item2} confirmed={model.status == Constant.STATUS_ORDER_OFFICER_PROCESSED ? true : false}  rootModel={model}/>}
                                                </div>
                                            )
                                        })}
                                    </div>}
                                </div>);
                            })
                        }
                    </div>
                </div>
                {this.props.authUser.roles.indexOf(Constant.ROLE_CUSTOMER_SERVICE_OFFICER ) != -1 && model.status == Constant.STATUS_ADMIN_PROCESSED &&
                <div style={{display: 'inline', float: 'right'}}>
                    <button type="submit" name="submit" value="submit" className="btn btn-lg btn-warning">
                        <i className="fa fa-fw fa-check"/>
                        Cập nhật
                    </button>
                </div>
                }
            </form>
        );
    }
}

CustomerServiceConfirm.propTypes = {
    model: PropTypes.object,
};

function mapStateToProps({auth}) {
    return {
        userPermissions: auth.permissions,
        authUser : auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'CustomerServiceConfirm'
})(CustomerServiceConfirm))
