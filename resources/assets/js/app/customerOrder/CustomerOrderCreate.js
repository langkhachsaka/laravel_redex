import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Card from "../../theme/components/Card";
import Layout from "../../theme/components/Layout";
import {Redirect} from "react-router-dom";
import AppConfig from "../../config";
import Validator from "../../helpers/Validator";
import CustomerConstant from "../customer/meta/constant";
import Select2Input from "../../theme/components/Select2Input";
import UserConstant from "../user/meta/constant";
import {change, Field, FieldArray, formValueSelector, reduxForm, SubmissionError} from "redux-form";
import PropTypes from "prop-types";
import TextInput from "../../theme/components/TextInput";
import {toastr} from "react-redux-toastr";
import ApiService from "../../services/ApiService";
import Constant from "./meta/constant";
import ForbiddenPage from "../common/ForbiddenPage";
import ShopConstant from "../shop/meta/constant";
import Converter from "../../helpers/Converter";
import UrlHelper from "../../helpers/Url";
import Formatter from "../../helpers/Formatter";
import SelectInput from "../../theme/components/SelectInput";
import FormUploadImage from "./components/FormUploadImage";
import FormLinkImage from "./components/FormLinkImage";
import FormImportItem from "./components/FormImportItem";
import FormCreateCustomerAddress from "./components/FormCreateCustomerAddress";


let newRow = 10;

const formName = 'CustomerOrderCreateForm';
const formSelector = formValueSelector(formName);


class ItemForm extends Component {

    constructor(props) {
        super(props);

        const orderItem = props.fields.get(props.index);

        this.state = {
            images: orderItem.images || [],
            quantity: orderItem.quantity || "",
            price_cny: orderItem.price_cny || "",
        };
    }

    _handleSaveImages(images) {
        const {item} = this.props;

        this.setState({images: images});
        this.props.changeFieldValue(`${item}.images`, images);
        this.props.actions.closeMainModal();
    }

    render() {
        const {fields, item, index} = this.props;
        const orderItem = fields.get(index);

        return (
            <tr>
                <td>{index + 1}</td>
                <td>
                    <div>
                        <img
                            src={UrlHelper.imageUrl(this.state.images[0] || "http://via.placeholder.com/100x100")}
                            alt="" className="img-fluid"/>
                    </div>
                    <div className="clearfix">
                        <a className="pull-left" onClick={(e) => {
                            e.preventDefault();
                            this.props.actions.openMainModal(
                                <FormUploadImage
                                    onSave={this._handleSaveImages.bind(this)}
                                    images={this.state.images}
                                />, "Tải ảnh sản phẩm");
                        }}>Upload</a>
                        <a className="pull-right" onClick={(e) => {
                            e.preventDefault();
                            this.props.actions.openMainModal(
                                <FormLinkImage
                                    onSave={this._handleSaveImages.bind(this)}
                                    images={this.state.images}
                                />, "Nhập ảnh sản phẩm");
                        }}>Link</a>
                    </div>
                    <Field name={`${item}.images`} component={TextInput} type="hidden"/>{/*For show validate error only*/}
                </td>
                <td>
                    <div className="form-group-wrapper">
                        <Field
                            name={`${item}.description`}
                            component={TextInput}
                            placeholder="Mô tả *"
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="form-group-wrapper">
                        <Field
                            name={`${item}.link`}
                            component={TextInput}
                            placeholder="Link sản phẩm *"
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="form-group-wrapper">
                        <div className="row">
                            <div className="col-sm-6">
                                <Field
                                    name={`${item}.colour`}
                                    component={TextInput}
                                    placeholder="Màu sắc *"
                                    validate={[Validator.required]}
                                />
                            </div>
                            <div className="col-sm-6">
                                <Field
                                    name={`${item}.size`}
                                    component={TextInput}
                                    placeholder="Kích cỡ (cm) *"
                                    validate={[Validator.required]}
                                />
                            </div>
                        </div>
                    </div>
                    <div className="form-group-wrapper">
                        <div className="row">
                            <div className="col-sm-6">
                                <Field
                                    name={`${item}.weight`}
                                    component={TextInput}
                                    placeholder="Trọng lượng (kg)"
                                    validate={[Validator.requireFloat, Validator.greaterThan0]}
                                />
                            </div>
                            <div className="col-sm-6">
                                <Field
                                    name={`${item}.volume`}
                                    component={TextInput}
                                    placeholder="Tính khối (cm3)"
                                    validate={[Validator.requireFloat, Validator.greaterThan0]}
                                />
                            </div>
                        </div>
                    </div>
                </td>
                <td style={{maxWidth: '120px'}}>
                    <Field
                        name={`${item}.unit`}
                        component={TextInput}
                        placeholder="Đơn vị *"
                        validate={[Validator.required]}
                    />
                </td>
                <td style={{maxWidth: '120px'}}>
                    <Field
                        name={`${item}.quantity`}
                        component={TextInput}
                        placeholder="Số lượng *"
                        validate={[Validator.required, Validator.requireInt]}
                        onChange={e => {
                            this.setState({quantity: e.target.value});
                        }}
                    />
                </td>
                <td style={{maxWidth: '120px'}}>
                    <Field
                        name={`${item}.price_cny`}
                        component={TextInput}
                        placeholder="Giá web *"
                        validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                        onChange={e => {
                            this.setState({price_cny: e.target.value});
                        }}
                    />
                </td>
                <td>
                    <Field
                        name={`${item}.shop_id`}
                        component={Select2Input}
                        select2Data={orderItem && orderItem.shop ? [{id: orderItem.shop.id, text: orderItem.shop.name}] : []}
                        select2Options={{
                            ajax: {
                                url: AppConfig.API_URL + ShopConstant.resourcePath("list"),
                                delay: 250
                            }
                        }}
                        placeholder="Nguồn hàng"
                    />
                </td>
                <td>
                    <Field
                        name={`${item}.note`}
                        component={TextInput}
                        placeholder="Ghi chú"
                    />
                </td>
                <td>
                    ￥{Formatter.money(Converter.str2int(this.state.quantity || "0") * Converter.str2float(this.state.price_cny || "0"))}
                </td>
                <td>
                    <button className="btn btn-sm btn-danger square" type="button" disabled={fields.length < 2}
                            onClick={() => fields.remove(index)}>
                        <i className="ft-trash-2"/> Xoá
                    </button>
                </td>
            </tr>
        );
    }
}


