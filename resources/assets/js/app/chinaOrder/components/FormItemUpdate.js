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
import Validator from "../../../helpers/Validator";
import TextInput from "../../../theme/components/TextInput";


class FormItemUpdate extends Component {

    constructor(props) {
        super(props);

        this.props.initialize(props.model);
    }

    handleSubmit(formProps) {
        const {model} = this.props;

        return ApiService.post(Constant.resourceItemPath(model.id), formProps)
            .then(({data}) => {
                this.props.setDetailState(({model}) => {
                    model.china_order_items = model.china_order_items.map(item => item.id === data.data.id ? data.data : item);
                    return {model: model};
                });
                toastr.success(data.message);
                this.props.actions.closeMainModal();
            });
    }

    render() {
        const {handleSubmit, submitting} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <Field
                    name="quantity"
                    component={TextInput}
                    label="Số lượng"
                    required={true}
                    validate={[Validator.required, Validator.requireInt]}
                />

                <Field
                    name="price_cny"
                    component={TextInput}
                    label="Giá"
                    required={true}
                    validate={[Validator.required, Validator.requireFloat, Validator.greaterThan0]}
                />

                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                        <i className="fa fa-fw fa-check"/> Cập nhật
                    </button>
                </div>

            </form>
        );
    }
}

FormItemUpdate.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setDetailState: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'ChinaOrderItemUpdateForm'
})(FormItemUpdate))
