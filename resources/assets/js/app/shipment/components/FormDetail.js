import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import * as themeActions from "../../../theme/meta/action";
import Formatter from "../../../helpers/Formatter";

class FormDetail extends Component {

    constructor(props) {
        super(props);

        this.state = {
            warehouseCN : null,
            billOfLadingCode : null,
            message : 'Vui lòng nhập mã vận đơn',
            canSubmit : 'disabled',
            model: props.model,
        };
    }

    componentDidMount() {
        const {model} = this.props;
        if (model) {
            this.props.initialize(model);
        }
    }

    render() {
        const {model} = this.props;
        const totalWeight = Formatter.number(model.shipment_item
            .map(item => item.bill_of_lading.weight)
            .reduce((a, b) => a + b, 0));
        const maxLength = Formatter.number(Math.max(...model.shipment_item.map(item => item.bill_of_lading.length)));
        const maxWidth = Formatter.number(Math.max(...model.shipment_item.map(item => item.bill_of_lading.width)));
        const maxHeight = Formatter.number(Math.max(...model.shipment_item.map(item => item.bill_of_lading.height)));
        const listRows = this.state.model.shipment_item.map(item => {
            return (
                <tr key={item.id}>
                    <td>{item.bill_of_lading_code}</td>
                    <td>{item.bill_of_lading && item.bill_of_lading.lading_code
                    && item.bill_of_lading.lading_code.ladingcodetable_type =='Modules\\CustomerOrder\\Models\\CustomerOrderItem' ? 'Đơn hàng Việt Nam' : 'Đơn hàng vận chuyển'}</td>
                    <td>{item.bill_of_lading && item.bill_of_lading.lading_code && item.bill_of_lading.lading_code.ladingcodetable_id}</td>
                    <td>{item.bill_of_lading.weight} (kg)</td>
                    <td>{item.bill_of_lading.height ? item.bill_of_lading.height : 0} (cm)</td>
                    <td>{item.bill_of_lading.width ? item.bill_of_lading.width : 0} (cm)</td>
                    <td>{item.bill_of_lading.length ? item.bill_of_lading.length : 0} (cm)</td>
                    {/*<td className="column-actions">
                        { <DetailActionButtons model={model} shipmentItem = {item} setListState={this.setState.bind(this)}/>}
                    </td>*/}
                </tr>
            );
        });

        return (
            <div>
                Thông tin lô hàng :
                <div className="row">
                    <div className="col-sm-3">
                        <b>Mã lô hàng :</b> {model.shipment_code}
                        <br/><b>Khối lượng :</b> <span className={model.real_weight && model.real_weight < totalWeight ? "red-color" : ""}>{model.real_weight && model.real_weight} </span>
                        <br/><b>Nơi nhận :</b>  {model.warehouse_id && model.warehouse.name}
                    </div>
                    <div className="col-sm-3">
                        <b>Chiều cao :</b> <span className={model.height && model.height < maxHeight ? "red-color" : ""}>{model.height && model.height + ' (cm)'} </span>
                        <br/><b>Chiều rộng :</b> <span className={model.width && model.width < maxWidth ? "red-color" : ""}>{model.width && model.width + ' (cm)'} </span>
                        <br/><b>Chiều dài :</b> <span className={model.length && model.length < maxLength ? "red-color" : ""}>{model.length && model.length + ' (cm)'} </span>
                    </div>
                    <div className="col-sm-3">
                        <b>Số kiện hàng đã nhập :</b> {model.shipment_item && model.shipment_item.length}
                        <br/><b>Hình thức vận chuyển :</b> {model.transport_type && model.transport_type_name}
                        <br/><b>Người tạo :</b> {model.user_creator && model.user_creator.name}
                    </div>
                </div>
                {model.note &&<div className="row">
                    <div className="col-sm-9">
                        <b>Ghi chú </b> :<div dangerouslySetInnerHTML={{__html: model.note}}/>
                </div>
                    
                </div>}
                <div style={{marginTop: '10px'}}>
                    <strong>Danh sách các kiện hàng được đã được nhập vào</strong>
                </div>
                <table className="table table-hover">
                    <thead>
                    <tr>
                        <th>Mã vận đơn</th>
                        <th>Loại đơn hàng</th>
                        <th>Mã</th>
                        <th>Khối lượng</th>
                        <th>Chiều cao</th>
                        <th>Chiều dài</th>
                        <th>Chiều rộng</th>
                        <th> </th>
                    </tr>
                    </thead>
                    <tbody>
                    {listRows}
                    <tr>
                        <td><b>Tổng</b></td>
                        <td></td>
                        <td></td>
                        <td>
                            Khối lượng: <b>{totalWeight} kg</b>
                        </td>
                    </tr>
                    </tbody>

                </table>
            </div>

        );
    }
}

FormDetail.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    model: PropTypes.object,
    setListState: PropTypes.func,
};

function mapStateToProps({auth}) {
    return {
        authUser: auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'WarehouseReceivingCNForm'
})(FormDetail))