ItemForm = connect(null, dispatch => {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch),
        changeFieldValue: function(field, value) {
            dispatch(change('CustomerOrderCreateForm', field, value))
        }
    }
})(ItemForm);


let ItemsForm = (props) => {

    const {fields, meta: {error}} = props;

    return (
        <table className="table table-hover table-form-order-items">
            <thead>
            <tr>
                <th>STT</th>
                <th style={{width: '100px'}}>Hình ảnh</th>
                <th>Thuộc tính</th>
                <th style={{maxWidth: '120px'}}>Đơn vị</th>
                <th style={{maxWidth: '120px'}}>Số lượng</th>
                <th style={{maxWidth: '120px'}}>Giá web</th>
                <th style={{minWidth: '200px'}}>Nguồn hàng</th>
                <th>Ghi chú</th>
                <th>Thành tiền</th>
                <th>
                    <a className="btn btn-info btn-sm btn-block square" onClick={(e) => {
                        e.preventDefault();
                        props.actions.openMainModal(
                            <FormImportItem
                                onImportSuccess={(data) => {
                                    data.forEach(item => {
                                        fields.push(item);
                                    });
                                }}
                            />, "Import sản phẩm");
                    }}><i className="ft-file-plus"/> Import sản phẩm
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>

            {fields.map((item, index) => <ItemForm item={item} index={index} key={index} fields={fields}/>)}

            <tr>
                <td colSpan={5}>.</td>
                <td colSpan={4} className="order-summary">
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Phí ship nội địa (RMB)</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <div className="form-group">
                                    <input className="form-control" value={0} readOnly={true}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Tổng tiền hàng (RMB)</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <div className="form-group">
                                    <input className="form-control" value={props.valueProductTotalPriceCNY} readOnly={true}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Tỷ giá</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <Field
                                    name="money_exchange_rate"
                                    component={TextInput}
                                    placeholder="Tỷ giá"
                                    disabled={true}
                                />
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Tổng tiền hàng (VNĐ)</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <div className="form-group">
                                    <input className="form-control" value={props.valueProductTotalPriceVND} readOnly={true}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Phí ship TQ-VN (VNĐ)</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <div className="form-group">
                                    <input className="form-control" value={0} readOnly={true}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Tổng đơn hàng (VNĐ)</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <div className="form-group">
                                    <input className="form-control" value={props.valueTotalPrice} readOnly={true}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Tạm ứng (VNĐ)</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <div className="form-group">
                                    <input className="form-control" value={props.valueMoneyPreDeposit} readOnly={true}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Dư nợ (VNĐ)</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <div className="form-group">
                                    <input className="form-control" value={props.valueMoneyDebt} readOnly={true}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Phí ship nội thành (VNĐ)</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <div className="form-group">
                                    <input className="form-control" value={0} readOnly={true}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            <h5 className="text-right">Tổng thanh toán (VNĐ)</h5>
                        </div>
                        <div className="col-sm-6">
                            <div className="form-group-wrapper">
                                <div className="form-group">
                                    <input className="form-control" value={props.valueTotalPayment} readOnly={true}/>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td className="align-top">
                    <div className="form-group-wrapper">
                        <div className="form-group-wrapper">
                            <input type="number" placeholder="Số lượng sản phẩm *"
                                   defaultValue={newRow}
                                   onChange={e => {
                                       newRow = e.target.value;
                                   }}
                                   className="form-control"/>
                        </div>
                        <button type="button" className="btn btn-success btn-sm btn-block square"
                                onClick={() => {
                                    let n = newRow || "1";
                                    n = Converter.str2int(n);
                                    for (let i = 0; i < n; i++) {
                                        fields.push({});
                                    }
                                }}>
                            Thêm sản phẩm
                        </button>
                    </div>
                    {error && <span>{error}</span>}
                </td>
            </tr>
            </tbody>
        </table>
    );

};

