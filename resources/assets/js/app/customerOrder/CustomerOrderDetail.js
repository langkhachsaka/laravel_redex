import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";

import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Card from "../../theme/components/Card";
import Layout from "../../theme/components/Layout";
import Constant from "../customerOrder/meta/constant";
import ForbiddenPage from "../common/ForbiddenPage";
import {Field, formValueSelector, reduxForm} from "redux-form";
import TextInput from "../../theme/components/TextInput";
import UrlHelper from "../../helpers/Url";
import Formatter from "../../helpers/Formatter";
import ShowImages from "../common/ShowImages";
import TextArea from "../../theme/components/TextArea";
import Converter from "../../helpers/Converter";
import SelectInput from "../../theme/components/SelectInput";
import {toastr} from 'react-redux-toastr'
import DatePickerInput from "../../theme/components/DatePickerInput";

import FormUpdateLadingCode from "./components/FormUpdateLadingCode";


const formName = 'CustomerOrderDetailForm';
const formSelector = formValueSelector(formName);


function calculateDiscountPrice(quantity, price, discount_percent, discount_price) {
    quantity = Converter.str2int(quantity);
    price = Converter.str2float(price);
    discount_percent = Converter.str2float(discount_percent);
    discount_price = Converter.str2float(discount_price);
    let totalPrice;
    if (discount_percent > 0) {
        totalPrice = quantity * price * (100 - discount_percent) / 100;
    } else if (discount_price) {
        totalPrice = quantity * (price - discount_price);
    } else {
        totalPrice = quantity * price
    }

    return Formatter.number(totalPrice);
}


class ItemForm extends Component {

    constructor(props) {
        super(props);

        const {item} = props;

        this.state = {
            quantity: item.quantity || 0,
            price_cny: item.price_cny || 0,
            shop_quantity : item.shop_quantity || 0,
            alerted : item.alerted,
            isNotEnough : item.shop_quantity ? item.shop_quantity < item.quantity ? true : false : false,
            showDiscount: false,
            discount_percent: item.discount_percent || 0,
            discount_price: item.discount_price || 0,
            discount_customer_percent: item.discount_customer_percent || 0,
            discount_customer_price: item.discount_customer_price || 0,
            surcharge: Converter.str2float(item.surcharge) || 0,
        };
    }

    getDiscountPrice() {
        return calculateDiscountPrice(this.state.quantity, this.state.price_cny, this.state.discount_percent, this.state.discount_price);
    }

    getCustomerDiscountPrice() {
        return calculateDiscountPrice(this.state.quantity, this.state.price_cny, this.state.discount_customer_percent, this.state.discount_customer_price) + this.state.surcharge;
    }

