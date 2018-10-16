import React, {Component} from 'react';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import TextInput from "../../../theme/components/TextInput";
import * as commonActions from "../../common/meta/action";
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import SelectInput from "../../../theme/components/SelectInput";
import Validator from "../../../helpers/Validator";
import ArrayHepper from "../../../helpers/Array";
import Select2Input from "../../../theme/components/Select2Input";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import Constant from "../meta/constant";
import UserConstant from "../../user/meta/constant";
import AppConfig from "../../../config";

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
                <div className="col-sm-2">
                    <Field name="order_id" component={TextInput} label="Mã đơn hàng"/>
                </div>  
                <div className="col-sm-4">
                        <div className="react-datepicker-range-wrapper">
                            <Field
                                name="start_date_from"
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
                                    this.props.change("start_date_from", newState.startDate ? newState.startDate.format("YYYY-MM-DD") : null);
                                    if (newState.endDate) {
                                        this.props.change("start_date_to", newState.endDate.format("YYYY-MM-DD"));
                                    }
                                }}
                                label="Bắt đầu từ ngày"
                            />
                            <Field
                                name="start_date_to"
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
                                    this.props.change("start_date_to", newState.endDate ? newState.endDate.format("YYYY-MM-DD") : null);
                                    if (newState.startDate) {
                                        this.props.change("start_date_from", newState.endDate.format("YYYY-MM-DD"));
                                    }
                                }}
                                label="đến ngày"
                            />
                        </div>
                    </div>
                    <div className="col-sm-4">
                        <div className="react-datepicker-range-wrapper">
                            <Field
                                name="end_date_from"
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
                                    this.props.change("end_date_from", newState.startDate ? newState.startDate.format("YYYY-MM-DD") : null);
                                    if (newState.endDate) {
                                        this.props.change("end_date_to", newState.endDate.format("YYYY-MM-DD"));
                                    }
                                }}
                                label="Kết thúc từ ngày"
                            />
                            <Field
                                name="end_date_to"
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
                                    this.props.change("end_date_to", newState.endDate ? newState.endDate.format("YYYY-MM-DD") : null);
                                    if (newState.startDate) {
                                        this.props.change("end_date_from", newState.endDate.format("YYYY-MM-DD"));
                                    }
                                }}
                                label="đến ngày"
                            />
                        </div>

                    </div>
                    
                    <div className="col-sm-2">
                        <Field name="status" component={SelectInput} label="Trạng thái">
                            <option value="" key={0}>Tất cả</option>
                            {Constant.TASK_STATUSES.map(stt => <option  hidden={ArrayHepper.twoArrayHasSameElement(stt.roleDisplay,this.props.authUser.roles) ? stt.hidden : "" } value={stt.id} key={stt.id} >{stt.text}</option>)}
                        </Field>
                    </div>
                </div>
                <div className="row">
                    <div className="col-sm-3">
                        <Field name="title" component={TextInput} label="Tiêu đề"/>
                    </div>
                    <div className="col-sm-3">
                        <Field name="description" component={TextInput} label="Mô tả"/>
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="creator_id"
                            component={Select2Input}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + Constant.resourcePath("listUser"),
                                    delay: 250
                                }
                            }}
                            label="Người tạo"
                        />
                        
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="performer_id"
                            component={Select2Input}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + Constant.resourcePath("listUser"),
                                    delay: 250
                                }
                            }}
                            label="Người thực hiện"
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

function mapStateToProps({warehouse, auth} ) {
    return {
        search: warehouse.search,
        authUser : auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'WarehouseFilterForm',
})(SearchForm))
