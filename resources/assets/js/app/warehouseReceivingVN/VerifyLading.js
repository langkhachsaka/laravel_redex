import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import PropTypes from 'prop-types'
import {bindActionCreators} from "redux";
import {Field, FieldArray, reduxForm} from 'redux-form';
import Validator from "../../helpers/Validator";
import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Constant from './meta/constant';
import Card from "../../theme/components/Card";
import Layout from "../../theme/components/Layout";
import ConfirmSaveLadingItemForm from './components/ConfirmSaveLadingItemForm'
import SelectInput from "../../theme/components/SelectInput";
import Converter from "../../helpers/Converter";
import {Redirect} from "react-router-dom";
import {toastr} from 'react-redux-toastr';
const TextInput = ({input, label,unit, type, disabled, placeholder, required, meta: {touched, error, invalid}}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        {!!label && <h5>{label} {input && required ? (<span className="text-danger">*</span>) : ''}</h5>}
        <div className="controls">
            <div className={"input-verify-lading"}><input {...input} type={type} disabled={disabled} style={{display :'inline'}} placeholder={placeholder} className="form-control"/></div>
            <div style={{display :"inline"}}>{' '}{unit}</div>
            {touched && error && <div className="help-block">{error}</div>}
        </div>
    </div>

);
const formName = 'ShipmentSplitForm';
class ItemForm extends Component {

    constructor(props) {
        super(props);

        const {index,fields} = this.props;
        this.state = {
            weight : fields.get(index).weight || 0,
            height : fields.get(index).height || 0,
            length : fields.get(index).length || 0,
            width :  fields.get(index).width || 0,
            isSubmited: this.props.isSubmited,
            factorConversion: this.props.factorConversion,
        };
    }

    calcWeightConversion(){
        if(this.state.weight != 0 && this.state.height != 0 && this.state.width != 0 && this.state.length != 0 ){
            let weightConversionCalc = (this.state.width*this.state.height*this.state.length) /this.state.factorConversion;
            return weightConversionCalc > this.state.weight ? weightConversionCalc : this.state.weight;
        } else {
            return 0;
        }
    }


    render() {
        const {item, index,fields} = this.props;
        return (
            <fieldset className="fiedset-verify-customer-order bgc-grey">
                <legend className="legend-account-deposited"></legend>
                <table style={{width: "100%"}}>
                    <tbody>
                    <tr>
                        <td style={{paddingBottom: "25px", width: "7%"}}>
                            Vận đơn :
                        </td>
                        <td style={{width: "100%"}} colSpan="5">
                            <Field
                                name={`${item}.lading_code`}
                                placeholder={"Mã vận đơn"}
                                component={TextInput}
                                disabled={this.state.isSubmited}
                                validate={[Validator.required]}
                            />
                        </td>
                        <td>
                            <button type="button" disabled={fields.length < 2 || this.state.isSubmited}
                                    className="btn btn-sm btn-danger square" title="Remove Lading"
                                    onClick={() => {
                                        fields.remove(index);
                                        if(this.props.onRemoveItem){
                                            this.props.onRemoveItem(index);
                                        }
                                    }}><i className="ft-trash-2"/></button>
                        </td>
                    </tr>

                    {fields.get(index) &&fields.get(index).id &&  fields.get(index).have_sub_lading_code.length > 0 &&
                    <tr>
                        <td className={"red-color"}colSpan="6">
                            Đã tách thành {fields.get(index).have_sub_lading_code.length} mã vận đơn phụ :
                            {fields.get(index).have_sub_lading_code.map(item =>(<b key={item.id}>{item.sub_lading_code + ' '}</b>))}
                        </td>
                    </tr>}

                    <tr>
                        <td style={{paddingBottom: "25px"}}>
                            Khối lượng :
                        </td>
                        <td>
                            <Field
                                name={`${item}.weight`}
                                component={TextInput}
                                unit={"kg"}
                                disabled={this.state.isSubmited}
                                onChange={ e =>{
                                    this.setState({
                                        weight: Converter.str2float(e.target.value || 0)
                                    });
                                }}
                                validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                            />

                        </td>
                        <td style={{paddingBottom: "25px"}}>
                            Kích thước :
                        </td>
                        <td>
                            <Field
                                name={`${item}.length`}
                                component={TextInput}
                                unit="cm"
                                placeholder={"Dài"}
                                onChange={ e =>{
                                    this.setState({
                                        length: Converter.str2float(e.target.value || 0)
                                    });
                                }}
                                disabled={this.state.isSubmited}
                                validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                            />
                        </td>
                        <td>
                            <Field
                                name={`${item}.width`}
                                component={TextInput}
                                unit="cm"
                                placeholder={"Rộng"}
                                disabled={this.state.isSubmited}
                                onChange={ e =>{
                                    this.setState({
                                        width : Converter.str2float(e.target.value || 0)
                                    });
                                }}
                                validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                            />
                        </td>
                        <td>
                            <Field
                                name={`${item}.height`}
                                component={TextInput}
                                unit="cm"
                                placeholder={"Cao"}
                                onChange={ e =>{
                                    this.setState({
                                        height: Converter.str2float(e.target.value || 0)
                                    });
                                }}
                                disabled={this.state.isSubmited}
                                validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                            />
                        </td>
                    </tr>
                    <tr>
                        <td style={{paddingBottom: "25px"}}>KL quy đổi :</td>
                        <td>
                            <div className={"input-verify-lading"}>
                                <input style={{marginBottom:'20px'}} readOnly={true} className="form-control" value={this.calcWeightConversion()}/>
                            </div>
                        </td>
                        <td style={{paddingBottom: "25px"}}>
                            Đóng gói :
                        </td>
                        <td>
                            <Field
                                disabled={this.state.isSubmited}
                                name={`${item}.pack`}
                                component={SelectInput}
                            >
                                <option value="0" key={0}>-----</option>
                                <option value="1" key={1}>Đóng gõ</option>
                                <option value="2" key={2}>Nẹp bìa</option>
                            </Field>
                        </td>
                        <td style={{paddingBottom: "25px", textAlign: "right"}}>
                            Phụ phí :
                        </td>
                        <td>
                            <Field
                                disabled={this.state.isSubmited}
                                name={`${item}.other_fee`}
                                component={TextInput}
                                unit="RMB"
                            />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        );
    }
}


