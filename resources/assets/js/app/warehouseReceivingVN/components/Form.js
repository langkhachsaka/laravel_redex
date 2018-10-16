import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import Constant from "../meta/constant";
import Select2Input from "../../../theme/components/Select2Input";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import SelectInput from "../../../theme/components/SelectInput";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import WarehouseConstant from "../../warehouse/meta/constant";
import AppConfig from "../../../config";
import UserConstant from "../../user/meta/constant";
import ShipmentConstant from "../../shipment/meta/constant";
import moment from "moment";
import Formatter from "../../../helpers/Formatter";

class Form extends Component {

    constructor(props) {
        super(props);

        this.state = {
            shipmentSelected : null,
            btnMapping : false,
        };
    }

    componentDidMount() {
        const {model} = this.props;
        if (model) {
            this.props.initialize(model);
            this.setState({
                shipmentSelected: model.shipment,
                approved: model.status == 2 ? true : false,
                reported: model.status == 3 ? true : false,
            });
        } else {
                this.props.initialize({
                user_receive_id: this.props.authUser.id,
                date_receiving : moment().format("YYYY-MM-DD"),
                warehouse_id: this.props.authUser.warehouse_id
            });
            
        }
    }

    handleSubmit(formProps) {
        const {model} = this.props;
        const {errorData} = this.props;
        if(!this.state.shipmentSelected){
            toastr.warning("Vui lòng kiểm tra lại mãi lô hàng");
            return;
        }
        if(this.state.btnMapping){
            var errorFields = this.checkMapping(errorData,formProps);
            if(errorFields.length == 0){
                swal({
                    title: "Xác nhận lô hàng đã nhận",
                    text: "Hệ thống xác nhận lô hàng đã trùng khớp. Bạn có chắc chắn muốn xác nhận lô hàng đã nhận này?",
                    icon: "warning",
                    buttons: {
                        cancel: "Hủy",
                        catch: {
                            text: "Xác nhận",
                            value: "approve",
                        },
                    },
                }).then((value) => {
                    if(value == 'approve') {
                        var request = {
                            id: model.id,
                        };
                        return ApiService.post(Constant.resourcePath('approve'), request)
                            .then(({data}) => {
                                this.props.setListState(({models}) => {
                                    return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                                });
                                swal(data.message);
                                this.submitData(model,formProps);
                            });
                    }
                });
            } else {
                swal({
                    title: "Báo cáo lô hàng đã nhận",
                    text: 'Hệ thống xác nhận lô hàng không trùng khớp về :'+errorFields.toString()+'. Bạn có chắc chắn muốn báo cáo lô hàng đã nhận không đúng này?',
                    icon: "warning",
                    buttons: {
                        cancel: "Hủy",
                        danger: {
                            text: "Báo cáo",
                            value: "report",
                        },
                    },
                    dangerMode: true,
                }).then((value) => {
                    if(value == 'report') {
                        return ApiService.post(Constant.resourcePath('report'), [formProps,errorFields.toString()])
                            .then(({data}) => {
                                this.props.setListState(({models}) => {
                                    return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                                });
                                this.setState({
                                    approved: false,
                                    reported: true,
                                });
                                swal(data.message);
                                this.submitData(model,formProps);
                            });
                    }
                });
            }
        } else {
            this.submitData(model,formProps);
        }

    }

    checkMapping(error, formProps) {
        var errorField = [];
        if(Math.abs(this.state.shipmentSelected.real_weight - formProps.weight) > error.error_weight ){
            errorField.push('khối lượng');
        }
        if(formProps.length && Math.abs(this.state.shipmentSelected.length - formProps.length) > error.error_size ){
            errorField.push('chiều dài');
        }
        if(formProps.width && Math.abs(this.state.shipmentSelected.width - formProps.width) > error.error_size ){
            errorField.push('chiều rộng');
        }
        if(formProps.height && Math.abs(this.state.shipmentSelected.height - formProps.height) > error.error_size ){
            errorField.push('chiều cao');
        }
        return errorField;
    }

    submitData(model,formProps){
        return ApiService.post(Constant.resourcePath(model.id), formProps)
            .then(({data}) => {
                this.props.setListState(({models}) => {
                    return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                });
                toastr.success(data.message);
                this.props.actions.closeMainModal();
            });

    }


    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;

