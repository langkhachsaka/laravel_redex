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
import {toastr} from 'react-redux-toastr'
import PasswordInput from "../../../theme/components/PasswordInput";
import axios from "axios";
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";


class Recharge extends Component {

    constructor(props) {
        super(props);
    }

    componentDidMount() {

    }

    handleSubmit(formProps) {
        const {model} = this.props;

        return ApiService.post(Constant.rechargePath(model.id), formProps)
            .then(({data}) => {
                this.props.setListState(({models}) => {
                    return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                });
                toastr.success(data.message);

                this.props.actions.closeMainModal();
            });
    }

    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <Field
                    name="amount"
                    component={TextInput}
                    label="Số tiền"
                    validate={[Validator.required, Validator.number, Validator.greaterThan0]}
                />

                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting || pristine}>
                        <i className="fa fa-fw fa-check"/>
                        Nạp tiền
                    </button>
                </div>

            </form>
        );
    }
}

Recharge.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
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

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'CustomerForm'
})(Recharge))