ItemForm.propTypes = {
    onRemoveItem: PropTypes.func,
};
ItemForm = connect(null, dispatch => {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch),
    }
})(ItemForm);



let ItemsForm = (props) => {
    const {fields, meta: {error},isSubmited,factorConversion} = props;
    return (
        <div>
            {fields.map((item, index) => {
                return (
                    <div key={index}>
                        {!fields.get(index).sub_lading_code &&
                        <ItemForm
                            isSubmited={isSubmited}
                            factorConversion={factorConversion}
                            key={index}
                            item={item}
                            index={index}
                            fields={fields}
                            onRemoveItem={props.onRemoveItem}
                        />
                        }
                    </div>
                )
            })}

            {!isSubmited &&
            <div style={{display: 'inline', float: 'right'}}>
                <select style={{marginRight: "10px"}} onChange={e => {
                    newItem = e.target.value;
                }}>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
                <button type="button" onClick={() => {
                    let n = newItem;

                    for (let i = 0; i < n; i++) {
                        fields.push({});
                    }
                    // let count = this.state.ladingInput;
                    if(props.onAddItem){
                        props.onAddItem();
                    }
                    newItem = 1;
                }}
                        className="btn btn-sm btn-info">
                    <i className="fa fa-fw fa-check"/>
                    Thêm kiện hàng
                </button>
            </div>
            }
        </div>
    )
}

let newItem = 1;

class VerifyLading extends Component {

    constructor(props) {
        super(props);
        this.state = {
            model: null,
            isRecheck : false,
            factorConversion : '',
            isSaveTemp : false,
            isSubmit : false,
            redirectToList : false,
            isSubmited : false,
            ladingInput: 2,
            unMatchItem: [],
            missingMatchItem: [],
            meta: {pageCount: 0, currentPage: 1},
            isLoading: true
        };
    }

