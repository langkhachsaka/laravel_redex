import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import Form from "./Form";
import Constant from "../meta/constant";
import {Link} from "react-router-dom";

class ActionButtons extends Component {
    render() {
        const {model} = this.props;


        var displayUpdateButton = true;
        if (model.status == Constant.ORDER_DELETED_STATUS) {
            displayUpdateButton = false;
        }
        

        const btnUpdate = <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
            this.props.actions.openMainModal(<Form model={model}
                                                   setListState={this.props.setListState}/>, "Sửa thông tin công việc");
        }}><i className="ft-edit"/></button>;

        
        const linkDetail = <Link key="link-detail" to={"/task/" + model.id}
                                 className="btn btn-sm btn-success square">
            <i className="ft-eye"/>
        </Link>;

        return (
            [
                displayUpdateButton&&btnUpdate,
                ' ',
                linkDetail
            ]
        );
    }
}

ActionButtons.propTypes = {
    model: PropTypes.object,
    setListState: PropTypes.func
};


function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(ActionButtons)

