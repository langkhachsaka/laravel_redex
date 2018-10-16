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
import ArrayHepper from "../../../helpers/Array";
import {toastr} from 'react-redux-toastr';
import SelectInput from "../../../theme/components/SelectInput";
import Select2Input from "../../../theme/components/Select2Input";
import UserConstant from "../../user/meta/constant";
import AppConfig from "../../../config";
import * as authActions from "../../../app/auth/meta/action";
import {Link} from "react-router-dom";
import RichTextEditor from 'react-rte';

class Form extends Component {

    constructor(props) {
        super(props);
        this.state = {
            taskId: null,
            customerOrderId: null,
            customer: {
                customerName: null,
                customerShippingName:null,
                customerShippingAddress: null,
                customerShippingPhone: null,
            },
            value: RichTextEditor.createEmptyValue(),
            customerDeposite : false,
        };
    }

    static propTypes = {
        onChange: PropTypes.func
    };

    onChange = (value) => {
        this.setState({value});
        
        if (this.props.onChange) {
          this.props.onChange(
            value.toString('html')
          );
        }
    };

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
                    },
                })
            }
            this.setState({
                value: RichTextEditor.createValueFromString(model.description,'html'),
            })         
        }
    }
    
    handleSubmit(formProps) {
        const {model} = this.props;

        const formData = new FormData();
        _.forOwn(formProps, (value, key) => {
            formData.append(key, value);
        });
        formData.append('description', this.state.value.toString('html'));
        formData.delete('creator_id');
        formData.delete('customer_order_id');
        formData.delete('complaint_id');
        if(window.location.pathname.split("/")[3]){
            return ApiService.post(Constant.resourcePath(model.id), formData)
            .then(({data}) => {
                if(data.data.length == 0) {
                    toastr.warning(data.message);
                } else {
                    toastr.success(data.message);
                    this.props.setDetailState({model : data.data});
                    this.props.actions.closeMainModal();
                }
            });
        } else {
            return ApiService.post(Constant.resourcePath(model.id), formData)
                .then(({data}) => {
                    if(data.data.length == 0) {
                        toastr.warning(data.message);
                    } else {
                        this.props.setListState(({models}) => {
                            return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                        });
                        toastr.success(data.message);
                        this.props.actions.closeMainModal();
                    }
                    
                })
        }
               
    }


    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;
        //CHECK HAVE COMPLAINT OR NORMAL ORDER
        var isComplaintTask = Constant.COMPLAINT_TASKS_STATUS.indexOf(model.status) != -1 ? true : false;
        var disabledStatusSelect = [Constant.TYPE_RECEIVE,Constant.TYPE_VERIFY,Constant.TYPE_COMPLAINT].indexOf(model.task_type) != -1;
        const toolbarConfig = {
            // Optionally specify the groups to display (displayed in the order listed).
            display: ['INLINE_STYLE_BUTTONS', 'BLOCK_TYPE_BUTTONS', 'LINK_BUTTONS', 'HISTORY_BUTTONS'],
            INLINE_STYLE_BUTTONS: [
              {label: 'Bold', style: 'BOLD', className: 'custom-css-class'},
              {label: 'Italic', style: 'ITALIC'},
              {label: 'Underline', style: 'UNDERLINE'}
            ],
            
            BLOCK_TYPE_BUTTONS: [
              {label: 'UL', style: 'unordered-list-item'},
              {label: 'OL', style: 'ordered-list-item'}
            ]
        };




        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                
                
                <div className="row">
                    <div className="col-sm-3">
                        <Field
                            name="customer_order_id"
                            component={TextInput}
                            label="Mã đơn hàng"
                            disabled = {true}
                            /*onChange = {e => {
                                const id = e.target.value.trim().replace(/\s/g, '');
                                
                                if(id){

                                    ApiService.get(Constant.resourcePath("getInfoOrder/"+id)).then(({data}) => {
                                    
                                    if(data.message == 'success'){
                                       if(data.data){
                                            this.setState({
                                                customer: {
                                                    customerName: data.data.customer.name,
                                                    customerShippingName:data.data.customer_shipping_name,
                                                    customerShippingAddress: data.data.customer_shipping_address,
                                                    customerShippingPhone: data.data.customer_shipping_phone,
                                                },
                                                taskId: null,
                                            });
                                        }  else {
                                            this.setState({
                                                customer: {
                                                    customerName: 'Mã đơn hàng không tồn tại',
                                                    customerShippingName:'Mã đơn hàng không tồn tại',
                                                    customerShippingAddress: 'Mã đơn hàng không tồn tại',
                                                    customerShippingPhone: 'Mã đơn hàng không tồn tại',
                                                },
                                                taskId: null,
                                            });
                                        }
                                    } else {

                                         this.setState({
                                                customer: {
                                                    customerName: 'Đã tồn tại nhiệm vụ cho đơn hàng này',
                                                    customerShippingName:'Đã tồn tại nhiệm vụ cho đơn hàng này',
                                                    customerShippingAddress: 'Đã tồn tại nhiệm vụ cho đơn hàng này',
                                                    customerShippingPhone: 'Đã tồn tại nhiệm vụ cho đơn hàng này',
                                                },
                                                taskId: data.data,
                                                customerOrderId : id,
                                            });
                                    }   
                                    
                                    
                                })    
                            }
                               

                            }}*/
                            // required={true}
                            // validate={[Validator.required]}
                        />
                    </div>
                    <div className="col-sm-9">
                        <div className="row">
                            <div className="col-sm-3">
                                 Tên khách hàng :  
                            </div>
                            <div className="col-sm-3">
                                <input disabled placeholder="Nhập mã đơn hàng" className="form-control" value={this.state.customer.customerName || "" } />
                            </div>
                             <div className="col-sm-3">
                                 Tên người nhận hàng :  
                             </div>
                             <div className="col-sm-3">
                                <input disabled placeholder="Nhập mã đơn hàng" className="form-control"  value = {this.state.customer.customerShippingName || "" } />
                             </div>
                        </div>

                        <div className="row">
                            <div className="col-sm-3">
                                 Địa chỉ nhận hàng :  
                            </div>
                            <div className="col-sm-3">
                               <input disabled placeholder="Nhập mã đơn hàng" className="form-control" value = {this.state.customer.customerShippingAddress || "" }  />
                            </div>
                        
                            <div className="col-sm-3">
                                 Số điện thoại :  
                            </div>
                            <div className="col-sm-3">
                               <input disabled placeholder="Nhập mã đơn hàng" className="form-control"  value = {this.state.customer.customerShippingPhone || "" }/> 
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
               
                <div className= 'rich-text'>
                    <h5>Mô tả <span className="text-danger">*</span> : </h5>
                    <div className="controls ">
                        <RichTextEditor
                            toolbarConfig={toolbarConfig}
                            value={this.state.value}
                            onChange={this.onChange}
                        />
                    </div>
                </div>
                

                <div className="row">
                    <div className="col-sm-4">
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
                                    url: AppConfig.API_URL + Constant.resourcePath("listUser?task_type=" + model.task_type),
                                    delay: 250
                                }
                            }}
                            label="Người thực hiện"
                            required={true} 
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="col-sm-2">
                        <Field 
                            name="status" 
                            component={SelectInput}
                            label="Trạng thái"
                            disabled={disabledStatusSelect}
                            required={true}
                            onChange = {e => {
                                console.log(e.target.value);
                                if(e.target.value == 21 ){
                                    this.setState({
                                        customerDeposite : true
                                    })
                                } else {
                                    this.setState({
                                        customerDeposite : false,
                                    })
                                }
                                }
                            }
                            validate={[Validator.required]}>

                            <option value="" key={0}>-- Chọn trạng thái --</option>
                            {/*Constant.TASK_STATUSES.map(stt => <option  hidden={isManager ? "" : isComplaintTask ? stt.hiddenOrder :isAccountant ? stt.displayAccountant : stt.hiddenComplaint} value={stt.id} key={stt.id} >{stt.text}</option>)*/}
                            {Constant.TASK_STATUSES.map(stt => stt.taskTypeDisplay.indexOf(model.task_type) != -1 && <option  value={stt.id} key={stt.id} >{stt.text}</option>)}
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
                            validate={[Validator.required]}
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
                        />
                    </div>
                </div>

                {/* Display input for deposite */}
                {ArrayHepper.twoArrayHasSameElement(this.props.authUser.roles,Constant.TYPE_ACCOUNTANT_ROLE) &&
                model.task_type == Constant.TYPE_ACCOUNTANT &&
                <div className="row">
                   <fieldset className="account-deposited">
                       <legend  className="legend-account-deposited">Thông tin đặt cọc  </legend>
                       <div className="row">
                           <div className="col-sm-3">
                               <Field
                                   name="money"
                                   component={TextInput}
                                   label="Số tiền"
                                   required={this.state.customerDeposite}
                                   validate={this.state.customerDeposite == true &&[Validator.required]}
                               />
                           </div>
                       </div>
                       <div>
                           <Field
                               name="note"
                               component={TextArea}
                               label="Nội dung"
                               required={this.state.customerDeposite}
                               validate={this.state.customerDeposite == true &&[Validator.required]}
                           />
                       </div>
                   </fieldset>

               </div>}
                {model.task_type != Constant.TYPE_ACCOUNTANT && <div>
                    <Field 
                        name="comment" 
                        component={TextArea} 
                        label="Bình luận" 
                    />
                </div>
                }
                


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

Form.propTypes = {
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
})(Form))