    render() {
        const {item, index, itemIndex} = this.props;

        return (
            <tr>
                <td>{index + 1}</td>
                <td className="pr-0 pl-0" style={{width: '200px', position : 'relative'}}>
                    {!!item.images.length &&
                    <a onClick={e => {
                        e.preventDefault();
                        this.props.actions.openMainModal(<ShowImages
                            images={item.images}/>, "Ảnh sản phẩm");
                    }}><img src={UrlHelper.imageUrl(item.images[0].path)}
                            className="img-fluid"
                            alt=""/></a>}
                    {this.state.isNotEnough && this.state.alerted == 0 && <div className={'btn-alert-not-enough'}>
                        <button key="btn-delete" type={'button'} className="btn btn-sm btn-danger square" onClick={() => {
                            swal({
                                title: "Gửi cảnh báo thiếu hàng",
                                text: 'Cảnh báo đến khách hàng số lượng shop không đủ ('+this.state.shop_quantity + '/' +item.quantity+')?',
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            })
                                .then((willDelete) => {
                                    if (willDelete) {
                                        ApiService.post(Constant.resourceItemPath('alert-to-customer-not-enough/'+item.id+'/'+this.state.shop_quantity))
                                            .then(({data}) => {
                                                this.setState({
                                                    alerted: 1
                                                })
                                                swal(data.message, {icon: "info"});
                                            });
                                    }
                                });
                        }}><i className="ft-bell"/> Báo thiếu hàng</button>
                    </div>}
                    {this.state.alerted == 1 && <div className={'btn-alert-not-enough'}>
                        <button key="btn-delete" type={'button'} className="btn btn-sm btn-info square"><i className="ft-alert-circle"/> Đã cảnh báo</button>
                    </div>}
                    {this.state.alerted == 2 && <div className={'btn-alert-not-enough'}>
                        <button key="btn-delete" type={'button'} className="btn btn-sm btn-success square"><i className="ft-check"/> K/H đã xác nhận</button>
                    </div>}
                </td>
                <td>
                    <table
                        className="table table-bordered">
                        <tbody>
                        <tr>
                            <td><a href={item.link} target="_blank">Link gốc</a></td>
                            <td className="text-bold-600">Kích cỡ</td>
                            <td className="text-bold-600">Màu sắc</td>
                            <td className="text-bold-600">Đơn vị</td>
                            <td className="text-bold-600">Giá web</td>
                            <td className="text-bold-600">Số lượng</td>
                            <td className="text-bold-600">Tổng tiền hàng</td>
                        </tr>
                        <tr>
                            <td><a href={item.discount_link} target="_blank">Link chiết khấu</a></td>
                            <td>{item.size}</td>
                            <td>{item.colour}</td>
                            <td>{item.unit}</td>
                            <td>￥{Formatter.money(item.price_cny)}</td>
                            <td>{item.quantity}</td>
                            <td>￥{Formatter.money(item.total_price)}</td>
                        </tr>
                        <tr>
                            <td>Mô tả</td>
                            <td colSpan={6}>{item.description}</td>
                        </tr>
                        <tr>
                            <td colSpan={2}>
                                <Field
                                    name={`customer_order_items[${itemIndex}].shop_quantity`}
                                    component={TextInput}
                                    onChange={e => {
                                        if(e.target.value < item.quantity){
                                            this.setState({shop_quantity : e.target.value, isNotEnough:true});
                                        } else {
                                            this.setState({shop_quantity : e.target.value, isNotEnough:false});
                                        }
                                    }}
                                    label="Số lượng Shop có"
                                />
                                <Field
                                    name={`customer_order_items[${itemIndex}].surcharge`}
                                    component={TextInput}
                                    label="Phụ phí (RMB)"
                                    onChange={e => {
                                        this.setState({surcharge: Converter.str2float(e.target.value || 0)});
                                    }}
                                />
                                <Field
                                    name={`customer_order_items[${itemIndex}].note`}
                                    component={TextArea}
                                    rows={4}
                                    label="Ghi chú"
                                />
                            </td>
                            <td colSpan={3}>
                                <h3>Chiết khấu/Redex</h3>
                                <h4>Tỷ lệ chiết khấu</h4>
                                <div className="row">
                                    <div className="col-sm-6">
                                        <Field
                                            name={`customer_order_items[${itemIndex}].discount_percent`}
                                            component={TextInput}
                                            label="Theo %"
                                            onChange={e => {
                                                this.setState({discount_percent: Converter.str2float(e.target.value || 0)});
                                            }}
                                        />
                                    </div>
                                    <div className="col-sm-6">
                                        <Field
                                            name={`customer_order_items[${itemIndex}].discount_price`}
                                            component={TextInput}
                                            label="Theo sp"
                                            onChange={e => {
                                                this.setState({discount_price: Converter.str2float(e.target.value || 0)});
                                            }}
                                        />
                                    </div>
                                </div>

                                <h4>Hình thức chiết khấu</h4>
                                <div className="row">
                                    <div className="col-sm-4">
                                        <label>
                                            <Field
                                                name={`customer_order_items[${itemIndex}].discount_formality`}
                                                component="input"
                                                type="radio"
                                                value="1"
                                            />{' '} Trước
                                        </label>
                                    </div>
                                    <div className="col-sm-4">
                                        <label>
                                            <Field
                                                name={`customer_order_items[${itemIndex}].discount_formality`}
                                                component="input"
                                                type="radio"
                                                value="2"
                                            />{' '} Sau
                                        </label>
                                    </div>
                                </div>

                                <h3 className="mt-2">Chiết khấu/Khách</h3>
                                <div className="row">
                                    <div className="col-sm-6">
                                        <Field
                                            name={`customer_order_items[${itemIndex}].discount_customer_percent`}
                                            component={TextInput}
                                            label="Theo %"
                                            onChange={e => {
                                                this.setState({discount_customer_percent: Converter.str2float(e.target.value || 0)});
                                            }}
                                        />
                                    </div>
                                    <div className="col-sm-6">
                                        <Field
                                            name={`customer_order_items[${itemIndex}].discount_customer_price`}
                                            component={TextInput}
                                            label="Theo sp"
                                            onChange={e => {
                                                this.setState({discount_customer_price: Converter.str2float(e.target.value || 0)});
                                            }}
                                        />
                                    </div>
                                </div>
                            </td>
                            <td colSpan={2}>
                                <div className="form-group">
                                    <h5>Thành tiền - Khách (RMB)</h5>
                                    <div className="controls">
                                        <input readOnly={true} className="form-control"
                                               value={Formatter.money(this.getCustomerDiscountPrice())}/>
                                    </div>
                                </div>
                                <div className="form-group">
                                    <h5>Thành tiền - Khách (VND)</h5>
                                    <div className="controls">
                                        <input readOnly={true} className="form-control"
                                               value={Formatter.money(this.getCustomerDiscountPrice() * this.props.valueExchangeRate)}/>
                                    </div>
                                </div>
                                <div className="form-group">
                                    <h5>Thành tiền - Redex (RMB)</h5>
                                    <div className="controls">
                                        <input readOnly={true} className="form-control"
                                               value={Formatter.money(this.getDiscountPrice())}/>
                                    </div>
                                </div>
                                <div className="form-group">
                                    <h5>Thành tiền - Redex (RMB)</h5>
                                    <div className="controls">
                                        <input readOnly={true} className="form-control"
                                               value={Formatter.money(this.getDiscountPrice() * this.props.valueExchangeRate)}/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        );
    }
}

