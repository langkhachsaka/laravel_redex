import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import Select2 from 'react-select2-wrapper';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import CustomerOrderConstant from "../../customerOrder/meta/constant";
import UserConstant from "../../user/meta/constant";
import {toastr} from 'react-redux-toastr'
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";
import Card from "../../../theme/components/Card";
import {Link} from "react-router-dom";
import Converter from "../../../helpers/Converter";
import Validator from "../../../helpers/Validator";
import UrlHelper from "../../../helpers/Url";
import Paginate from "../../common/Paginate";


class FormCreate extends Component {

    constructor(props) {
        super(props);

        this.state = {
            availableItems: [],
            searchData: {
                customers: [],
                shops: [],
            },
            searchParams: {
                customer_id: null,
                shop_id: null,
                page: 0,
                pageSize: 5,
            },
            itemError: null,
            isLoading: true,
        };
    }

    componentDidMount() {
        ApiService.get(CustomerOrderConstant.resourceItemPath("list-available"))
            .then(({data}) => {

                const customers = data.data
                    .filter(item => !!item.customer_order.customer)
                    .map(item => ({id: item.customer_order.customer.id, text: item.customer_order.customer.name}))
                    .filter((customer, index, self) => index === self.findIndex(c => c.id === customer.id));
                const shops = data.data
                    .filter(item => !!item.shop)
                    .map(item => ({id: item.shop.id, text: item.shop.name}))
                    .filter((shop, index, self) => index === self.findIndex(s => s.id === shop.id));

                const availableItems = data.data.map(item => _.assign({}, item, {
                    quantitySelected: 0,
                    inputQuantitySelect: item.quantity_available,
                    inputQuantitySelectError: null,
                    inputQuantityUpdate: 0,
                    inputQuantityUpdateError: null,
                }));

                this.setState({
                    availableItems: availableItems,
                    searchData: {
                        customers: customers,
                        shops: shops,
                    },
                    isLoading: false,
                });
            });
    }

    handleSubmit(formProps) {
        const {model} = this.props;

        formProps.items = this.state.availableItems
            .filter(item => item.quantitySelected > 0)
            .map(item => ({
                customer_order_item_id: item.id,
                quantity: item.quantitySelected,
                price_cny: item.price_cny
            }));

        if (!formProps.items.length) {
            this.setState({itemError: "Chưa có sản phẩm nào được chọn"});
            return;
        }

        return model ? this.handleSubmitAddItems(formProps) : this.handleSubmitCreateOrder(formProps);
    }

    handleSubmitCreateOrder(formProps) {
        return ApiService.post(Constant.resourcePath(), formProps)
            .then(({data}) => {
                toastr.success(data.message);
                this.props.actions.closeMainModal();

                if (this.props.onInsertSuccess) {
                    this.props.onInsertSuccess(data.data);
                }
            });
    }

    handleSubmitAddItems(formProps) {
        const {model} = this.props;
        formProps.items = formProps.items.map(item => {
            item.china_order_id = model.id;
            return item;
        });

        return ApiService.post(Constant.resourceItemPath(), formProps)
            .then(({data}) => {
                toastr.success(data.message);
                this.props.actions.closeMainModal();

                if (this.props.onInsertSuccess) {
                    this.props.onInsertSuccess(data.data);
                }
            });
    }


