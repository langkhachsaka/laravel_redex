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
import SelectInput from "../../../theme/components/SelectInput";


class Form extends Component {

    componentDidMount() {
        const {model} = this.props;

        if (model) {
            this.props.initialize(model);
        }
    }

    handleSubmit(formProps) {
        const {model} = this.props;

        if (model) {
            return ApiService.post(Constant.resourcePath(model.id), formProps)
                .then(({data}) => {
                    this.props.setListState(({models}) => {
                        return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                    });
                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        } else {
            return ApiService.post(Constant.resourcePath(), formProps)
                .then(({data}) => {
                    this.props.setListState(({models}) => {
                        models.unshift(data.data);
                        return {models: models};
                    });
                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        }
    }

    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;

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
                    name="type"
                    component={SelectInput}
                    label="Loại"
                    required={true}
                    validate={[Validator.required]}
                >
                    <option value="" key={0}>-</option>
                    {Constant.TYPES.map(type => <option key={type.id} value={type.id}>{type.text}</option>)}
                </Field>

                <Field
                    name="address"
                    component={TextInput}
                    label="Địa chỉ"
                    required={true}
                    validate={[Validator.required]}
                />


                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting || pristine}>
                        <i className="fa fa-fw fa-check"/>
                        {model ? 'Cập nhật' : 'Thêm mới'}
                    </button>
                </div>

            </form>
        );
    }
}

Form.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setListState: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'ShopForm'
})(Form))
