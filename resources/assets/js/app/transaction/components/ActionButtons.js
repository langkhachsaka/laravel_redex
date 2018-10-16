import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import swal from "sweetalert";
import {Link} from "react-router-dom";


class ActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;
        let link;
        if (model.type == 4) {
            link = '/transaction/payment-detail/' + model.id;
        }else if(model.type == 2){
            link = '/transaction/recharge/' + model.id;
        }else{
            link = "/transaction/" + model.id;
        }
        const linkDetail = <Link key="link-detail" to={link}
                                 className="btn btn-sm btn-success square">
                            <i className="ft-eye"/>
                            </Link>;

        return (
            [
                userPermissions.transaction.view && linkDetail,
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

