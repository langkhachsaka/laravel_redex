import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import UserConstant from "../../user/meta/constant";
import {toastr} from 'react-redux-toastr'
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";
import Validator from "../../../helpers/Validator";


class FormUpdate extends Component {

    constructor(props) {
        super(props);

        this.props.initialize(props.model);
    }

    handleSubmit(formProps) {
        const {model} = this.props;

        return ApiService.post(Constant.resourcePath(model.id), formProps)
            .then(({data}) => {
                toastr.success(data.message);
                this.props.actions.closeMainModal();

                if (this.props.onUpdateSuccess) {
                    this.props.onUpdateSuccess(data.data);
                }
            });
    }

    render() {
        const formDisabled = _.get(this.props.userPermissions, 'china_order.form_disabled', {});
        const {model, handleSubmit, submitting} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <Field
                    name="user_purchasing_id"
                    component={Select2Input}
                    select2Data={model && model.user_purchasing ? [{
                        id: model.user_purchasing.id,
                        text: model.user_purchasing.name
                    }] : []}
                    select2Options={{
                        placeholder: formDisabled.user_purchasing_id ? this.props.authUser.name : '',
                        ajax: {
                            url: AppConfig.API_URL + UserConstant.resourcePath("list?role=" + UserConstant.ROLE_USER_PURCHASING),
                            delay: 250
                        }
                    }}
                    label="Nhân viên đặt hàng"
                    disabled={formDisabled.user_purchasing_id}
                    required={true}
                    validate={[Validator.required]}
                />


                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                        <i className="fa fa-fw fa-check"/>
                        {model ? 'Cập nhật' : 'Thêm mới'}
                    </button>
                </div>

            </form>
        );
    }
}

FormUpdate.propTypes = {
    handleSubmit: PropTypes.func,
    submitting: PropTypes.bool,
    pristine: PropTypes.bool,
    model: PropTypes.object,
    onUpdateSuccess: PropTypes.func,
};

function mapStateToProps({auth}) {
    return {
        userPermissions: auth.permissions,
        authUser: auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'ChinaOrderUpdateForm'
})(FormUpdate))
