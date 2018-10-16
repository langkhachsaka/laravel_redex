import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import Form from "./Form";
import {Link} from "react-router-dom";

class ActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;

        const btnUpdate = <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
            this.props.actions.openMainModal(<Form model={model}
                                                   setListState={this.props.setListState}/>, "Xác nhận giao hàng");
        }}><i className="ft-edit"/></button>;

        const btnView = <Link className="btn btn-success btn-sm square"
                                 to={"/delivery/" + model.id}
                                 key="link-detail"
        ><i className="ft-eye"/></Link>;

        return (
            [
                model.delivery && model.delivery.status ==0 && userPermissions.delivery.update && btnUpdate,
                model.delivery && model.delivery.status == 1 && userPermissions.delivery.view && btnView
            ]
        );
    }
}

ActionButtons.propTypes = {
    model: PropTypes.object,
    setListState: PropTypes.func
};

function mapStateToProps({auth}) {
    return {
        userPermissions: auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(ActionButtons)

