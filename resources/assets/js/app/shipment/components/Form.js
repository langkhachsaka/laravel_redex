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
import WarehouseConstant from "../../warehouse/meta/constant";
import AppConfig from "../../../config";
import Formatter from "../../../helpers/Formatter";
import DatePickerInput from "../../../theme/components/DatePickerInput";

class Form extends Component {

    constructor(props) {
        super(props);

        this.state = {
            newModel : [],
            model :props.model,
            oldModel :props.model && props.model.shipment_item,
            warehouseCN : null,
            canNotSubmit : true,
            newShipmentCode : null,
            billOfLadingCodes : [],
        };
    }

    componentDidMount() {
        const {model} = this.props;
        if (model) {
            this.props.initialize(model);
        } else {
            ApiService.get(Constant.resourcePath("getNewShipmentCode")).then(({data : {data}}) => {
                this.props.change("shipment_code", data.newShipmentCode);
                this.props.change("conversion_factor", data.rate);
                this.setState({
                    newShipmentCode : data.newShipmentCode
                });

            })
        }
    }

    handleSubmit(formProps) {
        const {model} = this.props;

        if(this.state.newModel.length == 0 && (!this.state.oldModel || this.state.oldModel.length == 0)) {
            toastr.warning('Vui lòng thêm kiện hàng vào lô hàng');
            return;
        }
        if (model) {

            const formData = new FormData();

            _.forOwn(formProps, (value, key) => {
                formData.append(key, value);
            });
            formData.append('bill_of_lading_codes', this.state.billOfLadingCodes);
            formData.delete('note');
            formData.delete('conversion_factor');
            formData.delete('receive_date');

            return ApiService.post(Constant.resourcePath(model.id), formData)
                .then(({data}) => {
                    this.props.setListState(({models}) => {
                        return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                    });
                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        } else {
            const formData = new FormData();

            _.forOwn(formProps, (value, key) => {
                formData.append(key, value);
            });
            formData.append('bill_of_lading_codes', this.state.billOfLadingCodes);
            formData.delete('note');
            return ApiService.post(Constant.resourcePath("create"), formData)
                .then(({data}) => {
                    this.props.setListState(({models}) => {
                        models.unshift(data.data);
                        return {models: models};
                    });
                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        }
    }

    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;
        var disableStatus = false;
        if(!model) {
            disableStatus = true;

        } else if(Constant.STATUS_DISABLE.indexOf(model.status)!= -1) {
            disableStatus = true;
        }

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <div className="row">
                    <div className="col-sm-3">
                        <Field
                            name="shipment_code"
                            component={TextInput}
                            label="Mã lô hàng"
                            disabled = {true}
                            required={true}
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="warehouse_id"
                            component={Select2Input}
                            select2Data={model && model.warehouse ? [{
                                id: model.warehouse.id,
                                text: model.warehouse.name
                            }] :[]}
                            select2Options={{
                                placeholder: 'Chọn kho nhận',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + WarehouseConstant.resourcePath("list?type="+WarehouseConstant.TYPE_VN),
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
                            name="status"
                            component={SelectInput}
                            label="Trạng thái"
                            required={model ? "" : true}
                            disabled = {disableStatus}
                            validate={model ? [Validator.required] : []}>
                            {model ? <option value="" key={0}>-- Chọn trạng thái --</option> : <option value="1" key={1} >Lô hàng mới</option>}
                            {Constant.STATUSES.map(stt =>
                                <option value={stt.id}  hidden={stt.hidden} key={stt.id}>{stt.text}</option>)}
                        </Field>
                    </div>
                    <div className="col-sm-3">
                        <Field
                        name="transport_date"
                        component={DatePickerInput}
                        onDateChange={(date) => {
                            this.props.change("transport_date", date.format("YYYY-MM-DD"));
                        }}
                        label="Ngày phát"
                        required={true}
                        validate={[Validator.required]}
                        />
                    </div>
                </div>
                <div className = "row">

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
                            validate={[Validator.requireFloat, Validator.greaterThan0,Validator.required]}
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
                   {/* <div className="col-sm-3">
                        <Field
                            name="conversion_factor"
                            component={TextInput}
                            label="Hệ số quy đổi"
                            required={true}
                            validate={[Validator.requireFloat,Validator.required]}
                        />
                    </div>*/}
                </div>

                <table className="table table-hover">
                    <thead>
                    <tr>
                        <th style={{width: '430px'}}>Mã vận đơn</th>
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
                    {(!this.state.model || this.state.model && this.state.model.status == 1 || this.state.model && this.state.model.status == 4 || this.state.model && this.state.model.status == 2)&&
                    <tr>
                        <td>
                            <div className="row">
                                <div className = "col-sm-12">
                                    <Field
                                        name='bill_of_lading_code'
                                        component={Select2Input}
                                        select2Data={[]}
                                        select2Options={{
                                            ajax: {
                                                url: AppConfig.API_URL + Constant.resourcePath("listBillOfLading"),
                                                delay: 250
                                            }
                                        }}
                                        select2OnSelect={(e) => {
                                            this.setState({
                                                warehouseCN: e.params.data,
                                                canNotSubmit : false,
                                            });
                                        }}
                                        placeholder="Mã vận đơn"
                                    />
                                </div>
                            </div>
                        </td>
                        <td>{this.state.warehouseCN && this.state.warehouseCN.ladingcodetable_type}</td>
                        <td>{this.state.warehouseCN && this.state.warehouseCN.ladingcodetable_id}</td>
                        <td>{this.state.warehouseCN && this.state.warehouseCN.weight + ' (kg)' } </td>
                        <td>{this.state.warehouseCN && this.state.warehouseCN.height ?  this.state.warehouseCN.height + '(cm)' : ''} </td>
                        <td>{this.state.warehouseCN && this.state.warehouseCN.width ?  this.state.warehouseCN.width  + '(cm)' : ''}</td>
                        <td>{this.state.warehouseCN && this.state.warehouseCN.length ? this.state.warehouseCN.length + '(cm)' : ''}</td>
                        <td>
                            <button type="button" disabled={this.state.canNotSubmit} className="btn btn-primary btn-sm btn-block" onClick={() => {
                                if (!this.state.warehouseCN) {
                                    toastr.warning('Vui lòng chọn mã vận đơn');
                                    return;
                                }

                                let newModel = this.state.newModel;
                                let billOfLadings = this.state.billOfLadingCodes;

                                if(billOfLadings.indexOf(Constant.NEW_BILL_OF_LADING_CODE_PREFIX+this.state.warehouseCN.bill_of_lading_code) != -1) {
                                    toastr.warning("Vận đơn này đã tồn tại trong lô hàng mới này");
                                } else {
                                    newModel.push(this.state.warehouseCN);
                                    billOfLadings.push(Constant.NEW_BILL_OF_LADING_CODE_PREFIX+this.state.warehouseCN.bill_of_lading_code);
                                }
                                this.setState({
                                    billOfLadingCodes : billOfLadings,
                                    newModel : newModel,
                                    warehouseCN : null,
                                    message : 'Vui lòng nhập mã vận đơn',
                                    canNotSubmit : true,
                                });
                                $("#bill_of_lading_code").val("");
                            }}>
                                <i className="ft-plus"/>{' '} Thêm vận đơn
                            </button>
                        </td>
                    </tr>
                    }
                    { this.state.newModel&&
                    this.state.newModel.map(item => {
                        return (
                            <tr key={item.id}>
                                <td>{item.bill_of_lading_code}</td>
                                <td>{item.ladingcodetable_type}</td>
                                <td>{item.ladingcodetable_id}</td>
                                <td>{item.weight} (kg)</td>
                                <td>{item.height ? item.height : 0} (cm)</td>
                                <td>{item.width ? item.width : 0} (cm)</td>
                                <td>{item.length ? item.length : 0} (cm)</td>
                                <td className="column-actions">
                                    <button key="btn-delete" type="button"  className="btn btn-sm btn-danger square" onClick={() => {
                                        swal({
                                            title: "Xoá vận đơn",
                                            text: "Bạn có chắc chắn muốn xoá vận đơn này?",
                                            icon: "warning",
                                            buttons: true,
                                            dangerMode: true,
                                        })
                                            .then((willDelete) => {
                                                if (willDelete) {
                                                    let newModel = this.state.newModel;
                                                    let oldModel = this.state.oldModel;
                                                    let billOfLadings = this.state.billOfLadingCodes;
                                                    if(billOfLadings.indexOf(Constant.NEW_BILL_OF_LADING_CODE_PREFIX+item.bill_of_lading_code) != -1) {
                                                        var index = billOfLadings.indexOf(Constant.NEW_BILL_OF_LADING_CODE_PREFIX+item.bill_of_lading_code);
                                                        billOfLadings.splice(index,1);
                                                        newModel = newModel.filter(m => m.id !== item.id);
                                                    }
                                                    console.log(billOfLadings);
                                                    this.setState({
                                                        billOfLadingCodes : billOfLadings,
                                                        newModel : newModel,
                                                        oldModel : oldModel,
                                                        message : 'Vui lòng nhập mã vận đơn',
                                                        canNotSubmit : true,
                                                    });
                                                }
                                            });
                                    }}><i className="ft-trash-2"/></button>
                                </td>
                            </tr>

                        );
                    })
                    }
                    {this.state.newModel.length > 0 && ! this.state.oldModel &&  <tr>
                        <td><b>Tổng</b></td>
                        <td></td>
                        <td></td>
                        <td>
                            Khối lượng: <b>{Formatter.number(this.state.newModel
                            .map(item => item.weight)
                            .reduce((a, b) => a + b, 0))} kg</b>
                        </td>
                    </tr>}
                    {this.state.oldModel &&
                        this.state.oldModel.map(item => {
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
                                    <td className="column-actions">
                                        <button key="btn-delete" type="button"  className="btn btn-sm btn-danger square" onClick={() => {
                                            swal({
                                                title: "Xoá vận đơn",
                                                text: "Bạn có chắc chắn muốn xoá vận đơn này?",
                                                icon: "warning",
                                                buttons: true,
                                                dangerMode: true,
                                            })
                                                .then((willDelete) => {
                                                    if (willDelete) {
                                                        let newModel = this.state.newModel;
                                                        let oldModel = this.state.oldModel;
                                                        let billOfLadings = this.state.billOfLadingCodes;

                                                        oldModel = oldModel.filter(m => m.id !== item.id);
                                                        billOfLadings.push(Constant.DELETE_BILL_OF_LADING_CODE_PREFIX+ item.bill_of_lading_code);
                                                        this.setState({
                                                            billOfLadingCodes : billOfLadings,
                                                            newModel : newModel,
                                                            oldModel : oldModel,
                                                            message : 'Vui lòng nhập mã vận đơn',
                                                            canNotSubmit : true,
                                                        });
                                                    }
                                                });
                                        }}><i className="ft-trash-2"/></button>
                                    </td>
                                </tr>
                            );
                        })
                    }

                    {this.state.model &&<tr>
                        <td><b>Tổng</b></td>
                        <td></td>
                        <td></td>
                        <td>
                            Khối lượng: <b>{Formatter.number(this.state.model.shipment_item
                            .map(item => item.bill_of_lading.weight)
                            .reduce((a, b) => a + b, 0)) +
                            (this.state.newModel ? Formatter.number(this.state.newModel
                            .map(item => item.weight)
                            .reduce((a, b) => a + b, 0)) : 0)

                    } kg</b>
                        </td>
                    </tr>}
                    </tbody>

                </table>

                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
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
    form: 'WarehouseReceivingCNForm'
})(Form))