    componentDidMount() {
        this.setState({isLoading: true});
        ApiService.get(Constant.resourcePath(this.getModelId())).then(({data: {data}}) => {
            this.setState({
                model: data.data,
                factorConversion  : data.factor_conversion,
                isLoading: false,
                ladingInput: data.data.warehouse_vn_lading_items.length > 0 ? data.data.warehouse_vn_lading_items.length  :  data.data.shipment.shipment_item.length,
            });

            data.data.warehouse_vn_lading_items.length > 0 && data.data.warehouse_vn_lading_items[0].status != 1 &&
            this.setState({
                isSubmited: true,
            });

            if(data.data.warehouse_vn_lading_items.length == 0) {
                let items = [];
                for (let i = 0; i < data.data.shipment.shipment_item.length; i++) {
                    items.push({});
                }

                this.props.initialize({
                    ladings: items,
                });
            } else {
                let items = [];
                data.data.warehouse_vn_lading_items.map(item => {
                    items.push(item);
                })
                this.props.initialize({
                    ladings: items,
                });

                let ladingCodes = [];
                data.data.shipment.shipment_item.map(item =>{
                    ladingCodes.push(item.bill_of_lading_code);
                });
                let ladingCodeInputs = [];
                data.data.warehouse_vn_lading_items.map(item =>{
                    !item.sub_lading_code && ladingCodeInputs.push(item.lading_code);
                });
                this.checkMapping(ladingCodes,ladingCodeInputs);
            }

        });




        this.props.actions.changeThemeTitle("Thêm kiện hàng");
    }

    componentWillUnmount() {
        this.props.actions.clearState();
    }

    fetchData(id) {
        this.setState({isLoading: true});
        return ApiService.get(Constant.resourcePath(id)).then(({data: {data}}) => {
            this.setState({
                model: data,
                isLoading: false,
            });
        });
    }

    handleSubmit(formProps) {
        let ladingCodes = [];
        this.state.model.shipment.shipment_item.map(item =>{
            ladingCodes.push(item.bill_of_lading_code);
        });
        let ladingCodeInputs = [];
        formProps.ladings.map(item =>{
            ladingCodeInputs.push(item.lading_code);
        });
        let isError = this.checkMapping(ladingCodes,ladingCodeInputs);

        // Stop if click button "Kiểm tra lại"
        if(this.state.isRecheck || (isError && !this.state.isSaveTemp)){
            return;
        }

        if(this.state.isSaveTemp){
            this.setState({
                isLoading : true,
            });
            ApiService.post(Constant.resourcePath('saveTemp/'+this.getModelId()),formProps) .then(({data}) => {
                toastr.success(data.message);
                this.setState({
                    redirectToList : true,
                    isLoading: false,
                })
            });
        } else {
            this.setState({
                isLoading : true,
            });
            ApiService.post(Constant.resourcePath('saveTemp/'+this.getModelId()),formProps) .then(({data}) => {
                this.setState({
                    isLoading: false,
                })
                this.props.actions.openMainModal(<ConfirmSaveLadingItemForm model={data.data}
                />, "DANH SÁCH CHI TIẾT VẬN ĐƠN TRONG LÔ HÀNG: "+data.data.shipment_code);
            });

        }
    }

    // Check mapping with real check and data China side
    checkMapping(ladingCodes,ladingCodeInputs){
        this.setState({
            unMatchItem: [],
            missingMatchItem: [],
        });
        let isError = false;
        let unMatchItems = [];
        let missingMatchItem = [];
        ladingCodeInputs.map(item => {

            if(!ladingCodes.includes(item)){
                isError = true;
                unMatchItems.push(item);

            }

        });
        ladingCodes.map(item => {
            if(!ladingCodeInputs.includes(item)){
                isError = true;
                missingMatchItem.push(item);
            }
        });
        this.setState({
            missingMatchItem: missingMatchItem,
        });
        this.setState({
            unMatchItem: unMatchItems,
        });
        return isError;
    }

    getModelId() {
        return this.props.match.params.id;
    }

