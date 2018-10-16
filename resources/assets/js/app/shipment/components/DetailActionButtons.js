import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import Form from "./Form";
import FormDetail from "./FormDetail";
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import swal from "sweetalert";
import CommonConstant from "../../common/meta/constant";
import {Link} from "react-router-dom";

class DetailActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;
        const {shipmentItem} = this.props;

        const btnDelete = <button key="btn-delete" type="button"  className="btn btn-sm btn-danger square" onClick={() => {
            swal({
                title: "Xoá vận đơn",
                text: "Bạn có chắc chắn muốn xoá vận đơn này?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {

                         ApiService.delete(Constant.resourcePath("deleteShipmentItem/"+shipmentItem.id))
                            .then(({data}) => {
                                console.log(data);
                                this.props.setListState(() => {
                                    const shipment = model;
                                    shipment.shipment_item = shipment.shipment_item.filter(m => m.id !== shipmentItem.id);
                                    return {model: shipment};
                                });
                                swal(data.message, {icon: "info"});
                            });
                    }
                });
        }}><i className="ft-trash-2"/></button>;



        return (
            [
                btnDelete,
            ]
        );
    }
}

DetailActionButtons.propTypes = {
    model: PropTypes.object,
    shipmentItem: PropTypes.object,
    setListState: PropTypes.func,
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

export default connect(mapStateToProps, mapDispatchToProps)(DetailActionButtons)

