import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import Form from "./Form";
import CreateShipmentForm from "./CreateShipmentForm";
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import swal from "sweetalert";
import CommonConstant from "../../common/meta/constant";


class ActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;

        const btnUpdate = <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
            this.props.actions.openMainModal(<Form model={model}
                                                   setListState={this.props.setListState}/>, "Sửa thông tin hàng nhập kho");
        }}><i className="ft-edit"/></button>;

        const btnAddToShipment = <button key="btn-add-to-shipment" className="btn btn-sm btn-info square" onClick={() => {
            this.props.actions.openMainModal(<CreateShipmentForm model={model}
                                                   setListState={this.props.setListState}/>, "Thêm vào lô hàng");
        }}><i className="ft-package"/></button>;

        const btnDelete = <button key="btn-delete" className="btn btn-sm btn-danger square" onClick={() => {
            swal({
                title: "Xoá thông tin hàng nhập kho",
                text: "Bạn có chắc chắn muốn xoá hàng nhập kho này?",
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

        return (
            [
                userPermissions.warehouse_receiving_cn.update ? btnUpdate: '',
                ' ',
                !model.shipment_item && userPermissions.warehouse_receiving_cn.delete ? btnDelete : '',
                ' ',
                /*model.shipment_item || model.status == 2 ? '' : btnAddToShipment,*/
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

