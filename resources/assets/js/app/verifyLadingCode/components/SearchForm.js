import React, {Component} from 'react';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import TextInput from "../../../theme/components/TextInput";
import * as commonActions from "../../common/meta/action";
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import SelectInput from "../../../theme/components/SelectInput";
import Constant from "../meta/constant";
import WarehouseConstant from "../../warehouse/meta/constant";
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";
import UserConstant from "../../user/meta/constant";

class SearchForm extends Component {

    constructor(props) {
        super(props);

        this.state = {
            startDate: null,
            endDate: null,
        };
    }
    componentDidMount() {
        const params = _.get(this.props, 'search.params', {});
        this.props.initialize(params);
    }

    handleFilterSubmit(formProps) {
        this.props.actions.search(formProps);
    }

    render() {
        const {handleSubmit, submitting, reset} = this.props;
        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleFilterSubmit.bind(this))}>
                <div className="row">
                    <div className="col-sm-3">
                        <Field
                            name="warehouse_id"
                            component={Select2Input}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + WarehouseConstant.resourcePath("list?type="+WarehouseConstant.TYPE_VN),
                                    delay: 250
                                }
                            }}
                            label="Kho hàng"
                        />
                    </div>
                    <div className="col-sm-3">
                        <Field name="shipment_code" component={TextInput} label="Mã lô hàng"/>
                    </div>
                    <div className="col-sm-3">
                        <div className="react-datepicker-range-wrapper">
                            <Field
                                name="date_receiving_from"
                                component={DatePickerInput}
                                selectsStart={true}
                                startDate={this.state.startDate}
                                endDate={this.state.endDate}
                                onDateChange={(date) => {
                                    const newState = {startDate: date};
                                    if (this.state.endDate && newState.startDate && newState.startDate.isAfter(this.state.endDate)) {
                                        newState.endDate = date;
                                    }
                                    this.setState(newState);
                                    this.props.change("date_receiving_from", newState.startDate ? newState.startDate.format("YYYY-MM-DD") : null);
                                    if (newState.endDate) {
                                        this.props.change("date_receiving_to", newState.endDate.format("YYYY-MM-DD"));
                                    }
                                }}
                                label="Nhận từ ngày"
                            />
                            <Field
                                name="date_receiving_to"
                                component={DatePickerInput}
                                selectsEnd={true}
                                startDate={this.state.startDate}
                                endDate={this.state.endDate}
                                onDateChange={(date) => {
                                    const newState = {endDate: date};
                                    if (this.state.startDate && newState.endDate && newState.endDate.isBefore(this.state.startDate)) {
                                        newState.startDate = date;
                                    }
                                    this.setState(newState);
                                    this.props.change("date_receiving_to", newState.endDate ? newState.endDate.format("YYYY-MM-DD") : null);
                                    if (newState.startDate) {
                                        this.props.change("date_receiving_from", newState.endDate.format("YYYY-MM-DD"));
                                    }
                                }}
                                label="đến ngày"
                            />
                        </div>
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="user_receive_id"
                            component={Select2Input}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + UserConstant.resourcePath("list"),
                                    delay: 250
                                }
                            }}
                            label="Người nhận"
                        />
                    </div>
                    
                </div>

                <div>
                    <button type="submit" className="btn btn-info" disabled={submitting}>
                        <i className="fa fa-fw fa-search"/> Tìm kiếm
                    </button>
                    {' '}
                    <button type="button" className="btn btn-outline-warning" disabled={submitting} onClick={reset}>
                        <i className="fa fa-fw fa-refresh"/> Reset
                    </button>
                </div>

            </form>
        );
    }
}

SearchForm.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired
};

function mapStateToProps({warehouseReceivingCN}) {
    return {
        search: warehouseReceivingCN.search,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'WarehouseReceivingVNFilterForm',
})(SearchForm))
