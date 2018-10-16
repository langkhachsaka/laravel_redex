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
import Card from "../../../theme/components/Card";

const CheckboxInput = ({input, label, checked, className}) => (
    <div className={className}>
        <label>{label} :</label>  <input {...input} type="checkbox" checked={checked} className="checkbox-item-verify"/>
    </div>
);

class OrderringOfficeConfirm extends Component {

    constructor(props) {
        super(props);

        this.state = {
            isLoading : false,
            selectedFile: null,
            redirectToDetailId: null,
            redirectToComplaintList : false,
        };
    }
    handleSubmit(formProps) {
        var formData = {};
        _.forOwn(formProps, (value, key) => {
            if(key == '1' || key == '2'|| key == '3'|| key == '4'){
                formData["case_"+key]=value;
            }
        });

        if(!this.validateFormData(formData)) return;

        this.setState({
            isLoading: true
        });
        const {model} = this.props;
        return ApiService.post(Constant.resourcePath("orderOfficerConfirm/"+model.id), formData)
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
        if(formData['case_1'] && (!formData['case_1']['order_office_solution'] || !formData['case_1']['redex_solution']) ){
            toastr.warning('Vui lòng chọn đầy đủ phương án giải quyết ở tab Sai Cỡ');
            return false;
        }
        if(formData['case_2'] && (!formData['case_2']['order_office_solution'] || !formData['case_2']['redex_solution'])){
            toastr.warning('Vui lòng chọn đầy đủ phương án giải quyết ở tab Sai Màu');
            return false;
        }
        if(formData['case_3'] && (!formData['case_3']['order_office_solution'] || !formData['case_3']['redex_solution'])){
            toastr.warning('Vui lòng chọn đầy đủ phương án giải quyết ở tab Sai Hàng');
            return false;
        }
        if(formData['case_4'] && (!formData['case_4']['order_office_solution'] || !formData['case_4']['redex_solution'])){
            toastr.warning('Vui lòng chọn đầy đủ phương án giải quyết ở tab Thiếu Hàng');
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



    render() {
        if(this.state.redirectToComplaintList ){
            return <Redirect to={"/complaint"}/>;
        }
        const {model,rate,text,first,case_error,admin_comment,handleSubmit} = this.props;
        return (
            <Card isLoading={this.state.isLoading}>
                <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <div>
                    {model.case_complaint && model.case_complaint.length > 1 &&
                    <ul className="nav nav-tabs" role="tablist">
                        {model.error_size == 1 && <li className="nav-item">
                            <a className="nav-link active" href="#case1" role="tab" data-toggle="tab">Sai cỡ</a>
                        </li>}
                        {model.error_collor == 1 && <li className="nav-item">
                            <a className="nav-link" href="#case2" role="tab" data-toggle="tab">Sai màu</a>
                        </li>}
                        {model.error_product == 1 && <li className="nav-item">
                            <a className="nav-link" href="#case3" role="tab" data-toggle="tab">Sai hàng</a>
                        </li>}
                        {model.inadequate_product == 1 && <li className="nav-item">
                            <a className="nav-link" href="#case4" role="tab" data-toggle="tab">Thiếu hàng</a>
                        </li>}
                    </ul> }
                    <div className="tab-content">
                        {model.case_complaint && model.case_complaint.map((item,index) =>{
                            return (
                                <div key={item.id} role="tabpanel" className={index == 0 ?"tab-pane fade in active show" : "tab-pane fade in"} id={"case"+item.case}>
                                    {item.solution == Constant.SOLUTION_BACK_COMMODITY && <ReturnCommodityForShop model={item} confirmed={model.status == Constant.STATUS_ORDER_OFFICER_PROCESSED ? true : false} rate={rate} rootModel={model}/>}
                                    {item.solution == Constant.SOLUTION_RECEIVE_PRODUCT && <SolutionReceiveReturnMoney model={item} confirmed={model.status == Constant.STATUS_ORDER_OFFICER_PROCESSED ? true : false} rootModel={model}/>}
                                    {item.solution == Constant.SOLUTION_SHOP_ADD_MISSING_PRODUCT && <ShopAddMissingProduct model={item} confirmed={model.status == Constant.STATUS_ORDER_OFFICER_PROCESSED ? true : false} rootModel={model}/>}
                                    {item.solution == Constant.SOLUTION_SHOP_RETURN_MONEY && <ShopReturnMoneyForMissingProduct model={item} confirmed={model.status == Constant.STATUS_ORDER_OFFICER_PROCESSED ? true : false} rootModel={model}/>}
                                </div>
                            )
                        })}
                    </div>
                </div>

                {this.props.authUser.roles.indexOf(Constant.ROLE_ORDERING_SERVICE_OFFICER) != -1 && model.status == Constant.STATUS_CUSTOMER_SERVICE_PROCESSED &&
                <div style={{display: 'inline', float: 'right'}}>
                    <button type="submit" name="submit" value="submit" className="btn btn-lg btn-warning">
                        <i className="fa fa-fw fa-check"/>
                        Cập nhật
                    </button>
                </div>
                }
            </form>
            </Card>
        );
    }
}

OrderringOfficeConfirm.propTypes = {
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
    form: 'OrderringOfficeConfirm'
})(OrderringOfficeConfirm))