ItemsForm = connect(
    state => {
        const orderItems = formSelector(state, 'customer_order_items') || []; // các sản phẩm trong đơn hàng
        const exchangeRate = Converter.str2float(formSelector(state, 'money_exchange_rate') || 0); // tỷ giá tiền NDT - VND
        const depositPercent = Converter.str2float(formSelector(state, 'deposit_percent') || 0); // % tạm ứng
        const moneyDeposit = Converter.str2float(formSelector(state, 'money_deposit') || 0); // Tạm ứng
        const moneyDebt = Converter.str2float(formSelector(state, 'money_debt') || 0); // Dư nợ

        const valueProductTotalPriceCNY = Formatter.number(orderItems.map(item => (item.quantity || 0) * (item.price_cny || 0)).reduce((a, b) => a + b, 0)); // Tổng tiền hàng (RMB - NDT)
        const valueProductTotalPriceVND = Formatter.number(exchangeRate * valueProductTotalPriceCNY); // Tổng tiền hàng VND
        const valueTotalPrice = Formatter.number(valueProductTotalPriceVND); // Tổng đơn hàng
        const valueTotalPayment = Formatter.number(valueTotalPrice - moneyDeposit + moneyDebt); // Tổng thanh toán
        const valueMoneyPreDeposit = Formatter.number(valueProductTotalPriceVND * depositPercent / 100);
        const valueMoneyDebt = Formatter.number(valueTotalPrice - valueMoneyPreDeposit);

        return {
            valueProductTotalPriceCNY,
            valueProductTotalPriceVND,
            valueTotalPrice,
            valueTotalPayment,
            valueMoneyPreDeposit,
            valueMoneyDebt
        };
    },
    dispatch => {
        return {
            actions: bindActionCreators(_.assign({}, themeActions), dispatch)
        }
    }
)(ItemsForm);


class CustomerOrderCreate extends Component {

    constructor(props) {
        super(props);

        this.state = {
            customerSelected: {
                customer_addresses: []
            },
            order_rate: 0,
            redirectToDetailId: null, // redirect to detail page after insert new CustomerOrder
            isLoading: false,
        };
    }

    componentDidMount() {
        this.props.actions.changeThemeTitle("Tạo Đơn hàng mới");

        const items = [];
        for (let i = 0; i < 2; i++) {
            items.push({});
        }

        this.props.initialize({
            customer_order_items: items,
        });
    }

    handleSubmit(formProps) {

        // START validate
        const errors = {};
        let hasError = false;

        // validate item images
        errors.customer_order_items = {};
        formProps.customer_order_items.forEach((item, index) => {
            if (!item.images || !item.images.length) {
                hasError = true;
                errors.customer_order_items[index] = errors.customer_order_items[index] || {}; // Khởi tạo nếu chưa có
                errors.customer_order_items[index].images = "Chưa có ảnh sản phẩm";
            }
        });

        if (hasError) {
            throw new SubmissionError(errors); // SubmissionError là class của redux-form, chỉ sử dụng được trong `handleSubmit`, thường sử dụng khi validate phía server
        }
        //// END validate

        return ApiService.post(Constant.resourcePath(), formProps)
            .then(({data}) => {
                toastr.success(data.message);

                this.setState({redirectToDetailId: data.data.id});
            });
    }

