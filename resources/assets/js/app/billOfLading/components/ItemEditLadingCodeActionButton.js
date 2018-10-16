import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import FormCreateLadingCode from "./FormCreateLadingCode";


class ItemLadingCodeActionButton extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;

        const btnUpdateLadingCode = <a key="btn-update-lading-code" className="" onClick={(e) => {
            e.preventDefault();
            this.props.actions.openMainModal(<FormCreateLadingCode model={model} action={'edit'}
                                                                   setDetailState={this.props.setDetailState}/>, "Sửa mã vận đơn");
        }}><i className="ft-edit"/></a>;

        return (
            [
                userPermissions.customer_order_item.delete && btnUpdateLadingCode
            ]
        );
    }
}

ItemLadingCodeActionButton.propTypes = {
    model: PropTypes.object,
    setDetailState: PropTypes.func
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

export default connect(mapStateToProps, mapDispatchToProps)(ItemLadingCodeActionButton)

