import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import axios from "axios";

import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import CustomerAddressConstant from "../../customerAddress/meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";


class FormCreateCustomerAddress extends Component {

    constructor(props) {
        super(props);

        this.state = {
            provinces: [],
            districts: [],
            wards: [],
        };

        this.props.initialize({customer_id: props.customer.id});
    }

    componentDidMount() {
        axios.get("get-tinh-thanh-pho", {baseURL: AppConfig.ROOT_URL})
            .then(response => {
                this.setState({provinces: response.data.data})
            })
    }


    handleSubmit(formProps) {
        return ApiService.post(CustomerAddressConstant.resourcePath(), formProps)
            .then(({data}) => {
                this.props.onSave(data.data);
                toastr.success(data.message);
                this.props.actions.closeMainModal();
            });
    }

    render() {
        const {handleSubmit, submitting} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <Field
                    name="name"
                    component={TextInput}
                    label="Tên"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="provincial_id"
                    component={Select2Input}
                    select2Data={this.state.provinces.map(province => ({id: province.matp, text: province.name}))}
                    label="Tỉnh/Thành phố"
                    required={true}
                    validate={[Validator.required]}
                    select2OnSelect={(e) => {
                        this.setState({
                            districts: [],
                            wards: [],
                        });
                        this.props.change("district_id", "");
                        this.props.change("ward_id", "");
                        axios.get("get-quan-huyen", {baseURL: AppConfig.ROOT_URL, params: {matp: e.target.value}})
                            .then(response => {
                                this.setState({districts: response.data.data})
                            })
                    }}
                />

                <Field
                    name="district_id"
                    component={Select2Input}
                    select2Data={this.state.districts.map(district => ({id: district.maqh, text: district.name}))}
                    label="Quận/Huyện"
                    required={true}
                    validate={[Validator.required]}
                    select2OnSelect={(e) => {
                        this.setState({
                            wards: [],
                        });
                        this.props.change("ward_id", "");
                        axios.get("get-phuong-xa", {baseURL: AppConfig.ROOT_URL, params: {maqh: e.target.value}})
                            .then(response => {
                                this.setState({wards: response.data.data})
                            })
                    }}
                />

                <Field
                    name="ward_id"
                    component={Select2Input}
                    select2Data={this.state.wards.map(ward => ({id: ward.xaid, text: ward.name}))}
                    label="Phường/Xã"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="address"
                    component={TextInput}
                    label="Địa chỉ"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="phone"
                    component={TextInput}
                    label="Điện thoại"
                    required={true}
                    validate={[Validator.required]}
                />


                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                        <i className="fa fa-fw fa-check"/> Thêm mới
                    </button>
                </div>

            </form>
        );
    }
}

FormCreateCustomerAddress.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    customer: PropTypes.object,
    onSave: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'FormCreateCustomerAddress'
})(FormCreateCustomerAddress))