    render() {
        const {userPermissions} = this.props;
        if (!userPermissions.customer_order || !userPermissions.customer_order.create) return <ForbiddenPage/>;
        const formDisabled = _.get(userPermissions, 'customer_order.form_disabled', {});

        if (this.state.redirectToDetailId) {
            return <Redirect to={"/customer-order/" + this.state.redirectToDetailId + "/edit"}/>;
        }

        const {handleSubmit, submitting} = this.props;

        return (
            <Layout>
                <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                    <Card isLoading={submitting || this.state.isLoading}>
                        <div className="mb-1">
                            <div className="row">
                                <div className="col-sm-3">
                                    <Field
                                        name="customer_id"
                                        component={Select2Input}
                                        select2Options={{
                                            ajax: {
                                                url: AppConfig.API_URL + CustomerConstant.resourcePath("list"),
                                                delay: 250
                                            }
                                        }}
                                        select2OnSelect={(e) => {
                                            this.setState({customerSelected: e.params.data});
                                            this.props.change("customer_billing_name", "");
                                            this.props.change("customer_billing_address", "");
                                            this.props.change("customer_billing_phone", "");
                                            this.props.change("customer_shipping_address_id", "");
                                            this.props.change("money_exchange_rate", e.params.data.order_rate);
                                            this.props.change("deposit_percent", e.params.data.order_pre_deposit_percent);

                                            if (e.params.data.customer_addresses.length) {
                                                const address = e.params.data.customer_addresses[0];
                                                this.props.change("customer_billing_name", address.name);
                                                this.props.change("customer_billing_address", address.address);
                                                this.props.change("customer_billing_phone", address.phone);
                                            }
                                        }}
                                        label="Khách hàng"
                                        required={true}
                                        validate={[Validator.required]}
                                    />
                                </div>
                                <div className="col-sm-6 offset-sm-3">
                                    <Field
                                        name="seller_id"
                                        component={Select2Input}
                                        select2Options={{
                                            placeholder: formDisabled.seller_id ? this.props.authUser.name : '',
                                            ajax: {
                                                url: AppConfig.API_URL + UserConstant.resourcePath("list?role=" + UserConstant.ROLE_SELLER),
                                                delay: 250
                                            }
                                        }}
                                        label="Nhân viên CSKH"
                                        disabled={formDisabled.seller_id}
                                        required={true}
                                        validate={[Validator.required]}
                                    />
                                </div>
                            </div>

                            <div className="row">
                                <div className="col-sm-6">
                                    <h4>Thông tin người mua</h4>

                                    <div className="row">
                                        <div className="col-sm-4">
                                            <Field
                                                name="customer_billing_name"
                                                component={TextInput}
                                                label="Tên"
                                                required={true}
                                                validate={[Validator.required]}
                                            />
                                        </div>
                                        <div className="col-sm-4">
                                            <Field
                                                name="customer_billing_address"
                                                component={TextInput}
                                                label="Địa chỉ"
                                                required={true}
                                                validate={[Validator.required]}
                                            />
                                        </div>
                                        <div className="col-sm-4">
                                            <Field
                                                name="customer_billing_phone"
                                                component={TextInput}
                                                label="Điện thoại"
                                                required={true}
                                                validate={[Validator.required]}
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div className="col-sm-6">
                                    <h4>Thông tin người nhận</h4>

                                    <Field
                                        name="customer_shipping_address_id"
                                        component={SelectInput}
                                        label="Địa chỉ nhận"
                                        required={true}
                                        validate={[Validator.required]}
                                        onChange={e => {
                                            if (e.target.value === "new") {
                                                this.props.actions.openMainModal(
                                                    <FormCreateCustomerAddress
                                                        customer={this.state.customerSelected}
                                                        onSave={(address) => {
                                                            this.props.change("customer_shipping_address_id", address.id);

                                                            this.setState(({customerSelected}) => {
                                                                customerSelected.customer_addresses.push(address);
                                                                return customerSelected;
                                                            });
                                                        }}
                                                    />,
                                                    "Thêm địa chỉ mới"
                                                );
                                                this.props.change("customer_shipping_address_id", "");
                                            }
                                        }}
                                    >
                                        <option value="" key="0">-</option>
                                        {this.state.customerSelected.customer_addresses.map(address =>
                                            <option key={address.id} value={address.id}>
                                                {address.name} - ĐT: {address.phone} - {address.full_address}
                                            </option>
                                        )}
                                        {this.state.customerSelected.id &&
                                        <option value="new" key="new" style={{color: "red"}}>Thêm địa chỉ mới</option>}
                                    </Field>
                                </div>
                            </div>
                        </div>

                        <div className="mb-1">
                            <h4>Danh sách sản phẩm trong đơn hàng</h4>
                            <div className="table-responsive">

                                <FieldArray name="customer_order_items" component={ItemsForm}/>

                            </div>
                        </div>

                        <div className="form-group">
                            <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                                <i className="fa fa-fw fa-check"/> Thêm mới
                            </button>
                        </div>
                    </Card>

                </form>
            </Layout>
        );
    }
}

CustomerOrderCreate.propTypes = {
    handleSubmit: PropTypes.func,
    submitting: PropTypes.bool,
};

function mapStateToProps(state) {
    return {
        userPermissions: state.auth.permissions,
        authUser: state.auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: formName
})(CustomerOrderCreate))