    render() {
        const {model, handleSubmit, submitting} = this.props;
        const {searchParams, searchData} = this.state;

        const availableProducts = this.state.availableItems
            .filter(item => item.quantity_available > item.quantitySelected)
            .filter(item => !searchParams.customer_id || searchParams.customer_id === item.customer_order.customer_id)
            .filter(item => !searchParams.shop_id || searchParams.shop_id === item.shop_id);

        const availableProductList = availableProducts
            .slice(this.state.searchParams.page * this.state.searchParams.pageSize, (this.state.searchParams.page + 1) * this.state.searchParams.pageSize)
            .map(item =>
                <div key={item.id} className="card">
                    <div className="card-content">
                        <div className="cn-order-item media align-items-stretch">
                            <div style={{width: '100px'}}>
                                {!!item.images.length &&
                                <img src={UrlHelper.imageUrl(item.images[0].path)} className="img-fluid" alt=""/>}
                            </div>
                            <div className="pl-1 pr-1 media-body">
                                <h5 className="item-name"><a href={item.link} target="_blank">{item.description}</a></h5>
                                <p className="item-colour">Màu sắc: <b>{item.colour}</b></p>
                                <p className="item-size">Kích cỡ (cm): <b>{item.size}</b></p>
                                <p className="item-unit">Số
                                    lượng: <b>{item.quantity_available - item.quantitySelected}/{item.quantity}</b> {item.unit}
                                </p>
                                <p className="item-order">Đơn hàng: <b>{item.customer_order.id}</b></p>
                                <p className="item-customer">Khách hàng: <b>{item.customer_order.customer.name}</b>
                                </p>
                                {item.shop && <p className="item-shop">Nguồn hàng: <b>{item.shop.name}</b></p>}
                            </div>
                            <div style={{width: '100px'}}>
                                <div className="form-group "><h5>Số lượng <span className="text-danger">*</span>
                                </h5>
                                    <div className="controls">
                                        <input type="number" min={1}
                                               max={item.quantity_available - item.quantitySelected}
                                               className="form-control form-control-sm"
                                               value={item.inputQuantitySelect}
                                               onChange={e => {
                                                   const val = e.target.value;
                                                   const newItem = _.assign({}, item);
                                                   newItem.inputQuantitySelect = val;

                                                   this.setState(prevState => {
                                                       return {availableItems: prevState.availableItems.map(i => i.id === item.id ? newItem : i)};
                                                   });
                                               }}/>
                                        {item.inputQuantitySelectError &&
                                        <div
                                            className="help-block text-danger">{item.inputQuantitySelectError}</div>}
                                    </div>
                                </div>
                                <div>
                                    <a className="btn btn-sm btn-primary btn-block" onClick={e => {
                                        e.preventDefault();
                                        const newItem = _.assign({}, item);
                                        const val = item.inputQuantitySelect;

                                        if (Validator.required(val)) {
                                            newItem.inputQuantitySelectError = "Số lượng không được để trống";
                                        } else if (Validator.requireInt(val)) {
                                            newItem.inputQuantitySelectError = "Số lượng phải là số nguyên";
                                        } else {
                                            const num = Converter.str2int(val);
                                            if (num < 1 || num > item.quantity_available - item.quantitySelected) {
                                                newItem.inputQuantitySelectError = "Số lượng phải nằm trong khoảng 1 - " + (item.quantity_available - item.quantitySelected);
                                            } else {
                                                newItem.inputQuantitySelectError = null;
                                            }
                                        }

                                        if (!newItem.inputQuantitySelectError) {
                                            newItem.quantitySelected = item.quantitySelected + Converter.str2int(item.inputQuantitySelect);
                                            newItem.inputQuantityUpdate = newItem.quantitySelected;
                                            newItem.inputQuantitySelect = item.quantity_available - newItem.quantitySelected;
                                        }

                                        this.setState(prevState => ({
                                            availableItems: prevState.availableItems.map(i => i.id === item.id ? newItem : i),
                                            itemError: null,
                                        }));
                                    }}
                                    >Thêm</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );

        const selectedProducts = this.state.availableItems
            .filter(item => item.quantitySelected > 0)
            .map(item =>
                <div key={item.id} className="card">
                    <div className="card-content">
                        <div className="cn-order-item media align-items-stretch">
                            <div style={{width: '100px'}}>
                                {!!item.images.length &&
                                <img src={UrlHelper.imageUrl(item.images[0].path)} className="img-fluid" alt=""/>}
                            </div>
                            <div className="pl-1 pr-1 media-body">
                                <h5 className="item-name"><a href={item.link} target="_blank">{item.description}</a></h5>
                                <p className="item-colour">Màu sắc: <b>{item.colour}</b></p>
                                <p className="item-size">Kích cỡ (cm): <b>{item.size}</b></p>
                                <p className="item-unit">Số lượng còn: <b>{item.quantitySelected}</b> {item.unit}</p>
                                <p className="item-order">Đơn hàng: <b>{item.customer_order.id}</b></p>
                                <p className="item-customer">Khách hàng: <b>{item.customer_order.customer.name}</b></p>
                                {item.shop && <p className="item-shop">Nguồn hàng: <b>{item.shop.name}</b></p>}
                            </div>
                            <div style={{width: '100px'}}>
                                <div className="form-group "><h5>Số lượng <span className="text-danger">*</span></h5>
                                    <div className="controls">
                                        <input type="number" min={1} max={item.quantitySelected}
                                               className="form-control form-control-sm"
                                               value={item.inputQuantityUpdate}
                                               onChange={e => {
                                                   const val = e.target.value;
                                                   const newItem = _.assign({}, item);
                                                   newItem.inputQuantityUpdate = val;

                                                   this.setState(prevState => {
                                                       return {availableItems: prevState.availableItems.map(i => i.id === item.id ? newItem : i)};
                                                   });
                                               }}/>
                                        {item.inputQuantityUpdateError &&
                                        <div className="help-block text-danger">{item.inputQuantityUpdateError}</div>}
                                    </div>
                                </div>
                                <div>
                                    <a className="btn btn-sm btn-info btn-block" onClick={(e) => {
                                        e.preventDefault();
                                        const newItem = _.assign({}, item);
                                        const val = item.inputQuantityUpdate;

                                        if (Validator.required(val)) {
                                            newItem.inputQuantityUpdateError = "Số lượng không được để trống";
                                        } else if (Validator.requireInt(val)) {
                                            newItem.inputQuantityUpdateError = "Số lượng phải là số nguyên";
                                        } else {
                                            const num = Converter.str2int(val);
                                            if (num < 1 || num > item.quantitySelected) {
                                                newItem.inputQuantityUpdateError = "Số lượng phải nằm trong khoảng 1 - " + item.quantitySelected;
                                            } else {
                                                newItem.inputQuantityUpdateError = null;
                                            }
                                        }

                                        if (!newItem.inputQuantityUpdateError) {
                                            item.inputQuantityUpdate = Converter.str2int(item.inputQuantityUpdate);
                                            newItem.quantitySelected = item.inputQuantityUpdate;
                                            newItem.inputQuantitySelect = item.quantity_available - item.inputQuantityUpdate;
                                        }

                                        this.setState(prevState => ({availableItems: prevState.availableItems.map(i => i.id === item.id ? newItem : i)}));
                                    }}
                                    >Cập nhật</a>
                                    <a className="btn btn-sm btn-danger btn-block" onClick={(e) => {
                                        e.preventDefault();
                                        const newItem = _.assign({}, item, {
                                            quantitySelected: 0,
                                            inputQuantitySelect: item.quantity_available,
                                            inputQuantityUpdate: 0
                                        });
                                        this.setState(prevState => {
                                            return {availableItems: prevState.availableItems.map(i => i.id === item.id ? newItem : i)};
                                        });
                                    }}>Xoá</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                {!model && <Field
                    name="user_purchasing_id"
                    component={Select2Input}
                    select2Options={{
                        ajax: {
                            url: AppConfig.API_URL + UserConstant.resourcePath("list?role=" + UserConstant.ROLE_USER_PURCHASING),
                            delay: 250
                        }
                    }}
                    label="Nhân viên đặt hàng"
                    required={true}
                    validate={[Validator.required]}
                />}

                <div className="row">
                    <div className="col-sm-6">
                        <Card title="Danh sách sản phẩm đang chờ" isLoading={this.state.isLoading}>

                            <div className="row">
                                <div className="col-sm-6">
                                    <div className="form-group">
                                        <Select2
                                            value={searchParams.customer_id}
                                            data={searchData.customers}
                                            options={{placeholder: 'Lọc theo khách hàng', allowClear: true}}
                                            className="form-control"
                                            onChange={e => {
                                                const val = Converter.str2int(e.target.value) || null;
                                                this.setState(({searchParams}) => {
                                                    searchParams.customer_id = val;
                                                    searchParams.page = 0;
                                                    return {searchParams: searchParams};
                                                });
                                            }}
                                        />
                                    </div>
                                </div>
                                <div className="col-sm-6">
                                    <div className="form-group">
                                        <Select2
                                            value={searchParams.shop_id}
                                            data={searchData.shops}
                                            options={{placeholder: 'Lọc theo nguồn hàng', allowClear: true}}
                                            className="form-control"
                                            onChange={e => {
                                                const val = Converter.str2int(e.target.value) || null;
                                                this.setState(({searchParams}) => {
                                                    searchParams.shop_id = val;
                                                    searchParams.page = 0;
                                                    return {searchParams: searchParams};
                                                });
                                            }}
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="list-cn-order-items">
                                {availableProductList}
                                <div className="text-center">
                                    <Paginate
                                        pageCount={availableProducts.length / this.state.searchParams.pageSize}
                                        onPageChange={(data) => {
                                            this.setState(({searchParams}) => {
                                                searchParams.page = data.selected;
                                                return {searchParams: searchParams};
                                            });
                                        }}
                                        currentPage={this.state.searchParams.page}
                                    />
                                </div>
                            </div>
                        </Card>
                    </div>
                    <div className="col-sm-6">
                        <Card title="Danh sách sản phẩm đã chọn">
                            <div className="form-group">
                                <div className="list-cn-order-items">
                                    {selectedProducts}
                                </div>
                                {this.state.itemError && <p className="text-danger help-block">{this.state.itemError}</p>}
                            </div>
                            <div className="form-group">
                                <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                                    <i className="fa fa-fw fa-check"/> Thêm
                                </button>
                            </div>
                        </Card>
                    </div>
                </div>

            </form>
        );
    }
}

FormCreate.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setListState: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'ChinaOrderInsertForm'
})(FormCreate))
