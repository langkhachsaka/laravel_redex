import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import swal from "sweetalert";
import ConstantLadingCode from "../../ladingCode/meta/constant";


class ItemLadingCodeActionButton extends Component {
    render() {
        const {userPermissions} = this.props;
        const {model} = this.props;

        const btnDelete = <a key="btn-delete" onClick={(e) => {
            e.preventDefault();
            swal({
                title: "Xoá mã vận đơn",
                text: "Bạn có chắc chắn muốn xoá mã vận đơn này?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        ApiService.delete(ConstantLadingCode.resourcePath(model.id))
                            .then(({data}) => {
                                console.log(data);
                                this.props.setDetailState(prevState => {
                                    return {model: data.data};
                                });
                                swal(data.message, {icon: "info"});
                            });

                    }
                });
        }}><i className="ft-trash-2"/></a>;

        return (
            [
                userPermissions.customer_order_item.delete && btnDelete
            ]
        );
    }
}

ItemLadingCodeActionButton.propTypes = {
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

export default connect(mapStateToProps, mapDispatchToProps)(ItemLadingCodeActionButton)

