import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import BillOfLadingForm from "./BillOfLadingForm";
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import swal from "sweetalert";
import CustomerOrderForm from "./CustomerOrderForm";
import ManyCustomerOrderDetailForm from "./ManyCustomerOrderDetailForm";

class ActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;
        const btnDetail1 = <button key="btn-detail" className="btn btn-sm btn-success square" onClick={() => {
                this.props.actions.openMainModal(<CustomerOrderForm model={model} />, "Thông tin chi tiết mã vận đơn đã kiểm");
        }}><i className="ft-eye"/></button>;

        const btnDetail2 = <button key="btn-detail" className="btn btn-sm btn-success square" onClick={() => {
            this.props.actions.openMainModal(<ManyCustomerOrderDetailForm model={model} />, "Thông tin chi tiết mã vận đơn đã kiểm");
        }}><i className="ft-eye"/></button>;

        const btnDelete = <button key="btn-delete" className="btn btn-sm btn-danger square" onClick={() => {
            swal({
                title: "Xoá lịch sử kiểm hàng",
                text: "Bạn có chắc chắn muốn xoá lịch sử kiểm hàng này?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        ApiService.delete(Constant.resourcePath(model.id))
                            .then(({data}) => {
                            if(data.data.type == 1){
                                this.props.setListState(({models}) => ({models: models.filter(m => m.id !== model.id)}));
                            } else {
                                this.props.setListState(({models}) => {
                                    models: models.unshift(data.data.addItem);
                                });
                                data.data.deleteItem.map(item =>{
                                    this.props.setListState(({models}) => ({models: models.filter(m => m.id !== item.id)}));
                                });
                            }
                                swal(data.message, {icon: "info"});

                            });
                    }
                });
        }}><i className="ft-trash-2"/></button>;



        return (
            [

                model.status != 2 ? model.type == 1 ? btnDetail1 : btnDetail2 : '',
                ' ',
                model.status != 2 && btnDelete,
            ]
        );
    }
}

ActionButtons.propTypes = {
    model: PropTypes.object,
    errorData: PropTypes.object,
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

