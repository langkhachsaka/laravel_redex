import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";

class ShipmentDetailForm extends Component {

    constructor(props) {
        super(props);
        this.state = {
            model: props.model,
            meta: {pageCount: 0, currentPage: 1},
            isLoading: true
        };
    }

    componentDidMount() {
        const {model} = this.props;
        this.props.change("sys_length", model.shipment.length);
        this.props.change("sys_height", model.shipment.height);
        this.props.change("sys_width", model.shipment.width);
        this.props.change("sys_weight", model.shipment.real_weight);
        this.props.change("real_length", model.shipment.length);
        this.props.change("real_height", model.shipment.height);
        this.props.change("real_width", model.shipment.width);
        this.props.change("real_weight", model.shipment.real_weight);
        this.props.actions.changeThemeTitle("Chi tiết lô hàng");
    }



    handleSubmit(formProps) {}


    render() {
        const {userPermissions} = this.props;
        const {handleSubmit, submitting, reset} = this.props;
        const defaultPageSize = this.props.search.meta.per_page;
        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                Mã lô hàng : <b>{this.state.model.id} </b>
                <div className="row">
                    <div className="col-sm-6">
                        Thông tin lô hàng tự hệ thông :
                        <div className="row">

                            <div className="col-sm-6">
                                <fieldset className="account-deposited">
                                    <legend  className="legend-account-deposited">Kích thước :   </legend>
                                    <div className="row">

                                        <div className="col-sm-5">
                                            &nbsp;
                                        </div>
                                        <div className="col-sm-7">
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    Dài
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field
                                                        name="sys_length"
                                                        component={TextInput}
                                                        disabled = {true}
                                                        required={true}
                                                        validate={[Validator.required]}
                                                    />
                                                </div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    Rộng
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field
                                                        name="sys_width"
                                                        component={TextInput}
                                                        disabled = {true}
                                                        required={true}
                                                        validate={[Validator.required]}
                                                    />
                                                </div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    Cao
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field
                                                        name="sys_height"
                                                        component={TextInput}
                                                        disabled = {true}
                                                        required={true}
                                                        validate={[Validator.required]}
                                                    />
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </fieldset>
                                <fieldset className="account-deposited">
                                    <legend  className="legend-account-deposited"></legend>
                                    <div className="row">

                                        <div className="col-sm-6">
                                            Cân nặng
                                        </div>
                                        <div className="col-sm-2">
                                        </div>
                                        <div className="col-sm-3" style={{marginLeft:'14px'}}>
                                            <Field
                                                name="sys_weight"
                                                component={TextInput}
                                                disabled = {true}
                                                required={true}
                                                validate={[Validator.required]}
                                            />
                                        </div>

                                    </div>
                                </fieldset>
                            </div>
                            <div className="col-sm-5">
                                <fieldset className="account-deposited">
                                    <legend  className="legend-account-deposited">Danh sách mã vận đơn :   </legend>


                                    {this.state.model.shipment.shipment_item &&
                                    this.state.model.shipment.shipment_item.map((item, index) => {
                                        return (
                                            <div  key={item.id} className="row">
                                                <div className="col-sm-2">
                                                    {index + 1}
                                                </div>
                                                <div className="col-sm-10">
                                                    <strong>{item.bill_of_lading_code}</strong>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div className="col-sm-6 vl">
                        Thông tin thực kiểm :
                        <div className="row">
                            <div className="col-sm-6">
                                <fieldset className="account-deposited">
                                    <legend  className="legend-account-deposited">Danh sách mã vận đơn :   </legend>


                                    {this.state.model.shipment.shipment_item &&
                                    this.state.model.shipment.shipment_item.map((item, index) => {
                                        return (
                                            <div  key={item.id} className="row">
                                                <div className="col-sm-2">
                                                    {index + 1}
                                                </div>
                                                <div className="col-sm-10">
                                                    <strong>{item.bill_of_lading_code}</strong>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </fieldset>
                            </div>
                            <div className="col-sm-6">
                                <fieldset className="account-deposited">
                                    <legend  className="legend-account-deposited">Kích thước :   </legend>
                                    <div className="row">

                                        <div className="col-sm-5">
                                            &nbsp;
                                        </div>
                                        <div className="col-sm-7">
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    Dài
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field
                                                        name="real_length"
                                                        component={TextInput}
                                                        required={true}
                                                        validate={[Validator.required]}
                                                    />
                                                </div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    Rộng
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field
                                                        name="real_width"
                                                        component={TextInput}
                                                        required={true}
                                                        validate={[Validator.required]}
                                                    />
                                                </div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    Cao
                                                </div>
                                                <div className="col-sm-6">
                                                    <Field
                                                        name="real_height"
                                                        component={TextInput}
                                                        required={true}
                                                        validate={[Validator.required]}
                                                    />
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </fieldset>
                                <fieldset className="account-deposited">
                                    <legend  className="legend-account-deposited"></legend>
                                    <div className="row">

                                        <div className="col-sm-6">
                                            Cân nặng
                                        </div>
                                        <div className="col-sm-2">
                                        </div>
                                        <div className="col-sm-3" style={{marginLeft:'14px'}}>
                                            <Field
                                                name="real_weight"
                                                component={TextInput}
                                                required={true}
                                                validate={[Validator.required]}
                                            />
                                        </div>

                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="hl"></div>
                <div className="row">
                    <div className="col-sm-6">
                        <div className="row">
                            <div className="col-sm-2">
                            </div>
                            <div className="col-sm-8">
                                <center>DANH SÁCH MÃ VẬN ĐƠN CÙNG MỘT KH </center>
                                <table className="table table-hover">
                                    <thead>
                                        <tr className="table table-hover">
                                            <th> Mã khách hàng</th>
                                            <th> Mã vận đơn</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div className="col-sm-2">
                            </div>
                        </div>

                    </div>
                    <div className="col-sm-6">
                        <div className="row">
                            <div className="col-sm-2">
                            </div>
                            <div className="col-sm-8">
                                <center>DANH SÁCH MÃ VẬN ĐƠN CÓ TỪ 2 MÃ KH </center>
                                <table className="table table-hover">
                                    <thead>
                                        <tr className="table table-hover">
                                            <th> Mã khách hàng</th>
                                            <th> Mã vận đơn</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div className="col-sm-2">
                            </div>
                        </div>

                    </div>
                </div>
                <div style={{display : 'inline', float : 'right'}}>
                    <button type="submit" name="submit" value="submit" className="btn btn-lg btn-warning" >
                        <i className="fa fa-fw fa-check"/>
                        Cập nhật
                    </button>
                </div>
            </form>
    );
    }
}
ShipmentDetailForm.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired
};
function mapStateToProps(state) {
    return {
        search: state.warehouseReceivingCN.search,
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps) (reduxForm({
    form: 'ShipmentSplitForm',
})(ShipmentDetailForm))
