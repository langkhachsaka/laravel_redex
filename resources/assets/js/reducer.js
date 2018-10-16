import {combineReducers} from 'redux';

import {reducer as formReducer} from 'redux-form';
import {reducer as toastrReducer} from 'react-redux-toastr';

import themeReducer from './theme/meta/reducer';
import authReducer from './app/auth/meta/reducer';
import userReducer from './app/user/meta/reducer';
import areaCodeReducer from './app/areaCode/meta/reducer';
import billOfLadingReducer from './app/billOfLading/meta/reducer';
import chinaOrderReducer from './app/chinaOrder/meta/reducer';
import customerReducer from './app/customer/meta/reducer';
import complaintReducer from './app/complaint/meta/reducer';
import customerOrderReducer from './app/customerOrder/meta/reducer';
import shopReducer from './app/shop/meta/reducer';
import warehouseReducer from './app/warehouse/meta/reducer';
import warehouseReceivingCNReducer from './app/warehouseReceivingCN/meta/reducer';
import inventoryReducer from './app/inventory/meta/reducer';
import deliveryReducer from './app/delivery/meta/reducer';
import taskReducer from './app/task/meta/reducer';
import transactionReducer from './app/transaction/meta/reducer';
import rateReducer from './app/rate/meta/reducer';
import ladingCodeReducer from './app/ladingCode/meta/reducer';
import trackingReducer from './app/tracking/meta/reducer';
import blogReducer from './app/blog/meta/reducer';

const rootReducer = combineReducers({
    theme: themeReducer,
    auth: authReducer,
    user: userReducer,
    areaCode: areaCodeReducer,
    billOfLading: billOfLadingReducer,
    chinaOrder: chinaOrderReducer,
    customer: customerReducer,
    complaint: complaintReducer,
    customerOrder: customerOrderReducer,
    shop: shopReducer,
    warehouse: warehouseReducer,
    warehouseReceivingCN: warehouseReceivingCNReducer,
    inventory: inventoryReducer,
    delivery: deliveryReducer,
    task: taskReducer,
    transaction: transactionReducer,
    rate: rateReducer,
    ladingCode: ladingCodeReducer,
    tracking: trackingReducer,
    blog: blogReducer,

    form: formReducer,
    toastr: toastrReducer
});

export default rootReducer;
