import React, {Component} from 'react';
import {BrowserRouter, Route, Switch} from 'react-router-dom'
import ReactDOM from 'react-dom';
import {Provider} from 'react-redux';
import store from '../store';
import {verifyToken} from './auth/meta/action';

import ReduxToastr from 'react-redux-toastr'
import NotFoundPage from "./common/NotFoundPage";
import LoginForm from "./auth/components/LoginForm";
import AuthorizedRoute from "./common/AuthorizedRoute";

import Dashboard from "./dashboard/Dashboard";
// import Dashboard from "./Dashboard";
import User from "./user/User";
import Notification from "./notification/Notification";
import AreaCode from "./areaCode/AreaCode";
import BillOfLading from "./billOfLading/BillOfLading";
import ChinaOrder from "./chinaOrder/ChinaOrder";
import Customer from "./customer/Customer";
import CustomerOrder from "./customerOrder/CustomerOrder";
import CustomerOrderDetail from "./customerOrder/CustomerOrderDetail";
import CustomerOrderCreate from "./customerOrder/CustomerOrderCreate";
import CustomerOrderUpdate from "./customerOrder/CustomerOrderUpdate";
import Shop from "./shop/Shop";
import CourierCompany from "./courierCompany/CourierCompany";
import ChinaOrderDetail from "./chinaOrder/ChinaOrderDetail";
import Warehouse from "./warehouse/Warehouse";
import Task from "./task/Task";
import TaskDetail from "./task/TaskDetail";
import WarehouseReceivingCN from "./warehouseReceivingCN/WarehouseReceivingCN";
import Inventory from "./inventory/Inventory";
import Delivery from "./delivery/Delivery";
import DeliveryDetail from "./delivery/DeliveryDetail";
import Complaint from "./complaint/Complaint";
import ComplaintDetail from "./complaint/ComplaintDetail";
import WarehouseReceivingVN from "./warehouseReceivingVN/WarehouseReceivingVN";
import VerifyLading from "./warehouseReceivingVN/VerifyLading";
import BillOfLadingDetail from "./billOfLading/BillOfLadingDetail";
import Statistical from "./statistical/Statistical";
import Shipment from "./shipment/Shipment";
import Transaction from "./transaction/Transaction";
import Rate from "./rate/Rate";
import TransactionDetails from "./transaction/TransactionDetail";
import TransactionPaymentDetail from "./transaction/TransactionPaymentDetail";
import TransactionCharge from "./transaction/TransactionRecharge";
import Setting from "./setting/Setting";
import VerifyLadingCode from "./verifyLadingCode/VerifyLadingCode";
import VerifyCustomerOrder from "./verifyLadingCode/VerifyCustomerOrder";
import VerifyManyCustomerOrder from "./verifyLadingCode/VerifyManyCustomerOrder";
import LadingCode from "./ladingCode/LadingCode";
import Tracking from "./tracking/Tracking";
import PriceList from "./priceList/PriceList";
import Blog from "./blog/Blog";

// Used to log user in if token is valid
store.dispatch(verifyToken());

class App extends Component {
    render() {
        return (
            <BrowserRouter basename="/admin">
                <div>
                    <Switch>
                        <Route exact path="/auth/login" component={LoginForm}/>

                        <AuthorizedRoute exact path="/" component={Dashboard}/>

                        <AuthorizedRoute exact path="/area-code" component={AreaCode}/>

                        <AuthorizedRoute exact path="/bill-of-lading" component={BillOfLading}/>
                        <AuthorizedRoute exact path="/bill-of-lading/:id" component={BillOfLadingDetail}/>

                        <AuthorizedRoute exact path="/china-order" component={ChinaOrder}/>
                        <AuthorizedRoute exact path="/china-order/:id" component={ChinaOrderDetail}/>

                        <AuthorizedRoute exact path="/courier-company" component={CourierCompany}/>

                        <AuthorizedRoute exact path="/customer" component={Customer}/>

                        <AuthorizedRoute exact path="/complaint" component={Complaint}/>
                        <AuthorizedRoute exact path="/complaint/:id" component={ComplaintDetail}/>

                        <AuthorizedRoute exact path="/customer-order" component={CustomerOrder}/>
                        <AuthorizedRoute exact path="/customer-order/create" component={CustomerOrderCreate}/>
                        <AuthorizedRoute exact path="/customer-order/:id/edit" component={CustomerOrderUpdate}/>
                        <AuthorizedRoute exact path="/customer-order/:id" component={CustomerOrderDetail}/>

                        <AuthorizedRoute exact path="/task" component={Task}/>
                        <AuthorizedRoute exact path="/task/:id" component={TaskDetail}/>

                        <AuthorizedRoute exact path="/shop" component={Shop}/>

                        <AuthorizedRoute exact path="/inventory" component={Inventory}/>

                        <AuthorizedRoute exact path="/warehouse" component={Warehouse}/>

                        <AuthorizedRoute exact path="/warehouse-receiving-cn" component={WarehouseReceivingCN}/>
                        <AuthorizedRoute exact path="/shipment" component={Shipment}/>
                        <AuthorizedRoute exact path="/warehouse-receiving-vn" component={WarehouseReceivingVN}/>
                        <AuthorizedRoute exact path="/warehouse-receiving-vn/:id" component={VerifyLading}/>
                        <AuthorizedRoute exact path="/verify-lading-code" component={VerifyLadingCode}/>
                        <AuthorizedRoute exact path="/verify-lading-code/:id" component={VerifyCustomerOrder}/>
                        <AuthorizedRoute exact path="/verify-lading-code/2/:id" component={VerifyManyCustomerOrder}/>

                        <AuthorizedRoute exact path="/lading-code" component={LadingCode}/>

                        <AuthorizedRoute exact path="/delivery" component={Delivery}/>
                        <AuthorizedRoute exact path="/delivery/:id" component={DeliveryDetail}/>

                        <AuthorizedRoute exact path="/user" component={User}/>

                        <AuthorizedRoute exact path="/notification" component={Notification}/>

                        <AuthorizedRoute exact path="/statistical" component={Statistical}/>

                        <AuthorizedRoute exact path="/transaction" component={Transaction}/>
                        <AuthorizedRoute exact path="/transaction/:id" component={TransactionDetails}/>
                        <AuthorizedRoute exact path="/transaction/payment-detail/:id" component={TransactionPaymentDetail}/>
                        <AuthorizedRoute exact path="/transaction/recharge/:id" component={TransactionCharge}/>

                        <AuthorizedRoute exact path="/rate" component={Rate}/>

                        <AuthorizedRoute exact path="/setting" component={Setting}/>

                        <AuthorizedRoute exact path="/price-list" component={PriceList}/>

                        <AuthorizedRoute exact path="/tracking" component={Tracking}/>

                        <AuthorizedRoute exact path="/blog" component={Blog}/>

                        <Route path="*" component={NotFoundPage}/>
                    </Switch>
                    <ReduxToastr
                        timeOut={15000}
                        transitionIn="fadeIn"
                        transitionOut="fadeOut"/>
                </div>
            </BrowserRouter>
        );
    }
}

ReactDOM.render(
    <Provider store={store}>
        <App/>
    </Provider>,
    document.getElementById('root')
);