    render() {
        const {userPermissions} = this.props;
        const {handleSubmit, submitting, reset} = this.props;
        const defaultPageSize = this.props.search.meta.per_page;

        if(this.state.redirectToList){
            return <Redirect to={"/warehouse-receiving-vn/"}/>;
        }
        return (
            <Layout>
                <Card isLoading={this.state.isLoading}>

                    <fieldset className="fiedset-verify-customer-order bgc-grey">
                        <legend  className="legend-account-deposited">Thông tin lô hàng</legend>
                        <div className={"row"}>
                            <div className={"col-sm-3"}>
                                Mã lô hàng <input  type="text" style={{display :'inline', width : '150px',marginLeft:'20px'}}
                                                   disabled="disabled"
                                                   value={this.state.model ? this.state.model.shipment_code : ' '} className="form-control"/>
                            </div>
                            <div className={"col-sm-3"}>
                                Khối lượng <input  type="text" style={{display :'inline', width : '150px',marginLeft:'20px'}}
                                                   disabled="disabled"
                                                   value={this.state.model ? this.state.model.weight : ' '} className="form-control"/>
                            </div>
                            <div className={"col-sm-4"}>
                                Kích thước <input  type="text" style={{display :'inline', width : '60px',marginLeft:'20px'}}
                                                   disabled="disabled"
                                                   value={this.state.model ? this.state.model.length : ' '} className="form-control"/>
                                <input  type="text" style={{display :'inline', width : '60px',marginLeft:'20px'}}
                                        disabled="disabled"
                                        value={this.state.model ? this.state.model.width : ' '} className="form-control"/>
                                <input  type="text" style={{display :'inline', width : '60px',marginLeft:'20px'}}
                                        disabled="disabled"
                                        value={this.state.model ? this.state.model.height : ' '} className="form-control"/>
                            </div>
                            <div className={"col-sm-2"}>
                                Đóng gói <input  type="text" style={{display :'inline', width : '140px',marginLeft:'20px'}}
                                                 disabled="disabled"
                                                 value={this.state.model ? this.state.model.pack_name  : ' '} className="form-control"/>
                            </div>
                        </div>


                    </fieldset>
                    <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                        <fieldset className="fiedset-verify-customer-order">
                            <legend  className="legend-account-deposited">Nhập thông tin kiện hàng</legend>

                            <FieldArray name="ladings" isSubmited={this.state.isSubmited} factorConversion={this.state.factorConversion}
                                        onRemoveItem ={(data) =>{
                                            let count = this.state.ladingInput;
                                            this.setState({
                                                ladingInput: this.state.ladingInput - 1,
                                            });
                                        }}
                                        onAddItem ={(data) =>{
                                            let count = this.state.ladingInput;
                                            this.setState({
                                                ladingInput: this.state.ladingInput + 1,
                                            });
                                        }}
                                        component={ItemsForm} />

                        </fieldset>
                        {!this.state.isSubmited &&
                        <div style={{display: 'inline', float: 'right'}}>
                            {(this.state.unMatchItem.length > 0 || this.state.missingMatchItem.length > 0 ) &&
                            <button type="submit" name="submit" onClick={() => {
                                this.setState({
                                    isRecheck: true,
                                })
                            }}
                                    value="submit" className="btn btn-lg btn-warning">
                                <i className="fa fa-fw fa-check"/>
                                Kiểm tra lại
                            </button>}{' '}
                            {( this.state.unMatchItem.length > 0 || this.state.missingMatchItem.length > 0) &&
                            <button type="submit" name="submit" onClick={() => {
                                this.setState({
                                    isRecheck: false,
                                    isSaveTemp: true,
                                    isSubmit: false,
                                })
                            }}
                                    value="submit" className="btn btn-lg btn-info">
                                <i className="fa fa-fw fa-check"/>
                                Lưu tạm
                            </button>}
                            {this.state.unMatchItem.length == 0 && this.state.missingMatchItem.length == 0 &&
                            <button type="submit"
                                    onClick={() => {
                                        this.setState({
                                            isRecheck: false,
                                            isSaveTemp: false,
                                            isSubmit: true,
                                        })
                                    }}
                                    className="btn btn-lg btn-primary">
                                <i className="fa fa-fw fa-check"/>
                                Nhập kho
                            </button>}
                        </div>
                        }
                        {this.state.unMatchItem.length > 0  &&
                        <div>
                            <h3  style={{ color:"red"}}>Mã vận đơn :<b>{this.state.unMatchItem.toString()}</b> không có bên lô Xuất, yêu cầu kiểm tra lại thông tin</h3>
                        </div>
                        }
                        {this.state.missingMatchItem.length > 0  &&
                        <div>
                            <h3  style={{ color:"red"}}>Mã vận đơn :<b>{this.state.missingMatchItem.toString()}</b> có bên lô Xuất, không có ở lô Nhập, yêu cầu kiểm tra lại thông tin</h3>
                        </div>
                        }
                        <h3>Tổng mã vận đơn lô Nhập/Xuất : {this.state.isSubmited ? this.state.model && this.state.model.shipment&&this.state.model.shipment.shipment_item && this.state.model.shipment.shipment_item.length :  this.state.ladingInput}/
                            {this.state.model && this.state.model.shipment&&this.state.model.shipment.shipment_item ? this.state.model.shipment.shipment_item.length  : '0'}</h3>
                    </form>


                </Card>

            </Layout>
        );
    }
}
VerifyLading.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired
};
function mapStateToProps(state) {
    return {
        search: state.warehouseReceivingCN.search,
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps) (reduxForm({
    form: formName,
})(VerifyLading))
