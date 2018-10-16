import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import FormUpdate from "./FormUpdate";
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import swal from "sweetalert";
import {Link} from "react-router-dom";


class ActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;

        const btnUpdate = <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
            this.props.actions.openMainModal(
                <FormUpdate model={model} onUpdateSuccess={(data) => {
                    this.props.setListState(({models}) => ({models: models.map(m => m.id === data.id ? data : m)}));
                }}/>,
                "Sửa thông tin Đơn hàng");
        }}><i className="ft-edit"/></button>;

        const btnDelete = <button key="btn-delete" className="btn btn-sm btn-danger square" onClick={() => {
            swal({
                title: "Xoá thông tin đơn hàng",
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

        const linkDetail = <Link key="link-detail" to={"/china-order/" + model.id}
                                 className="btn btn-sm btn-success square">
            <i className="ft-eye"/>
        </Link>;

        return (
            [
                userPermissions.china_order.update && btnUpdate,
                ' ',
                userPermissions.china_order.delete && btnDelete,
                ' ',
                userPermissions.china_order.view && linkDetail,
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