ItemForm = connect(
    state => {
        const valueExchangeRate = Converter.str2float(formSelector(state, 'money_exchange_rate') || 0); // tỷ giá tiền NDT - VND

        return {
            valueExchangeRate
        }
    },
    dispatch => {
        return {
            actions: bindActionCreators(_.assign({}, themeActions), dispatch),
            // changeFieldValue: function(field, value) {
            //     dispatch(change('CustomerOrderCreateForm', field, value))
            // }
        }
    }
)(ItemForm);


class CustomerOrderDetail extends Component {

    constructor(props) {
        super(props);

        this.state = {
            model: {
                customer_order_items: []
            },
            isLoading: true,
            canAccess: props.userPermissions.customer_order && props.userPermissions.customer_order.view,
        };
    }

    componentDidMount() {
        this.fetchModel(this.getModelId());

        this.props.actions.changeThemeTitle("Đặt hàng");
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

            const {data: {data}} = response;

            data.customer_order_items.forEach(item => {
                if (item.discount_formality) item.discount_formality = item.discount_formality.toString();
            });

            data.shop_bill_codes = [];
            data.bill_codes.forEach(bCode => {
                // if (bCode.delivery_type) bCode.delivery_type = bCode.delivery_type.toString();
                // if (bCode.insurance_type) bCode.insurance_type = bCode.insurance_type.toString();
                // if (bCode.reinforced_type) bCode.reinforced_type = bCode.reinforced_type.toString();

                data.shop_bill_codes[bCode.shop_id] = bCode;
            });

            this.props.initialize(data);

            this.setState({
                model: data,
                isLoading: false,
            });
        });
    }

    getModelId() {
        return this.props.match.params.id;
    }

    handleSubmit(formProps) {
        const modelId = this.getModelId();

        return ApiService.post(Constant.resourcePath(modelId + "/update2"), formProps)
            .then(({data}) => {
                toastr.success(data.message);

                this.setState({
                    model: data.data,
                    isLoading: false,
                });
            });
    }

    render() {
        if (!this.state.canAccess) return <ForbiddenPage/>;
        const {model} = this.state;
        const {handleSubmit, submitting} = this.props;
        const {summaryValues} = this.props;

        const listShops = _.uniqBy(
            model.customer_order_items.filter(item => !!item.shop).map(item => item.shop),
            shop => shop.id
        );

        const listNoShopItems = model.customer_order_items.filter(item => !item.shop);

        return (
            <Layout>
                <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                    {!this.state.isLoading && listShops.map(shop => {

                        const billCode = model.bill_codes.find(b => b.shop_id === shop.id);

                        const shopItems = model.customer_order_items.filter(item => item.shop ? item.shop.id === shop.id : !shop.id);

                        return (
                            <div key={shop.id} className="mb-3">
                                <Card isLoading={this.state.isLoading}>

                                    <div className="row">
                                        <div className="col-sm-3">
                                            Shop:<br/>
                                            <b>{shop.name}</b>
                                        </div>
                                        <div className="col-sm-3">
                                            <Field
                                                name={`shop_bill_codes[${shop.id}].order_date`}
                                                component={DatePickerInput}
                                                onDateChange={(date) => {
                                                    this.props.change(`shop_bill_codes[${shop.id}].order_date`, date.format("YYYY-MM-DD"));
                                                }}
                                                label="Ngày đặt"
                                            />
                                        </div>
                                        <div className="col-sm-3">
                                            <Field
                                                name={`shop_bill_codes[${shop.id}].bill_code`}
                                                component={TextInput}
                                                label="Mã GD"
                                            />
                                        </div>
                                        <div className="col-sm-3">
                                            {billCode && <a onClick={(e) => {
                                                e.preventDefault();
                                                this.props.actions.openMainModal(
                                                    <FormUpdateLadingCode
                                                        billCode={billCode}
                                                        setDetailState={this.setState.bind(this)}
                                                    />, "Mã giao dịch: " + billCode.bill_code
                                                );
                                            }}>
                                                Mã vận đơn ({billCode.lading_codes.length})
                                            </a>}
                                        </div>
                                    </div>

                                    <hr/>

                                    <div>

                                        <table className="table table-hover">

                                            <tbody>
                                            {shopItems.map((item, index) =>
                                                <ItemForm
                                                    key={item.id}
                                                    item={item}
                                                    index={index}
                                                    itemIndex={model.customer_order_items.findIndex(orderItem => orderItem.id === item.id)}
                                                />
                                            )}
                                            </tbody>

                                        </table>

                                    </div>


                                    <div className="row">
                                        <div className="col-sm-4">
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    <h5 className="text-right lh-40">Chuyển phát</h5>
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field name={`shop_bill_codes[${shop.id}].delivery_type`}
                                                           component={SelectInput}>
                                                        <option value="" key={0}>-</option>
                                                        {Constant.DELIVERY_TYPES.map(type => <option key={type.id}
                                                                                                     value={type.id}>{type.text}</option>)}
                                                    </Field>
                                                </div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    <h5 className="text-right lh-40">Bảo hiểm</h5>
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field name={`shop_bill_codes[${shop.id}].insurance_type`}
                                                           component={SelectInput}>
                                                        <option value="" key={0}>-</option>
                                                        {Constant.INSURANCE_TYPES.map(type => <option key={type.id}
                                                                                                      value={type.id}>{type.text}</option>)}
                                                    </Field>
                                                </div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    <h5 className="text-right lh-40">Gia cố</h5>
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field name={`shop_bill_codes[${shop.id}].reinforced_type`}
                                                           component={SelectInput}>
                                                        <option value="" key={0}>-</option>
                                                        {Constant.REINFORCED_TYPES.map(type => <option key={type.id}
                                                                                                       value={type.id}>{type.text}</option>)}
                                                    </Field>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-sm-4">
                                            <div className="form-group">
                                                <div className="row controls">
                                                    <div className="col-sm-6">
                                                        <h5 className="text-right lh-40">Tổng SL</h5>
                                                    </div>
                                                    <div className="col-sm-6">
                                                        <input readOnly={true} className="form-control"
                                                               value={Formatter.money(summaryValues.shops[shop.id].quantity)}/>
                                                    </div>
                                                </div>
                                                <div className="row controls">
                                                    <div className="col-sm-6">
                                                        <h5 className="text-right lh-40">Thực chi (RMB)</h5>
                                                    </div>
                                                    <div className="col-sm-6">
                                                        <input readOnly={true} className="form-control"
                                                               value={Formatter.money(summaryValues.shops[shop.id].total_shop)}/>
                                                    </div>
                                                </div>
                                                <div className="row controls">
                                                    <div className="col-sm-6">
                                                        <h5 className="text-right lh-40">Thực chi (VND)</h5>
                                                    </div>
                                                    <div className="col-sm-6">
                                                        <input readOnly={true} className="form-control"
                                                               value={Formatter.money(summaryValues.shops[shop.id].total_shop * this.props.valueExchangeRate)}/>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div className="col-sm-4">
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    <h5 className="text-right lh-40">Ship nội địa (RMB)</h5>
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field name={`shop_bill_codes[${shop.id}].fee_ship_inland`}
                                                           component={TextInput}/>
                                                </div>
                                            </div>
                                            <div className="row controls">
                                                <div className="col-sm-6">
                                                    <h5 className="text-right lh-40">Tổng tiền khách (RMB)</h5>
                                                </div>
                                                <div className="col-sm-6">
                                                    <input readOnly={true} className="form-control"
                                                           value={Formatter.money(summaryValues.shops[shop.id].total_customer)}/>
                                                </div>
                                            </div>
                                            <div className="row controls">
                                                <div className="col-sm-6">
                                                    <h5 className="text-right lh-40">Tổng tiền khách (VND)</h5>
                                                </div>
                                                <div className="col-sm-6">
                                                    <input readOnly={true} className="form-control"
                                                           value={Formatter.money(summaryValues.shops[shop.id].total_customer * this.props.valueExchangeRate)}/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </Card>
                            </div>
                        );
                    })}


                    {listNoShopItems.length > 0 && <div>
                        <Card>
                            <div className="mb-1 font-italic">
                                Shop chưa xác định
                            </div>
                            <table className="table table-hover table-form-order-items">
                                <tbody>
                                {listNoShopItems.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td className="pr-0 pl-0" style={{width: '200px'}}>
                                            {!!item.images.length &&
                                            <a onClick={e => {
                                                e.preventDefault();
                                                this.props.actions.openMainModal(<ShowImages
                                                    images={item.images}/>, "Ảnh sản phẩm");
                                            }}><img src={UrlHelper.imageUrl(item.images[0].path)}
                                                    className="img-fluid"
                                                    alt=""/></a>}
                                        </td>
                                        <td>
                                            <table
                                                className="table table-bordered">
                                                <tbody>
                                                <tr>
                                                    <td><a href={item.link} target="_blank">Link gốc</a></td>
                                                    <td className="text-bold-600">Kích cỡ</td>
                                                    <td className="text-bold-600">Màu sắc</td>
                                                    <td className="text-bold-600">Đơn vị</td>
                                                    <td className="text-bold-600">Giá web</td>
                                                    <td className="text-bold-600">Số lượng</td>
                                                    <td className="text-bold-600">Tổng tiền hàng</td>
                                                </tr>
                                                <tr>
                                                    <td><a href={item.discount_link} target="_blank">Link chiết khấu</a>
                                                    </td>
                                                    <td>{item.size}</td>
                                                    <td>{item.colour}</td>
                                                    <td>{item.unit}</td>
                                                    <td>￥{Formatter.money(item.price_cny)}</td>
                                                    <td>{item.quantity}</td>
                                                    <td>￥{Formatter.money(item.total_price)}</td>
                                                </tr>
                                                <tr>
                                                    <td>Mô tả</td>
                                                    <td colSpan={6}>{item.description}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                ))}
                                </tbody>
                            </table>
                        </Card>
                    </div>}


                    <Card isLoading={this.state.isLoading}>
                        <div className="mb-3">
                            <h3>Tổng</h3>
                            <div className="row">
                                <div className="col-sm-4">
                                    <div className="row controls">
                                        <div className="col-sm-6">
                                            <h5 className="text-right lh-40">Tổng SL</h5>
                                        </div>
                                        <div className="col-sm-6">
                                            <input readOnly={true} className="form-control"
                                                   value={Formatter.money(summaryValues.total.quantity)}/>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-sm-4">
                                    <div className="row controls">
                                        <div className="col-sm-6">
                                            <h5 className="text-right lh-40">Tổng thực chi (RMB)</h5>
                                        </div>
                                        <div className="col-sm-6">
                                            <input readOnly={true} className="form-control"
                                                   value={Formatter.money(summaryValues.total.total_shop)}/>
                                        </div>
                                    </div>
                                    <div className="row controls">
                                        <div className="col-sm-6">
                                            <h5 className="text-right lh-40">Tổng thực chi (VND)</h5>
                                        </div>
                                        <div className="col-sm-6">
                                            <input readOnly={true} className="form-control"
                                                   value={Formatter.money(summaryValues.total.total_shop * this.props.valueExchangeRate)}/>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-sm-4">
                                    <div className="row controls">
                                        <div className="col-sm-6">
                                            <h5 className="text-right lh-40">Tổng tiền khách (RMB)</h5>
                                        </div>
                                        <div className="col-sm-6">
                                            <input readOnly={true} className="form-control"
                                                   value={Formatter.money(summaryValues.total.total_customer)}/>
                                        </div>
                                    </div>
                                    <div className="row controls">
                                        <div className="col-sm-6">
                                            <h5 className="text-right lh-40">Tổng tiền khách (VND)</h5>
                                        </div>
                                        <div className="col-sm-6">
                                            <input readOnly={true} className="form-control"
                                                   value={Formatter.money(summaryValues.total.total_customer * this.props.valueExchangeRate)}/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                            <i className="fa fa-fw fa-check"/> Cập nhật
                        </button>
                    </Card>

                </form>
            </Layout>
        );
    }
}

