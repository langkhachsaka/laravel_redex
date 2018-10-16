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

        const linkUpdate = <Link className="btn btn-info btn-sm square"
                                 to={"/customer-order/" + model.id + "/edit"}
                                 key="link-edit"
        ><i className="ft-edit"/></Link>;

        const btnDelete = <button key="btn-delete" className="btn btn-sm btn-danger square" onClick={() => {
            swal({
                title: "Xoá Đơn hàng",
                text: "Bạn có chắc chắn muốn xoá Đơn hàng này?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        ApiService.delete(Constant.resourcePath(model.id))
                            .then(({data}) => {
                                this.props.setListState(({models}) => ({models: models.filter(m => m.id !== model.id)}));
                                swal(data.message, {icon: "info"});
                            });
                    }
                });
        }}><i className="ft-trash-2"/></button>;

        const btnApprove = <button key="btn-approve" className="btn btn-sm btn-warning square" onClick={() => {
            swal({
                title: "Duyệt đơn hàng",
                text: "Bạn có chắc chắn duyệt đơn hàng này?",
                icon: "warning",
                buttons: true,
                // dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        ApiService.post(Constant.resourcePath( model.id+'/approve'))
                            .then(({data}) => {
                                this.props.setListState(({models}) => {
                                    return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                                });
                                swal(data.message, {icon: "info"});
                            });
                    }
                });
        }}><i className="ft-check-square"/></button>;

        const linkDetail = <Link key="link-detail" to={"/customer-order/" + model.id}
                                 className="btn btn-sm btn-success square">
            <i className="ft-shopping-cart"/>
        </Link>;

        return (
            [
                userPermissions.customer_order.update && linkUpdate,
                ' ',
                model.status == 0 && userPermissions.customer_order.delete && btnDelete,
                ' ',
                model.status == 0 && userPermissions.customer_order.approve ? btnApprove : userPermissions.customer_order.view && model.status != 0 && linkDetail,
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

