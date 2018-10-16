import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import Dropzone from 'react-dropzone'
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import moment from "moment";
import TextArea from "../../../theme/components/TextArea";
import ImageConstant from "../../image/meta/constant";
import UrlHelper from "../../../helpers/Url";

const CheckboxInput = ({input, label, checked}) => (
    <div>
        {label} : <input {...input} type="checkbox" checked={checked} className="checkbox-item-verify"/>
    </div>
);

const TextInput = ({input, label, type, disabled, placeholder, required, meta: {touched, error, invalid}}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        {label}{input && required ? (<span className="text-danger">*</span>) : ''}
        : <input {...input}  type="text" disabled={disabled} style={{display :'inline', width : '50%',marginBottom:'20px'}}  className="form-control"/>
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
class ManyCustomerOrderDetailForm extends Component {

    constructor(props) {
        super(props);

        this.state = {
            models : [],
            verifyItem :[],
            indexState : 0,
            isLoading : true,
        };
    }

    componentDidMount() {
        const {model} = this.props;
        this.fetchData(model.lading_code);
        if (model) {
            this.props.initialize(model);
        }
    }
    fetchData(id) {
        return ApiService.get(Constant.resourcePath('getCustomerOrderDetail/'+id)).then(({data: {data}}) => {
            this.setState({
                models: data.ladingCode,
                verifyItem : data.verifyCustomerOrderItems,
                subLadingCode : data.subLadingCode,
                isLoading: false,
                ladingCode : data.subLadingCode.sub_lading_code,
                length : data.subLadingCode.length,
                height : data.subLadingCode.height,
                width : data.subLadingCode.width,
                weight : data.subLadingCode.weight,
            });
            this.props.change("lading_code",data.subLadingCode.sub_lading_code)
        });
    }

    render() {
        let index = 0;
        const {model, handleSubmit} = this.props;
        let numberLading = 0;
        this.state.models.map(item=> {
             item.bill_code.customer_order_items.map(customer_order_item=> {
                 numberLading++;
             })});

        let verifyItemId = [];
        this.state.verifyItem.map(item=> {
            verifyItemId.push(item.customer_order_item_id);
            });
        return (
            <div>
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
                                Dài: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={this.state.length && this.state.length} className="form-control"/>
                            </div>
                            <div className="col-sm-2">
                                Rộng: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={this.state.width && this.state.width} className="form-control"/>
                            </div>
                            <div className="col-sm-2">
                                Cao: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={this.state.height && this.state.height} className="form-control"/>
                            </div>
                            <div className="col-sm-2">
                                Nặng: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={this.state.length && this.state.weight} className="form-control"/>
                            </div>
                            <div className="col-sm-2">
                                Kiện: <input  type="text" disabled style={{display :'inline', width : '80px',marginLeft:'20px'}} value={numberLading} className="form-control"/>
                            </div>
                        </div>
                    </fieldset>
                    {this.state.isLoading && <div style={{textAlign:"center"}}>
                        <h3>Đang tải dữ liệu...</h3>
                    </div>}
                    {this.state.models.map(item=> {
                        return item.bill_code.customer_order_items.map(customer_order_item=> {
                            const imageName = "item["+(index)+"].images";
                            const quantity_verify = "item["+(index)+"].quantity_verify";
                            const note = "item["+(index)+"].note";
                            const is_broken_gash = "item["+(index)+"].is_broken_gash";
                            const is_error_color = "item["+(index)+"].is_error_color";
                            const is_error_product = "item["+(index)+"].is_error_product";
                            const is_error_size = "item["+(index)+"].is_error_size";
                            const is_exuberancy = "item["+(index)+"].is_exuberancy";
                            const is_inadequate = "item["+(index)+"].is_inadequate";
                            verifyItemId.indexOf(customer_order_item.id) != -1  ? index = index + 1 : '';
                            let verifyItem = {};
                            this.state.verifyItem.map(item => {
                               if(item.customer_order_item_id == customer_order_item.id){
                                   verifyItem = item;
                                   return;
                               }
                            });
                            this.props.change(quantity_verify,verifyItem.quantity_verify)
                            this.props.change(note,verifyItem.note);
                            return (
                                verifyItemId.indexOf(customer_order_item.id) != -1 && <fieldset key={index} className="fiedset-verify-customer-order">
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
                                                    Link:
                                                    <input  type="text" disabled style={{display :'inline', width : '600px',marginLeft:'20px'}} value={customer_order_item.link}  className="form-control"/>
                                                    <div className="row" style={{paddingBottom :"10px",paddingTop: "10px"}}>
                                                        <div className="col-sm-3">
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
                                                                    disabled={true}
                                                                    required={true}
                                                                    validate={[Validator.required]}
                                                                />
                                                                <div className="row">
                                                                    <div className={"col-sm-3"+(verifyItem.is_error_size && verifyItem.is_error_size == 1 ? " red-color-bold-text" : "")}>
                                                                        Sai cỡ
                                                                    </div>
                                                                    <div className="col-sm-2">
                                                                        <Field
                                                                            label=""
                                                                            checked={verifyItem.is_error_size && verifyItem.is_error_size == 1 ? true : false}
                                                                            name={is_error_size}
                                                                            component={CheckboxInput}
                                                                        />
                                                                    </div>
                                                                    <div className={"col-sm-3"+(verifyItem.is_error_color && verifyItem.is_error_color == 1 ? " red-color-bold-text" : "")}>
                                                                        Sai màu
                                                                    </div>
                                                                    <div className="col-sm-2">
                                                                        <Field
                                                                            label=""
                                                                            checked={verifyItem.is_error_color && verifyItem.is_error_color == 1 ? true : false}
                                                                            name={is_error_color}
                                                                            component={CheckboxInput}
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <div className="row">
                                                                    <div className={"col-sm-3"+(verifyItem.is_error_product && verifyItem.is_error_product == 1 ? " red-color-bold-text" : "")}>
                                                                        Sai hàng
                                                                    </div>
                                                                    <div className="col-sm-2">
                                                                        <Field
                                                                            label=""
                                                                            checked={verifyItem.is_error_product && verifyItem.is_error_product == 1 ? true : false}
                                                                            name={is_error_product}
                                                                            component={CheckboxInput}
                                                                        />
                                                                    </div>
                                                                    <div className={"col-sm-3"+(verifyItem.is_broken_gash && verifyItem.is_broken_gash == 1 ? " red-color-bold-text" : "")}>
                                                                        Bẹp, vỡ, rách
                                                                    </div>
                                                                    <div className="col-sm-2">
                                                                        <Field
                                                                            label=""
                                                                            checked={verifyItem.is_broken_gash && verifyItem.is_broken_gash == 1 ? true : false}
                                                                            name={is_broken_gash}
                                                                            component={CheckboxInput}
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <div className="row">
                                                                    <div className={"col-sm-3"+(verifyItem.is_exuberancy && verifyItem.is_exuberancy == 1 ? " red-color-bold-text" : "")}>
                                                                        Thừa sản phẩm
                                                                    </div>
                                                                    <div className="col-sm-2">
                                                                        <Field
                                                                            label=""
                                                                            checked={verifyItem.is_exuberancy && verifyItem.is_exuberancy == 1 ? true : false}
                                                                            name={is_exuberancy}
                                                                            component={CheckboxInput}
                                                                        />
                                                                    </div>
                                                                    <div className={"col-sm-3"+(verifyItem.is_inadequate && verifyItem.is_inadequate == 1 ? " red-color-bold-text" : "")}>
                                                                        Thiếu sản phẩm
                                                                    </div>
                                                                    <div className="col-sm-2">
                                                                        <Field
                                                                            label=""
                                                                            checked={verifyItem.is_inadequate && verifyItem.is_inadequate == 1 ? true : false}
                                                                            name={is_inadequate}
                                                                            component={CheckboxInput}
                                                                        />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    Ghi chú
                                                    <Field
                                                        name={note}
                                                        disabled={true}
                                                        component={TextArea}
                                                        rows="3"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {verifyItem.image1 &&
                                    <fieldset key={index} className="fiedset-verify-customer-order bgc-grey">
                                        <legend  className="legend-account-deposited">Hình ảnh sau khi kiểm tra kiện hàng</legend>
                                        <div>
                                            {verifyItem.image1 &&
                                            <div style={{float :'left'}}>
                                                <a href ={UrlHelper.imageUrl(verifyItem.image1)} target="_blank"><img src={UrlHelper.imageUrl(verifyItem.image1)} className="img-thumbnail-verify"
                                                        alt=""/></a>
                                            </div>}
                                            {verifyItem.image2 &&
                                            <div style={{float :'left'}}>

                                                <a href ={UrlHelper.imageUrl(verifyItem.image2)} target="_blank"><img src={UrlHelper.imageUrl(verifyItem.image2)} className="img-thumbnail-verify"
                                                        alt=""/></a>
                                            </div>}
                                            {verifyItem.image3 &&
                                            <div style={{float :'left'}}>
                                                <a href ={UrlHelper.imageUrl(verifyItem.image3)} target="_blank"><img src={UrlHelper.imageUrl(verifyItem.image3)} className="img-thumbnail-verify"
                                                        alt=""/></a>
                                            </div>}
                                            {verifyItem.image4 &&
                                            <div style={{float :'left'}}>
                                                <a href ={UrlHelper.imageUrl(verifyItem.image4)} target="_blank"><img src={UrlHelper.imageUrl(verifyItem.image4)} className="img-thumbnail-verify"
                                                        alt=""/></a>
                                            </div>}
                                            {verifyItem.image5 &&
                                            <div style={{float :'left'}}>
                                                <a href ={UrlHelper.imageUrl(verifyItem.image5)} target="_blank"><img src={UrlHelper.imageUrl(verifyItem.image5)} className="img-thumbnail-verify"
                                                        alt=""/></a>
                                            </div>}
                                        </div>
                                    </fieldset>}

                                </fieldset>
                            );
                        })
                    })}

                </div>
            </div>
        );
    }
}

ManyCustomerOrderDetailForm.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
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
    form: 'ManyCustomerOrderDetailForm'
})(ManyCustomerOrderDetailForm))
