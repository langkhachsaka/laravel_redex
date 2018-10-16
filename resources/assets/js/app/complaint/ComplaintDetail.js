import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import {Field, reduxForm} from 'redux-form';
import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Card from "../../theme/components/Card";
import Layout from "../../theme/components/Layout";
import {toastr} from 'react-redux-toastr'
import Constant from "./meta/constant";
import ForbiddenPage from "../common/ForbiddenPage";
import PropTypes from 'prop-types';
import ComplaintHistory from "./components/ComplaintHistory";
import {Redirect} from "react-router-dom";
import TextArea from "../../theme/components/TextArea";
import TextInput from "../../theme/components/TextInput";
import CustomerServiceConfirm from "./components/CustomerServiceConfirm";
import UrlHelper from "../../helpers/Url";
import Validator from "../../helpers/Validator";
import SelectInput from "../../theme/components/SelectInput";
import OrderringOfficeConfirm from "./components/OrderringOfficeConfirm";

const CheckboxInput = ({input, label, checked, className}) => (
    <div className={className}>
        <label style={{color : checked ? "red" : "black"}}>{label} :</label>  <input {...input} type="checkbox" checked={checked} className="checkbox-item-verify"/>
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
class ComplaintDetail extends Component {

    constructor(props) {
        super(props);

        this.state = {
            model: {
                complaint_histories: [],
                verifyItem: null,
                customerOrderItem : null,
            },
            rate : null,
            redirectToComplaintList : false,
            isLoading: false,
            canAccess: props.userPermissions.complaint && props.userPermissions.complaint.view,
        };
    }

    componentDidMount() {
        this.fetchModel(this.getModelId());

        this.props.actions.changeThemeTitle("Chi tiết khiếu nại");
    }

    componentWillReceiveProps(nextProps) {
        const currentModelId = this.getModelId();
        const nextModelId = nextProps.match.params.id;

        if (currentModelId !== nextModelId) {
            this.fetchModel(nextModelId);
        }
    }

    fetchModel(id) {
        this.setState({isLoading: true});
        ApiService.get(Constant.resourcePath(id)).then((response) => {
            if (response.status === 403) {
                this.setState({canAccess: false});
                return;
            }
            const {data} = response;
            this.changeData(data.data.model);
            this.setState({
                model: data.data.model,
                rate : data.data.customer_rate && data.data.customer_rate != 0 ? data.data.customer_rate : data.data.rate,
                verifyItem : data.data.model.verify_customer_order_item,
                customerOrderItem : data.data.model.customer_order_item,
                isLoading: false,
            });
        });
    }

    changeData(model){
        if(model.status >= Constant.STATUS_ADMIN_PROCESSED) {
            this.props.change("comment_error_size",model.comment_error_size);
            this.props.change("comment_error_product",model.comment_error_product);
            this.props.change("comment_error_collor",model.comment_error_collor);
            this.props.change("comment_inadequate_product",model.comment_inadequate_product);
        }

    }

    handleSubmit(formProps) {
        const {model} = this.props;
        this.setState({
            isLoading : true
        });
       return ApiService.post(Constant.resourcePath("adminConfirm/"+this.getModelId()), formProps)
            .then(({data}) => {
                toastr.success(data.message);
                this.setState({
                    isLoading: false,
                    redirectToComplaintList: true,
                });
            });

    }

    getModelId() {
        return this.props.match.params.id;
    }

    render() {
        if(this.state.redirectToComplaintList ){
            return <Redirect to={"/complaint"}/>;
        }
        if (!this.state.canAccess) return <ForbiddenPage/>;
        const {handleSubmit} = this.props;
        const {model} = this.state;
        var roles = this.props.authUser.roles;
        this.state.verifyItem && this.props.change("note_verify_customer_order_item",this.state.verifyItem.note);
        return (
            <Layout>
                <Card isLoading={this.state.isLoading}>
                    <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                        <div>
                            <div className="row">

                                <div className="col-sm-3">
                                    Shop: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}} value={
                                        this.state.model && this.state.model.customer_order_item && this.state.model.customer_order_item.shop ?  this.state.model.customer_order_item.shop.name: ''} className="form-control"/>
                                </div>
                                <div className="col-sm-3">
                                    Mã giao dịch: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}} value={
                                    this.state.model && this.state.model.lading_code ?  this.state.model.lading_code.bill_code: ''} className="form-control"/>
                                </div>
                                <div className="col-sm-3">
                                    Mã vận đơn: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}} value={
                                    this.state.model && this.state.model.lading_code ?  this.state.model.lading_code.code: ''} className="form-control"/>
                                </div>
                                <div className="col-sm-3">
                                    Mã đơn hàng: <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}} value={
                                    this.state.model && this.state.model.customer_order_item ?  this.state.model.customer_order_item.customer_order_id: ''} className="form-control"/>
                                </div>
                            </div>
                            <div className={"row padding-tb-10"}>
                                <div className={"col-sm-7"} style={{bottom :"-25px"}}>
                                    {this.state.verifyItem && this.state.verifyItem.image1 && <p>Ảnh khiếu nại </p>}
                                </div>
                                <div className={"col-sm-5"}>
                                    Tình trạng khiếu nại <input  type="text" disabled style={{display :'inline', width : "auto", maxWidth: "70%", marginLeft:'20px'}} value={
                                    this.state.model ? this.state.model.status_name : ''} className="form-control"/>
                                </div>
                            </div>
                            {this.state.verifyItem && this.state.verifyItem.image1 &&
                            <fieldset className="fiedset-verify-customer-order bgc-grey">
                                <legend  className="legend-account-deposited"></legend>
                                <div>
                                    {this.state.verifyItem.image1 &&
                                    <div style={{float :'left'}}>
                                        <a href={UrlHelper.imageUrl(this.state.verifyItem.image1)} target="_blank"><img src={UrlHelper.imageUrl(this.state.verifyItem.image1)} className="img-thumbnail-verify"
                                                alt=""/></a>
                                    </div>}
                                    {this.state.verifyItem.image2 &&
                                    <div style={{float :'left'}}>
                                        <a href={UrlHelper.imageUrl(this.state.verifyItem.image2)} target="_blank"><img src={UrlHelper.imageUrl(this.state.verifyItem.image2)} className="img-thumbnail-verify"
                                                                                                         alt=""/></a>
                                    </div>}
                                    {this.state.verifyItem.image3 &&
                                    <div style={{float :'left'}}>
                                        <div style={{float :'left'}}>
                                            <a href={UrlHelper.imageUrl(this.state.verifyItem.image3)} target="_blank"><img src={UrlHelper.imageUrl(this.state.verifyItem.image3)} className="img-thumbnail-verify"
                                                                                                                            alt=""/></a>
                                        </div>
                                    </div>}
                                    {this.state.verifyItem.image4 &&
                                    <div style={{float :'left'}}>
                                        <div style={{float :'left'}}>
                                            <a href={UrlHelper.imageUrl(this.state.verifyItem.image4)} target="_blank"><img src={UrlHelper.imageUrl(this.state.verifyItem.image4)} className="img-thumbnail-verify"
                                                                                                                            alt=""/></a>
                                        </div>
                                    </div>}
                                    {this.state.verifyItem.image5 &&
                                    <div style={{float :'left'}}>
                                        <div style={{float :'left'}}>
                                            <a href={UrlHelper.imageUrl(this.state.verifyItem.image5)} target="_blank"><img src={UrlHelper.imageUrl(this.state.verifyItem.image5)} className="img-thumbnail-verify"
                                                                                                                            alt=""/></a>
                                        </div>
                                    </div>}
                                </div>
                            </fieldset>}
                            <div className={"row padding-tb-10"}>
                                <div className={"col-sm-3"}>
                                    Ảnh đặt mua
                                    { this.state.customerOrderItem && this.state.customerOrderItem.images &&<div>
                                        <ShowImages
                                            images={this.state.customerOrderItem.images}/>
                                    </div>
                                    }
                                </div>
                                <div className={"col-sm-9"} style={{top :"30px"}}>
                                    <div className={"row"}>
                                        <div className={"col-sm-3 "}>
                                            <Field
                                                label="Sai cỡ"
                                                name="error_size"
                                                checked={this.state.model.error_size ? true : false}
                                                component={CheckboxInput}
                                            />
                                        </div>
                                        <div className={"col-sm-3"}>
                                            <Field
                                                label="Sai hàng"
                                                name="error_product"
                                                checked={this.state.model.error_product ? true : false}
                                                component={CheckboxInput}
                                            />
                                        </div>
                                        <div className={"col-sm-3"}>
                                            <Field
                                                label="Sai màu"
                                                name="error_collor"
                                                checked={this.state.model.error_collor ? true : false}
                                                component={CheckboxInput}
                                            />
                                        </div>
                                        <div className={"col-sm-3"}>
                                            <Field
                                                label="Thiếu hàng"
                                                name="inadequate_product"
                                                checked={this.state.model.inadequate_product ? true : false}
                                                component={CheckboxInput}
                                            />
                                        </div>

                                    </div>
                                    <Field
                                        name="note_verify_customer_order_item"
                                        label={'Ghi chú'}
                                        disabled={true}
                                        component={TextArea}
                                        rows="3"
                                    />
                                </div>
                            </div>

                            {roles.indexOf(Constant.ROLE_ADMIN) != -1  && [Constant.STATUS_NOT_YET_PROCESS,Constant.STATUS_ADMIN_PROCESSED].indexOf(this.state.model.status) != -1 &&
                            <div>
                                <fieldset className="fiedset-verify-customer-order bgc-grey padding-tb-10">
                                <legend  className="legend-account-deposited">Ý kiến admin :</legend>
                                <div className={"row"}>
                                    <div className={"col-sm-2 txt-align-right"}>
                                        <Field
                                            label="Sai cỡ"
                                            name="error_size"
                                            className={"vertical-align-mid"}
                                            checked={this.state.model.error_size ? true : false}
                                            component={CheckboxInput}
                                        />
                                    </div>
                                    <div className={"col-sm-9"}>
                                        <Field
                                            label={""}
                                            component={TextInput}
                                            disabled={this.state.model.status!= Constant.STATUS_NOT_YET_PROCESS? true: !this.state.model.error_size ? true: false}
                                            name={"comment_error_size"}
                                            validate={this.state.model.error_size ?[Validator.required] : []}
                                        />
                                    </div>
                                </div>
                                <div className={"row"}>
                                    <div className={"col-sm-2 txt-align-right"}>
                                        <Field
                                            label="Sai hàng"
                                            name="error_product"
                                            checked={this.state.model.error_product ? true :  false}
                                            className={"vertical-align-mid"}
                                            component={CheckboxInput}
                                        />
                                    </div>
                                    <div className={"col-sm-9"}>
                                        <Field
                                            label={""}
                                            component={TextInput}
                                            disabled={this.state.model.status!= Constant.STATUS_NOT_YET_PROCESS? true:!this.state.model.error_product ? true : false}
                                            name={"comment_error_product"}
                                            validate={this.state.model.error_product ?[Validator.required] : []}
                                        />
                                    </div>
                                </div>
                                <div className={"row"}>
                                    <div className={"col-sm-2 txt-align-right"}>
                                        <Field
                                            label="Sai màu"
                                            name="error_collor"
                                            checked={this.state.model.error_collor ? true :  false}
                                            className={"vertical-align-mid"}
                                            component={CheckboxInput}
                                        />
                                    </div>
                                    <div className={"col-sm-9"}>
                                        <Field
                                            label={""}
                                            component={TextInput}
                                            disabled={this.state.model.status!= Constant.STATUS_NOT_YET_PROCESS? true:!this.state.model.error_collor ? true : false}
                                            name={"comment_error_collor"}
                                            validate={this.state.model.error_collor ?[Validator.required] : []}
                                        />
                                    </div>
                                </div>
                                <div className={"row"}>
                                    <div className={"col-sm-2 txt-align-right"}>
                                        <Field
                                            label="Thiếu hàng"
                                            name="inadequate_product"
                                            checked={this.state.model.inadequate_product ? true :  false}
                                            className={"vertical-align-mid"}
                                            component={CheckboxInput}
                                        />
                                    </div>
                                    <div className={"col-sm-9"}>
                                        <Field
                                            label={""}
                                            component={TextInput}
                                            disabled={this.state.model.status != Constant.STATUS_NOT_YET_PROCESS ? true: !this.state.model.inadequate_product ? true : false}
                                            name={"comment_inadequate_product"}
                                            validate={this.state.model.inadequate_product ?[Validator.required] : []}
                                        />
                                    </div>
                                </div>
                            </fieldset>
                                {roles.indexOf(Constant.ROLE_ADMIN) != 1 && this.state.model.status == Constant.STATUS_NOT_YET_PROCESS && <div style={{display : 'inline', float : 'right'}}>
                                    <button type="submit" name="submit" value="submit" className="btn btn-lg btn-warning" >
                                        <i className="fa fa-fw fa-check"/>
                                        Cập nhật
                                    </button>
                                </div>}
                            </div>}
                        </div>
                    </form>
                    <hr/>
                        {(roles.indexOf(Constant.ROLE_CUSTOMER_SERVICE_OFFICER) != -1  && this.state.model.status >= Constant.STATUS_ADMIN_PROCESSED || roles.indexOf(Constant.ROLE_ADMIN)!= -1  && this.state.model.status >= Constant.STATUS_CUSTOMER_SERVICE_PROCESSED) &&
                            <CustomerServiceConfirm rate={this.state.rate} model={this.state.model}/>
                        }
                        {[Constant.STATUS_CUSTOMER_SERVICE_PROCESSED,Constant.STATUS_ORDER_OFFICER_PROCESSED,].indexOf(model.status)!= -1 && roles.indexOf(Constant.ROLE_ORDERING_SERVICE_OFFICER) != -1 &&
                            <OrderringOfficeConfirm  rate={this.state.rate} model={model}/>
                        }



                </Card>

            </Layout>
        );
    }
}

ComplaintDetail.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
};

function mapStateToProps({auth}) {
    return {
        userPermissions: auth.permissions,
        authUser: auth.user
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'ComplaintDetail'
})(ComplaintDetail))
