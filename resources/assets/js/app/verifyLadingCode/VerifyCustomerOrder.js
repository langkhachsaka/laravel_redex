import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import Dropzone from 'react-dropzone'
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import Constant from './meta/constant';
import Validator from "../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import moment from "moment";
import Layout from "../../theme/components/Layout";
import Card from "../../theme/components/Card";
import TextArea from "../../theme/components/TextArea";
import ImageConstant from "../image/meta/constant";
import UrlHelper from "../../helpers/Url";
import {Redirect} from "react-router-dom";

const maxImageUpload = value =>
    value && value.length > Constant.ORDER_ITEM_MAX_IMAGES ?
        "Chỉ được phép chọn tối đa " + Constant.ORDER_ITEM_MAX_IMAGES + " ảnh" : undefined;

const CheckboxInput = ({input, label, checked}) => (
    <div>
        {label} : <input {...input} type="checkbox" checked={checked} className="checkbox-item-verify"/>
    </div>
);

const TextInput = ({input, label, type, disabled, placeholder, required, meta: {touched, error, invalid}}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        {label}{input && required ? (<span className="text-danger">*</span>) : ''}
        : <input {...input}  type="text" disabled={disabled} style={{display :'inline', width : '100px',marginBottom:'20px'}}  className="form-control"/>
        {touched && error && <div className="help-block">{error}</div>}
    </div>

);


class ShowImages extends Component {

    constructor(props) {
        super(props);

        this.state = {
            img: props.images[0],
        };
    }

    render() {
        const {images} = this.props;

        return (
            <div>
                <div className="image-preview-verify">
                    <img className="img-fluid" src={UrlHelper.imageUrl(this.state.img.path)} alt=""/>
                </div>
                <div className="images-thumb-verify">
                    {images.map(img =>
                        <div key={img.id}
                             className={"img-thumb-verify" + (this.state.img.id === img.id ? " active" : "")}>
                            <a onClick={e => {
                                e.preventDefault();
                                this.setState({img: img});
                            }}>
                                <img className="img-fluid" src={UrlHelper.imageUrl(img.path)} alt=""/>
                            </a>
                        </div>
                    )}
                </div>
            </div>
        );
    }
}

class VerifyCustomerOrder extends Component {

    constructor(props) {
        super(props);
        this.state = {
            redirectToListVerifyLadingCode : false,
            models : [],
            isLoading : true,
            ladingCode :"",
            length : "",
            height :"",
            width : "",
            weight :"",
            hasProblems : [],
        };
    }
    componentDidMount() {
        this.fetchData(this.getModelId());
        this.props.actions.changeThemeTitle("Kiểm tra kiện hàng của đơn hàng Việt Nam : 1 Mã vận đơn - 1 Đơn hàng");
    }

    fetchData(id) {
        return ApiService.get(Constant.resourcePath('getCustomerOrder/'+id)).then(({data: {data}}) => {
            this.setState({
                models: data,
                isLoading: false,
                ladingCode :data[0].code,
                length : data[0].warehouse_vn_lading_item.length,
                height :data[0].warehouse_vn_lading_item.height,
                width : data[0].warehouse_vn_lading_item.width,
                weight :data[0].warehouse_vn_lading_item.weight,
            });
            this.props.change("lading_code",data[0].code)
            this.props.change('customer_order_id',data[0].bill_code.customer_order_id);
        });
    }
    getModelId() {
        return this.props.match.params.id;
    }
    renderImageInputs = ({fields, meta: {error}}) => (
        <div className={`form-group ${error ? 'error' : ''}`}>
            <div className="images-input-preview">
                {fields.map((image, index) =>
                    <div key={index} className="img-preview">
                        <a onClick={(e) => {
                            e.preventDefault();
                            fields.remove(index);
                            // TODO optimize
                            //ApiService.delete(ImageConstant.resourcePath('delete'), {image: fields.get(index)});
                        }}><i className="ft-x"/></a>
                        <img src={UrlHelper.imageUrl(fields.get(index))} alt=""/>
                        <Field name={image} type="hidden" component="input"/>
                    </div>)}
                {fields.length < Constant.ORDER_ITEM_MAX_IMAGES && <Dropzone
                    style={{}}
                    className="drop-zone"
                    accept="image/*"
                    onDrop={(acceptedFiles) => {
                        let formData = new FormData();
                        acceptedFiles.forEach((acceptedFile) => {
                            formData.append('images[]', acceptedFile);
                        });

                        ApiService.post(ImageConstant.resourcePath(), formData)
                            .then(({data}) => {
                                data.data.forEach((item) => fields.push(item));
                            });
                    }}
                >
                    <div className="drop-zone-text">Thêm ảnh</div>
                </Dropzone>}
            </div>
            {error && <div className="help-block">{error}</div>}
        </div>
    );

    handleSubmit(formProps){
        return ApiService.post(Constant.resourcePath("storeVerifyCustomerOrder"), formProps)
            .then(({data}) => {
                toastr.success(data.message);
                this.setState({
                    redirectToListVerifyLadingCode : true,
                })
            });

    }


