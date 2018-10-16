import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import Form from "./Form";
import Recharge from "./Recharge";
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import swal from "sweetalert";


class ActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;

        const btnRecharge = <button key="btn-recharge" className="btn btn-sm btn-success square" onClick={() => {
            this.props.actions.openMainModal(<Recharge model={model}
                                                   setListState={this.props.setListState}/>, "Nạp tiền cho ví điện tử");
        }}><i className="ft-plus"/></button>;

        const btnUpdate = <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
            this.props.actions.openMainModal(<Form model={model}
                                                   setListState={this.props.setListState}/>, "Sửa thông tin Khách hàng " + model.name);
        }}><i className="ft-edit"/></button>;

        const btnDelete = <button key="btn-delete" className="btn btn-sm btn-danger square" onClick={() => {
            swal({
                title: "Xoá thông tin Khách hàng",
                text: "Bạn có chắc chắn muốn xoá thông tin Khách hàng này?",
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
                btnRecharge,
                ' ',
                userPermissions.customer.update && btnUpdate,
                ' ',
                userPermissions.customer.delete && btnDelete,
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