function mapStateToProps(state) {
    const orderItems = formSelector(state, 'customer_order_items') || []; // các sản phẩm trong đơn hàng
    const billCodes = formSelector(state, 'shop_bill_codes') || []; // bill_codes
    const valueExchangeRate = Converter.str2float(formSelector(state, 'money_exchange_rate') || 0); // tỷ giá tiền NDT - VND

    const summaryValues = {
        shops: [], // Tổng theo shop
        total: { // Tổng cả đơn hàng (tức tổng của các shop)
            quantity: 0, // Tổng số lượng cả đơn hàng
            total_shop: 0, // Tổng thực chi cả đơn hàng
            total_customer: 0, // Tổng tiền khách cả đơn hàng
        },
    };

    orderItems.forEach(item => {
        const quantity = item.quantity; // số lượng
        const total_shop = calculateDiscountPrice(item.quantity, item.price_cny, item.discount_percent, item.discount_price); // thực chi

        let total_customer = calculateDiscountPrice(item.quantity, item.price_cny, item.discount_customer_percent, item.discount_customer_price); //tổng tiền khách
        if (item.surcharge) {
            total_customer += Converter.str2float(item.surcharge); // cộng phụ phí vào tổng tiền KH
        }

        if (item.shop) {
            // Cộng [ số lương ], [ thực chi ], [ tiền khách ] vào theo shop
            const tmp = summaryValues.shops[item.shop_id] || {quantity: 0, total_shop: 0, total_customer: 0};
            tmp.quantity += quantity;
            tmp.total_shop += total_shop;
            tmp.total_customer += total_customer;
            tmp.fee_ship_inland = Converter.str2float(_.get(billCodes[item.shop_id], "fee_ship_inland") || 0);

            summaryValues.shops[item.shop_id] = tmp;
        }


    });

    summaryValues.shops.map(shopValue => {
        shopValue.total_customer += shopValue.fee_ship_inland;

        // Cộng [ số lương ], [ thực chi ], [ tiền khách ] vào tổng
        summaryValues.total.quantity += shopValue.quantity;
        summaryValues.total.total_shop += shopValue.total_shop;
        summaryValues.total.total_customer += shopValue.total_customer;

        return shopValue;
    });


    return {
        userPermissions: state.auth.permissions,
        summaryValues,
        valueExchangeRate
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: formName
})(CustomerOrderDetail))
