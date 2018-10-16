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
import CheckboxInput from "../../../theme/components/CheckboxInput";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import SelectInput from "../../../theme/components/SelectInput";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import WarehouseConstant from "../../warehouse/meta/constant";
import AppConfig from "../../../config";
import ShipmentConstant from "../../shipment/meta/constant";
import moment from "moment";

class CreateShipmentForm extends Component {

    constructor(props) {
        super(props);

        this.state = {
            models : this.props.model,
            allowSelect : true,
            shipmentSelected: null,
            shipmentCode : null,
        };
    }

    componentDidMount() {

    }

    handleSubmit(formProps) {

        if (!formProps.shipment_code && this.state.allowSelect == true) {
            toastr.warning("Chọn lô hàng hoạc tạo mới lô hàng ");
            return;
        }
        const formData = new FormData();
        _.forOwn(formProps, (value, key) => {
            formData.append(key, value);
        });
        formData.delete('shipment_code');
        formData.append('bill_of_lading_codes', this.state.models);
        formData.append('is_new_shipment', !this.state.allowSelect);
        formData.append('shipment_code', this.state.shipmentCode);
        return ApiService.post(ShipmentConstant.resourcePath(), formData)
        .then(({data}) => {
            data.data.map(item => {
                this.props.setListState(({models}) => {
                    return {models: models.map(m => m.id === item.id ? item : m)};
                });
            });
            if (this.props.onImportSuccess) {
                this.props.onImportSuccess(true);
            }
            toastr.success(data.message);
            this.props.actions.closeMainModal();
        })
    }

    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;
        const newRow = '</div><div className="row">'
        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <fieldset className="fiedset-verify-customer-order bgc-grey">
                    <legend  className="legend-account-deposited">Danh sách mã vận đơn</legend>
                    <div className="row">
                        {this.state.models.map((model,index) => {

                            return (
                            <div key ={index} className="col-sm-3">
                                {index + 1}.{model}
                            </div>)
                                {index > 0 && index%4==0 ? newRow :""}
                            })
                        }
                    </div>
                </fieldset>


                <div className="row">
                    <div className="col-sm-2">
                        <label><input type="checkbox"
                            onChange = {e => {
                                if(e.target.checked){
                                    this.setState({
                                        allowSelect : false,
                                    }) 
                                } else {
                                    this.setState({
                                        allowSelect : true,
                                    }) 
                                }
                            }} 
                            value=""/>Tạo lô hàng mới</label> 
                    </div>
                    
                        <div className="col-sm-3">
                            <Field
                                name="shipment_code"
                                component={Select2Input}
                                disabled = {!this.state.allowSelect}
                                select2Options={{
                                    placeholder: 'Chọn lô hàng',
                                    allowClear: true,
                                    ajax: {
                                        url: AppConfig.API_URL + ShipmentConstant.resourcePath("list?status="+ShipmentConstant.STATUS_NEW_SHIPMENT),
                                        delay: 250
                                    }
                                }}
                                select2OnSelect={(e) => {
                                    this.setState({
                                        shipmentSelected: e.params.data,
                                        shipmentCode : e.params.data.shipment_code
                                    });
                                    this.props.initialize({
                                        shipment_code: e.params.data.id
                                    });
                                }}
                                label="Lô hàng"
                                required={true} 
                                Validatorte={[Validator.required]}
                            />
                        </div>
                        {this.state.allowSelect && 
                        <div className="col-sm-7">
                            
                            {this.state.shipmentSelected && 
                                <div>
                                    Thông tin lô hàng :
                                    <div className="row"> 
                                        <div className="col-sm-4"> 
                                            <b>Mã lô hàng :</b> {this.state.shipmentSelected.shipment_code}
                                            <br/><b>Khối lượng :</b>  {this.state.shipmentSelected.weight && this.state.shipmentSelected.weight}
                                            <br/><b>Nơi nhận :</b>  {this.state.shipmentSelected.warehouse_id && this.state.shipmentSelected.warehouse.name}
                                        </div>
                                        <div className="col-sm-8">
                                            <b>Số kiện hàng đã nhập :</b> {this.state.shipmentSelected.shipment_item && this.state.shipmentSelected.shipment_item.length} &nbsp;
                                      
                                            ( {this.state.shipmentSelected.shipment_item && this.state.shipmentSelected.shipment_item.map(model => <i key ={model.id}>{model.bill_of_lading_code}&nbsp;</i>)} )

                                            <br/><b>Hình thức vận chuyển :</b> {this.state.shipmentSelected.transport_type && this.state.shipmentSelected.transport_type_name}
                                            <br/><b>Người tạo :</b> {this.state.shipmentSelected.user_creator && this.state.shipmentSelected.user_creator.name}
                                        </div>
                                    </div>
                                </div>}

                        </div> }


                </div>
                {!this.state.allowSelect &&
                <div className={"row"}>
                    <div className="col-sm-3">
                        <Field
                            name="warehouse_id"
                            component={Select2Input}
                            select2Data={model && model.warehouse ? [{
                                id: model.warehouse.id,
                                text: model.warehouse.name
                            }] : []}
                            select2Options={{
                                placeholder: 'Chọn kho nhận',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + WarehouseConstant.resourcePath("list?type=" + WarehouseConstant.TYPE_VN),
                                    delay: 250
                                }
                            }}
                            label="Kho nhận"
                            required={true}
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="transport_type"
                            component={SelectInput}
                            label="Loại hình vận chuyển"
                            required={true}
                            validate={[Validator.required]}>
                            <option value="" key={0}>-- Chọn loại hình vận chuyển --</option>
                            {Constant.TRANSPORT_TYPES.map(stt =>
                                <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                        </Field>
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="real_weight"
                            component={TextInput}
                            label="Khối lượng (kg)"
                            required={true}
                            validate={[Validator.requireFloat, Validator.greaterThan0, Validator.required]}
                        />
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="volume"
                            component={TextInput}
                            label="Thể tích"
                            required={true}
                            validate={[Validator.required]}
                        />
                    </div>
                </div>
                }
                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                        <i className="fa fa-fw fa-check"/>
                         Thêm
                    </button>
                </div>
            </form>
        );
    }
}

/*Form.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setListState: PropTypes.func,
};*/


function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'CreateShipmentForm'
})(CreateShipmentForm))
