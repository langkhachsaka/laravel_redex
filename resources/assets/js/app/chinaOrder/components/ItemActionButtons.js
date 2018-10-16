import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import swal from "sweetalert";
import FormItemUpdate from "./FormItemUpdate";


class ItemActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;

        const btnUpdate = <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
            this.props.actions.openMainModal(<FormItemUpdate model={model}
                                                             setDetailState={this.props.setDetailState}/>, "Sửa thông tin sản phẩm");
        }}><i className="ft-edit"/></button>;

        const btnDelete = <button key="btn-delete" className="btn btn-sm btn-danger square" onClick={() => {
            swal({
                title: "Xoá sản phẩm",
                text: "Bạn có chắc chắn muốn xoá sản phẩm này?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        ApiService.delete(Constant.resourceItemPath(model.id))
                            .then(({data}) => {
                                this.props.setDetailState(prevState => {
                                    const order = prevState.model;
                                    order.china_order_items = order.china_order_items.filter(item => item.id !== model.id);
                                    return {model: order};
                                });
                                swal(data.message, {icon: "info"});
                            });
                    }
                });
        }}><i className="ft-trash-2"/></button>;

        return (
            [
                userPermissions.china_order_item.update && btnUpdate,
                ' ',
                userPermissions.china_order_item.delete && btnDelete,
            ]
        );
    }
}

ItemActionButtons.propTypes = {
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

export default connect(mapStateToProps, mapDispatchToProps)(ItemActionButtons)

