import React, {Component} from "react";
import {connect} from "react-redux";
import {Redirect, Route} from "react-router-dom";


class AuthorizedRoute extends Component {
    render() {
        const {component: Component, loggedIn, ...rest} = this.props;
        return (
            <Route {...rest} render={props => {
                return loggedIn
                    ? <Component {...props} />
                    : <Redirect to="/auth/login"/>
            }}/>
        )
    }
}

const stateToProps = ({auth}) => ({
    loggedIn: auth.isAuthenticated
});

export default connect(stateToProps)(AuthorizedRoute);
