import { createStore, applyMiddleware, compose } from 'redux';

import thunkMiddleware from 'redux-thunk';

import rootReducer from './reducer';

/**
 * Create a Redux store that holds the app state.
 */
const store = createStore(rootReducer, compose(
    applyMiddleware(thunkMiddleware),

    //For working redux dev tools in chrome (https://github.com/zalmoxisus/redux-devtools-extension)
    // window.devToolsExtension ? window.devToolsExtension() : function (f) {
    //     return f;
    // }
));

export default store;
