import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import Form from "./Form";
import ShipmentDetailForm from "./ShipmentDetailForm";
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import {Link} from "react-router-dom";
import swal from "sweetalert";
import CommonConstant from "../../common/meta/constant";


class ActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;
        const {errorData} = this.props;
        const btnUpdate = <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
            this.props.actions.openMainModal(<Form model={model} errorData={errorData}
                                                   setListState={this.props.setListState}/>, "Sửa thông tin hàng nhập kho");
        }}><i className="ft-edit"/></button>;


        const btnSplit = <Link key="link-split" to={"/warehouse-receiving-vn/" + model.warehouse_receiving_v_n.id}
                                 className="btn btn-sm btn-warning square">
            <i className="ft-scissors"/>
        </Link>;

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
                        ApiService.delete(Constant.resourcePath(model.warehouse_receiving_v_ns_id))
                            .then(({data}) => {
                                this.props.setListState(({models}) => ({models: models.filter(m => m.warehouse_receiving_v_ns_id !== model.warehouse_receiving_v_ns_id)}));
                                swal(data.message, {icon: "info"});
                            });
                    }
                });
        }}><i className="ft-trash-2"/></button>;

        return (
            [

                btnSplit,
                ' ',
                btnDelete,

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

