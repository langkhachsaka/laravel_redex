import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import TextArea from "../../../theme/components/TextArea";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr';
import SelectInput from "../../../theme/components/SelectInput";
import Select2Input from "../../../theme/components/Select2Input";
import UserConstant from "../../user/meta/constant";
import AppConfig from "../../../config";
import * as authActions from "../../../app/auth/meta/action";

class FormUpdate extends Component {

        constructor(props) {
        super(props);

        this.state = {
            customer: {
                customerName: null,
                customerShippingName:null,
                customerShippingAddress: null,
                customerShippingPhone: null,
            },
        };
    }
    componentDidMount() {
        const {model} = this.props;

        if (model) {
            this.props.initialize(model);
            if(model.customer_order){
                this.setState({
                    customer: {
                        customerName: model.customer_order.customer.name,
                        customerShippingName:model.customer_order.customer_shipping_name,
                        customerShippingAddress: model.customer_order.customer_shipping_address,
                        customerShippingPhone: model.customer_order.customer_shipping_phone,
                    }
                })
            } 
        } 
    }

    handleSubmit(formProps) {
        const {model} = this.props; 
        return ApiService.post(Constant.resourcePath(model.id), formProps)
            .then(({data}) => {
                
                toastr.success(data.message);
                this.props.actions.closeMainModal();
                window.location.reload();
            });
        
    }

    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;
        
        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                
                <div className="row">
                    <div className="col-sm-3">
                        <Field
                            name="customer_order_id"
                            component={TextInput}
                            label="Mã đơn hàng"
                            disabled = {true}
                        />
                    </div>
                    <div className="col-sm-9">
                        <div className="row">
                            <div className="col-sm-3">
                                 Tên khách hàng :  
                            </div>
                            <div className="col-sm-3">
                                <input disabled className="form-control" value={this.state.customer.customerName || "" } />
                            </div>
                             <div className="col-sm-3">
                                 Tên người nhận hàng :  
                             </div>
                             <div className="col-sm-3">
                                <input disabled className="form-control"  value = {this.state.customer.customerShippingName || "" } />
                             </div>
                        </div>

                        <div className="row">
                            <div className="col-sm-3">
                                 Địa chỉ nhận hàng :  
                            </div>
                            <div className="col-sm-3">
                               <input disabled className="form-control" value = {this.state.customer.customerShippingAddress || "" }  />
                            </div>
                        
                            <div className="col-sm-3">
                                 Số điện thoại :  
                            </div>
                            <div className="col-sm-3">
                               <input disabled className="form-control"  value = {this.state.customer.customerShippingPhone || "" }/> 
                            </div>
                        </div>
                    </div>
                </div>

                <Field
                    name="title"
                    component={TextInput}
                    label="Tiêu đề"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="description"
                    component={TextArea}
                    label="Mô tả"
                    required={true}
                    validate={[Validator.required]}
                />

                <div className="row">
                    <div className="col-sm-3">
                            <Field
                                name="performer_id"
                                component={Select2Input}
                                select2Data={model && model.user_performer ? [{
                                    id: model.user_performer.id,
                                    text: model.user_performer.name
                                }] : []}
                                select2Options={{
                                    placeholder: 'Tất cả',
                                    allowClear: true,
                                    ajax: {
                                        url: AppConfig.API_URL + UserConstant.resourcePath("list"),
                                        delay: 250
                                    }
                                }}
                                label="Người thực hiện"
                            />
                    </div>
                    <div className="col-sm-3">
                        <Field 
                            name="status" 
                            component={SelectInput}
                            label="Trạng thái">
                            {Constant.TASK_STATUSES.map(stt => <option value={stt.id}
                                                                        key={stt.id}>{stt.text}</option>)}
                        </Field>
                    </div>
                     <div className="col-sm-3">
                        <Field
                            name="start_date"
                            component={DatePickerInput}
                            onDateChange={(date) => {
                                this.props.change("start_date", date.format("YYYY-MM-DD"));
                            }}
                            label="Ngày bắt đầu"
                            required={true}
                        />
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="end_date"
                            component={DatePickerInput}
                            onDateChange={(date) => {
                                this.props.change("end_date", date.format("YYYY-MM-DD"));
                            }}
                            label="Ngày kết thúc"
                            required={true}
                        />
                    </div>
                </div>

               
                <div>
                    <Field 
                        name="comment" 
                        component={TextArea} 
                        label="Bình luận" 
                    />
                </div>
                


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

FormUpdate.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
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
    form: 'ShopForm'
})(FormUpdate))