        const {detail} = this.props;
        return (
            <div>
                <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                    <div className="row">
                        <div className="col-sm-3">
                            <Field
                                name="shipment_code"
                                component={Select2Input}
                                disabled = {model && true}
                                select2Data={model && model.shipment ? [{
                                    id: model.shipment_code,
                                    text: model.shipment_code
                                }] : []}
                                select2Options={{
                                    placeholder: 'Chọn lô hàng',
                                    allowClear: true,
                                    ajax: {
                                        url: AppConfig.API_URL + ShipmentConstant.resourcePath("list?status="+ShipmentConstant.STATUS_TRANSPORTED),
                                        delay: 250
                                    }
                                }}
                                select2OnSelect={(e) => {
                                    this.setState({
                                        shipmentSelected: e.params.data,
                                    });
                                }}
                                label="Lô hàng"
                                required={true}
                                validate={[Validator.required]}
                            />
                        </div>
                        <div className="col-sm-8">
                            {this.state.shipmentSelected ?
                                <div>
                                    Thông tin lô hàng :
                                    <div className="row">
                                        <div className="col-sm-3">
                                            <b>Mã lô hàng :</b> {this.state.shipmentSelected.shipment_code}
                                            <br/><b>Khối lượng thực :</b>  {this.state.shipmentSelected.real_weight ? this.state.shipmentSelected.real_weight : 0  } (kg)
                                            <br/><b>Khối lượng quy đổi:</b>  {this.state.shipmentSelected.conversion_weight ? this.state.shipmentSelected.conversion_weight : 0  } (kg)
                                        </div>
                                        <div className="col-sm-3">
                                            <b>Số kiện hàng đã nhập :</b> {this.state.shipmentSelected.shipment_item && this.state.shipmentSelected.shipment_item.length}
                                            <br/><b>Hình thức vận chuyển :</b> {this.state.shipmentSelected.transport_type && this.state.shipmentSelected.transport_type_name}
                                            <br/><b>Nơi nhận :</b>  {this.state.shipmentSelected.warehouse && this.state.shipmentSelected.warehouse.name}
                                        </div>
                                        <div className="col-sm-3">
                                            <b>Chiều cao :</b>  {this.state.shipmentSelected.height && this.state.shipmentSelected.height} (cm)
                                            <br/><b>Chiều rộng :</b>  {this.state.shipmentSelected.width && this.state.shipmentSelected.width} (cm)
                                            <br/><b>Chiều dài :</b> {this.state.shipmentSelected.length && this.state.shipmentSelected.length} (cm)
                                        </div>
                                    </div>
                                </div>
                            :
                                <p className = "red-color"><strong>Vui lòng chọn lô hàng </strong></p>
                            }
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-4">
                            <Field
                                name="user_receive_id"
                                disabled = {detail}
                                component={Select2Input}
                                select2Data={model && model.user_receive ? [{
                                    id: model.user_receive.id,
                                    text: model.user_receive.name
                                }] : [{
                                    id: this.props.authUser.id,
                                    text : this.props.authUser.name + " ( Bạn )"
                                }]}
                                select2Options={{
                                    placeholder: 'Tất cả',
                                    allowClear: true,
                                    ajax: {
                                        url: AppConfig.API_URL + UserConstant.resourcePath("list?role="+UserConstant.ROLE_USER_WAREHOUSE_VN),
                                        delay: 250
                                    }
                                }}
                                label="Người nhận hàng"
                                required={true}
                                validate={[Validator.required]}
                            />
                        </div>
                        <div className="col-sm-4">
                            <Field
                                name="warehouse_id"
                                component={Select2Input}
                                disabled = {detail}
                                select2Data={model && model.warehouse ? [{
                                    id: model.warehouse.id,
                                    text: model.warehouse.name
                                }] :(  this.props.authUser.warehouse && this.props.authUser.warehouse_id ?
                                    [{
                                        id : this.props.authUser.warehouse_id,
                                        text : this.props.authUser.warehouse.name,
                                    }] :
                                    []
                                )
                                }
                                select2Options={{
                                    placeholder: 'Tất cả',
                                    allowClear: true,
                                    ajax: {
                                        url: AppConfig.API_URL + WarehouseConstant.resourcePath("list?type="+WarehouseConstant.TYPE_VN),
                                        delay: 250
                                    }
                                }}
                                label="Kho hàng"
                                required={true}
                                validate={[Validator.required]}
                            />
                        </div>
                        <div className="col-sm-4">
                            <Field
                                name="date_receiving"
                                disabled = {detail}
                                component={DatePickerInput}

                                onDateChange={(date) => {
                                    this.props.change("date_receiving", date.format("YYYY-MM-DD"));
                                }}
                                label="Ngày nhận hàng"
                                required={true}
                                validate={[Validator.required]}
                            />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-3">
                            <Field
                                name="weight"
                                disabled = {detail}
                                component={TextInput}
                                label="Khối lượng (kg)"
                                required={true}
                                validate={[Validator.requireFloat, Validator.greaterThan0,Validator.required]}
                            />
                        </div>
                        <div className="col-sm-3">
                            <Field
                                name="height"
                                disabled = {detail}
                                component={TextInput}
                                label="Chiều cao"
                            />
                        </div>
                        <div className="col-sm-3">
                            <Field
                                name="width"
                                disabled = {detail}
                                component={TextInput}
                                label="Chiều rộng"
                            />
                        </div>
                        <div className="col-sm-3">
                            <Field
                                name="length"
                                disabled = {detail}
                                component={TextInput}
                                label="Chiều dài"
                            />
                        </div>
                    </div>

                    {!detail &&
                    <div className="form-group">
                        <div style={{display : 'inline'}}>
                            <button type="submit" name="submit" value="submit" className="btn btn-lg btn-primary" onClick={() => {
                                this.setState({
                                    btnMapping: false,
                                })
                            }
                            } disabled={submitting || pristine}>
                                <i className="fa fa-fw fa-check"/>
                                Cập nhật
                            </button>
                        </div>
                        <div style={{display : 'inline', float : 'right'}}>
                            <button type="submit" name="submit" value="submit" className="btn btn-lg btn-info" onClick={() => {
                                this.setState({
                                    btnMapping: true,
                                })
                            }
                            } >
                                <i className="fa fa-fw fa-check"/>
                                So sánh
                            </button>
                        </div>
                    </div>}

                </form>
                {model  && <div style={{marginTop: '10px'}}>
                    <strong>Danh sách các kiện hàng được đã được nhập vào lô hàng này</strong>

                    <table className="table table-hover">
                    <thead>
                    <tr>
                        <th>Mã vận đơn</th>
                        <th>Loại đơn hàng</th>
                        <th>Mã</th>
                        <th>Khối lượng</th>
                        <th>Chiều cao</th>
                        <th>Chiều dài</th>
                        <th>Chiều rộng</th>
                        <th> </th>
                    </tr>
                    </thead>
                    <tbody>
                    {
                    model.shipment.shipment_item.map(item => {
                        return (
                            <tr key={item.id}>
                                <td>{item.bill_of_lading_code}</td>
                                <td>{item.bill_of_lading && item.bill_of_lading.lading_code
                                && item.bill_of_lading.lading_code.ladingcodetable_type =='Modules\\CustomerOrder\\Models\\CustomerOrderItem' ? 'Đơn hàng Việt Nam' : 'Đơn hàng vận chuyển'}</td>
                                <td>{item.bill_of_lading && item.bill_of_lading.lading_code && item.bill_of_lading.lading_code.ladingcodetable_id}</td>
                                <td>{item.bill_of_lading.weight} (kg)</td>
                                <td>{item.bill_of_lading.height ? item.bill_of_lading.height : 0} (cm)</td>
                                <td>{item.bill_of_lading.width ? item.bill_of_lading.width : 0} (cm)</td>
                                <td>{item.bill_of_lading.length ? item.bill_of_lading.length : 0} (cm)</td>
                                {/*<td className="column-actions">
                            { <DetailActionButtons model={model} shipmentItem = {item} setListState={this.setState.bind(this)}/>}
                        </td>*/}
                            </tr>
                        );
                    })}
                    {
                        <tr>
                            <td><b>Tổng</b></td>
                            <td>
                                Khối lượng: <b>{ Formatter.number(model.shipment.shipment_item
                                .map(item => item.bill_of_lading.weight)
                                .reduce((a, b) => a + b, 0))} kg</b>
                            </td>
                        </tr> }
                    </tbody>

                    </table>
                </div> }
        </div>
        );
    }
}

Form.propTypes = {
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
})(Form))
