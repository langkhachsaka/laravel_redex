import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import Form from "./Form";
import CustomerServiceForm from "./CustomerServiceForm";
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import swal from "sweetalert";


class ActionButtons extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;

        const btnUpdate = <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
            this.props.actions.openMainModal(<Form model={model}
                                                   setListState={this.props.setListState}/>, "Sửa thông tin khiếu nại " + model.name);
        }}><i className="ft-edit"/></button>;

        const btnAdminForm = <button key="btn-admin-form" className="btn btn-sm btn-primary square" onClick={() => {
            this.props.actions.openMainModal(<AdminForm model={model}
                                                   setListState={this.props.setListState}/>, "Thông tin khiếu nại ");
        }}><i className="ft-info"/></button>;

        const btnCustomerServiceForm = <button key="btn-admin-form" className="btn btn-sm btn-danger square" onClick={() => {
            this.props.actions.openMainModal(<CustomerServiceForm model={model}
                                                        setListState={this.props.setListState}/>, "Thông tin khiếu nại ");
        }}><i className="ft-check"/></button>;

        const btnDelete = <button key="btn-delete" className="btn btn-sm btn-danger square" onClick={() => {
            swal({
                title: "Xoá thông tin khiếu nại",
                text: "Bạn có chắc chắn muốn xoá thông tin khiếu nại này?",
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
                model.status == 0  && btnAdminForm,
                model.status == 1  && btnCustomerServiceForm,
                ' ',
                userPermissions.complaint.update && btnUpdate,
                ' ',
                /*userPermissions.complaint.delete && btnDelete,*/
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

