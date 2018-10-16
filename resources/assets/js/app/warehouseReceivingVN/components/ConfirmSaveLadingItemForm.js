import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import {toastr} from 'react-redux-toastr'
import {Redirect} from "react-router-dom";

class ConfirmSaveLadingItemForm extends Component {

    constructor(props) {
        super(props);
        this.state = {
            model: props.model,
            meta: {pageCount: 0, currentPage: 1},
            isLoading: true,
            redirectToList : false,
        };
    }

    componentDidMount() {

    }



    confirmLading() {
        ApiService.post(Constant.resourcePath('submitData/'+this.state.model.id)) .then(({data}) => {
            toastr.success(data.message);
            this.setState({
                redirectToList : true,
                isLoading: false,
            })
            this.props.actions.closeMainModal();
        });
    }

    render() {
        if(this.state.redirectToList){
            return <Redirect to={"/warehouse-receiving-vn/"}/>;
        }
        const {model} = this.props;
        const listRows = this.state.model.shipment.shipment_item
            .map(model => {
            return ( <tr key={model.warehouse_vn_lading_item.id}>
                <td>{model.lading_codes.map((item,index) =>{
                    return (item.bill_code ? <div key={index}>{index + 1}. {item.bill_code.customer_order.customer.name} </div>
                        : item.bill_of_lading && item.bill_of_lading.customer && <div key={index}>{index + 1}. {item.bill_of_lading.customer.name} </div>  );
                })}</td>
                <td>{model.lading_codes[0].bill_code ? model.lading_codes.map(item =>{
                    return (<div>{item.bill_code.bill_code}</div>);
                }) :"Đơn hàng vận chuyển"}</td>
                <td>{model.bill_of_lading_code}</td>
                <td><small>Cao</small> : {model.warehouse_vn_lading_item.height},<small> dài</small>:{model.warehouse_vn_lading_item.length}, <small>rộng </small>:{model.warehouse_vn_lading_item.width}</td>
                <td>{model.warehouse_vn_lading_item.weight}</td>
                <td>{model.warehouse_vn_lading_item.other_fee && model.warehouse_vn_lading_item.other_fee }</td>
                <td>{model.warehouse_vn_lading_item.pack_name }</td>
                <td>Đã lưu tạm</td>
            </tr> );
        });
        return (
            <div>
                <table className="table table-hover">
                    <thead>
                    <tr>
                        <th>Tên khách hàng</th>
                        <th>Mã đơn hàng</th>
                        <th>Mã vận đơn</th>
                        <th>Kích thước</th>
                        <th>Khối lượng</th>
                        <th>Phụ phí</th>
                        <th>Đóng gói</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    {listRows}
                    </tbody>
                </table>
                <div style={{display : 'inline', float : 'right'}}>
                    <button type="button"
                            className="btn btn-lg btn-primary" onClick={() =>{
                                this.confirmLading();
                    }}>
                        <i className="fa fa-fw fa-check"/>
                        Xác nhận
                    </button>
                </div>
                <h3>Tổng mã vận đơn lô Nhập/Xuất : {this.state.model.shipment.shipment_item.length}/{this.state.model.shipment.shipment_item.length}</h3>
            </div>
    );
    }
}
ConfirmSaveLadingItemForm.propTypes = {

};
function mapStateToProps(state) {
    return {
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps) (reduxForm({
    form: 'ConfirmSaveLadingItemForm ',
})(ConfirmSaveLadingItemForm))
