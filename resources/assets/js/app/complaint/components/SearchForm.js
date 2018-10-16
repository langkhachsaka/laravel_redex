import React, {Component} from 'react';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import * as commonActions from "../../common/meta/action";
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import CustomerConstant from "../../customer/meta/constant";
import Select2Input from "../../../theme/components/Select2Input";
import AppConfig from "../../../config";
import UserConstant from "../../user/meta/constant";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import SelectInput from "../../../theme/components/SelectInput";
import Constant from "../meta/constant";


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
        const searchDisabled = _.get(this.props.userPermissions, 'complaint.search_disabled', {});
        const {handleSubmit, submitting, reset} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleFilterSubmit.bind(this))}>

                <div className="row">
                    <div className="col-sm-3">
                        <Field
                            name="customer_id"
                            component={Select2Input}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + CustomerConstant.resourcePath("list"),
                                    delay: 250
                                }
                            }}
                            label="Khách hàng"
                        />
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="user_id"
                            component={Select2Input}
                            select2Options={{
                                placeholder: searchDisabled.user_id ? this.props.authUser.name : 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + UserConstant.resourcePath("list"),
                                    delay: 250
                                }
                            }}
                            label="Nhân viên xử lý"
                            disabled={searchDisabled.user_id}
                        />
                    </div>
                    <div className="col-sm-3">
                        <div className="react-datepicker-range-wrapper">
                            <Field
                                name="created_at_from"
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
                                    this.props.change("created_at_from", newState.startDate ? newState.startDate.format("YYYY-MM-DD 00:00:00") : null);
                                    if (newState.endDate) {
                                        this.props.change("created_at_to", newState.endDate.format("YYYY-MM-DD 23:59:59"));
                                    }
                                }}
                                label="Tạo từ ngày"
                            />
                            <Field
                                name="created_at_to"
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
                                    this.props.change("created_at_to", newState.endDate ? newState.endDate.format("YYYY-MM-DD 23:59:59") : null);
                                    if (newState.startDate) {
                                        this.props.change("created_at_from", newState.endDate.format("YYYY-MM-DD 00:00:00"));
                                    }
                                }}
                                label="đến ngày"
                            />
                        </div>
                    </div>
                    <div className="col-sm-3">
                        <Field name="status" component={SelectInput} label="Trạng thái">
                            <option value="" key={0}>Tất cả</option>
                            {Constant.COMPLAINT_STATUSES.map(stt =>
                                <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                        </Field>
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

function mapStateToProps(state) {
    return {
        search: state.complaint.search,
        userPermissions: state.auth.permissions,
        authUser: state.auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'ComplaintFilterForm',
})(SearchForm))