    checkProblemsItem(value,index){
        let hasProblems = this.state.hasProblems;
        var itemNew =  {index : index,value : 1};
        var isExits = false;

        hasProblems.forEach((element, index) => {
            if(element.index === itemNew.index) {
                var item = element;
                if(value){
                    item.value = item.value + 1;
                } else {
                    item.value = item.value - 1;
                }
                hasProblems[index] = item;
                isExits = true;
                return;
            }
        });
        if(!isExits){
            hasProblems.push(itemNew);
        }
        this.setState({
            hasProblems: hasProblems,
        });
    }

    render() {

        const {handleSubmit} = this.props;
        let index = 0;
        if(this.state.redirectToListVerifyLadingCode){
            return <Redirect to={"/verify-lading-code"}/>;
        }
        let numberLading = 0;
        this.state.models.map(item=> {
            item.bill_code && item.bill_code.customer_order_items.map(customer_order_item=> {
                numberLading++;
            })});

        return (
            <Layout>
                <Card isLoading={this.state.isLoading}>
                <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                    <div>
                        <fieldset className="fiedset-verify-customer-order bgc-grey">
                            <legend  className="legend-account-deposited"></legend>
                            <div className="row">
                                    <div className="col-sm-2">
                                        <Field
                                            label="Mã vận đơn"
                                            name="lading_code"
                                            component={TextInput}
                                            disabled={true}
                                        />
                                    </div>
                                    <div className="col-sm-2">
                                        Dài: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={this.state.length ? this.state.length  : ''} className="form-control"/>
                                    </div>
                                    <div className="col-sm-2">
                                        Rộng: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={this.state.width ? this.state.width : ''} className="form-control"/>
                                    </div>
                                    <div className="col-sm-2">
                                        Cao: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={this.state.height ? this.state.height : ''} className="form-control"/>
                                    </div>
                                    <div className="col-sm-2">
                                        Nặng: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={this.state.weight ? this.state.weight : ''} className="form-control"/>
                                    </div>
                                    <div className="col-sm-2">
                                        Kiện: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={numberLading} className="form-control"/>
                                    </div>
                                </div>
                        </fieldset>
                        <div hidden={true}>
                            <Field
                                label="Số lượng kiểm"
                                name={'customer_order_id'}
                                component={TextInput}
                            />
                        </div>
                        {this.state.models.map((item,index1)=> {
                            return item.bill_code && item.bill_code.customer_order_items.map((customer_order_item,index2)=> {
                                const imageName = "item["+(index)+"].images";
                                const quantity_verify = "item["+(index)+"].quantity_verify";
                                const note = "item["+(index)+"].note";
                                const is_broken_gash = "item["+(index)+"].is_broken_gash";
                                const is_error_color = "item["+(index)+"].is_error_color";
                                const is_error_product = "item["+(index)+"].is_error_product";
                                const is_error_size = "item["+(index)+"].is_error_size";
                                const is_exuberancy = "item["+(index)+"].is_exuberancy";
                                const is_inadequate = "item["+(index)+"].is_inadequate";
                                const customer_order_item_id = "item["+(index)+"].customer_order_item_id";

                                this.props.change(customer_order_item_id,customer_order_item.id);

                                const rowNum = index;
                                let isRequied = false;
                                var foundIndex = this.state.hasProblems.findIndex(x => x.index == rowNum);
                                this.state.hasProblems[foundIndex] && this.state.hasProblems[foundIndex]['value'] > 0 ? isRequied = true : false;
                                index = index + 1;

                                return (
                                    <fieldset key={index} className="fiedset-verify-customer-order">
                                        <legend  className="legend-account-deposited"></legend>
                                        <div  className="row">
                                            <div className="col-sm-1" style={{paddingLeft: "85px",marginLeft: "-69px"}}>
                                                <fieldset className="fiedset-verify-customer-order h100 bgc-grey">
                                                    <legend  className="legend-account-deposited"></legend>
                                                    <div style={{position: "relative",top: "50%"}}>
                                                        {index}
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div className="col-sm-11">
                                                <div className="row" >
                                                    <div className="col-sm-3">
                                                        { customer_order_item.images &&<div>
                                                            <ShowImages
                                                                images={customer_order_item.images}/>
                                                        </div>
                                                        }
                                                    </div>
                                                    <div className="col-sm-9">
                                                        <div className="row" style={{paddingBottom :"10px",paddingTop: "10px"}}>
                                                            <div className="col-sm-3" style={{
                                                                textAlign: 'center',
                                                                paddingTop: '10px'
                                                            }}>
                                                                <a href={customer_order_item.link} target="_blank">Link gốc</a>
                                                            </div>
                                                            <div className="col-sm-3">
                                                                Đơn vị: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={customer_order_item.unit} className="form-control"/>
                                                            </div>
                                                            <div className="col-sm-3">
                                                                Loại hàng: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={customer_order_item.description} className="form-control"/>
                                                            </div>
                                                            <div className="col-sm-3">
                                                            </div>
                                                        </div>
                                                        Description :
                                                        <input  type="text" disabled style={{display :'inline', width : '600px',marginLeft:'20px'}} value={customer_order_item.description}  className="form-control"/>
                                                        <fieldset key={item.id} className="fiedset-verify-customer-order mgt-10 bgc-grey">
                                                            <legend  className="legend-account-deposited"></legend>
                                                            <div className="row">
                                                                <div className="col-sm-5">
                                                                    <div className="row">
                                                                        <div className="col-sm-6 ta-right">
                                                                            Số lượng đặt :
                                                                        </div>
                                                                        <div className="col-sm-6 col-input-info-verify">
                                                                            <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={customer_order_item.quantity}  className="form-control"/>
                                                                        </div>
                                                                    </div>
                                                                    <div className="row">
                                                                        <div className="col-sm-6 ta-right ">
                                                                            Cỡ:
                                                                        </div>
                                                                        <div className="col-sm-6 col-input-info-verify">
                                                                            <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={customer_order_item.size}  className="form-control"/>
                                                                        </div>
                                                                    </div>
                                                                    <div className="row">
                                                                        <div className="col-sm-6 ta-right">
                                                                            Màu:
                                                                        </div>
                                                                        <div className="col-sm-6 col-input-info-verify">
                                                                            <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={customer_order_item.colour}  className="form-control"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div className="col-sm-7">
                                                                    <Field
                                                                        label="Số lượng kiểm"
                                                                        name={quantity_verify}
                                                                        component={TextInput}
                                                                        required={true}
                                                                        validate={[Validator.required, Validator.greaterThan0]}
                                                                    />
                                                                    <div className="row">
                                                                        <div className="col-sm-3">
                                                                            Sai cỡ
                                                                        </div>
                                                                        <div className="col-sm-2">
                                                                            <Field
                                                                                label=""
                                                                                onChange={e=>{
                                                                                    this.checkProblemsItem(e.target.checked,rowNum);
                                                                                }}
                                                                                name={is_error_size}
                                                                                component={CheckboxInput}
                                                                            />
                                                                        </div>
                                                                        <div className="col-sm-3">
                                                                            Sai màu
                                                                        </div>
                                                                        <div className="col-sm-2">
                                                                            <Field
                                                                                label=""
                                                                                onChange={e=>{
                                                                                    this.checkProblemsItem(e.target.checked,rowNum);
                                                                                }}
                                                                                name={is_error_color}
                                                                                component={CheckboxInput}
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div className="row">
                                                                        <div className="col-sm-3">
                                                                            Sai hàng
                                                                        </div>
                                                                        <div className="col-sm-2">
                                                                            <Field
                                                                                label=""
                                                                                onChange={e=>{
                                                                                    this.checkProblemsItem(e.target.checked,rowNum);
                                                                                }}
                                                                                name={is_error_product}
                                                                                component={CheckboxInput}
                                                                            />
                                                                        </div>
                                                                        <div className="col-sm-3">
                                                                            Bẹp, vỡ, rách
                                                                        </div>
                                                                        <div className="col-sm-2">
                                                                            <Field
                                                                                label=""
                                                                                onChange={e=>{
                                                                                    this.checkProblemsItem(e.target.checked,rowNum);
                                                                                }}
                                                                                name={is_broken_gash}
                                                                                component={CheckboxInput}
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div className="row">
                                                                        <div className="col-sm-3">
                                                                            Thừa sản phẩm
                                                                        </div>
                                                                        <div className="col-sm-2">
                                                                            <Field
                                                                                label=""
                                                                                onChange={e=>{
                                                                                    this.checkProblemsItem(e.target.checked,rowNum);
                                                                                }}
                                                                                name={is_exuberancy}
                                                                                component={CheckboxInput}
                                                                            />
                                                                        </div>
                                                                        <div className="col-sm-3">
                                                                            Thiếu sản phẩm
                                                                        </div>
                                                                        <div className="col-sm-2">
                                                                            <Field
                                                                                label=""
                                                                                onChange={e=>{
                                                                                    this.checkProblemsItem(e.target.checked,rowNum);
                                                                                }}
                                                                                name={is_inadequate}
                                                                                component={CheckboxInput}
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>

                                                        <Field
                                                            name={note}
                                                            label={'Ghi chú'}
                                                            component={TextArea}
                                                            rows="3"
                                                            required={isRequied}
                                                            validate={isRequied ? [Validator.required] :[]}
                                                        />
                                                    </div>

                                                    <div hidden={true}>
                                                        <field
                                                            label=""
                                                            name={customer_order_item_id}
                                                            component={TextInput}

                                                        />
                                                    </div>
                                                </div>
                                                <div>
                                                    <FieldArray
                                                        name={imageName}
                                                        component={this.renderImageInputs.bind(this)}
                                                        validate={maxImageUpload}
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                    </fieldset>
                                );
                            })
                        })}

                        <div style={{display : 'inline', float : 'right'}}>
                            <button type="submit" name="submit" value="submit" className="btn btn-lg btn-warning" >
                                <i className="fa fa-fw fa-check"/>
                                Cập nhật
                            </button>
                        </div>
                    </div>
            </form>
            </Card>
            </Layout>
        );
    }
}

VerifyCustomerOrder.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    model: PropTypes.array,
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
})(VerifyCustomerOrder))
