import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, formValueSelector, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import SelectInput from "../../../theme/components/SelectInput";
import PasswordInput from "../../../theme/components/PasswordInput";
import Select2Input from "../../../theme/components/Select2Input";
import WarehouseConstant from "../../warehouse/meta/constant";
import AppConfig from "../../../config";
import Converter from "../../../helpers/Converter";


class Form extends Component {

    componentDidMount() {
        const {model} = this.props;
        if(model){
            if( model.user_roles.length == 0) {
                let items = [];
                for (let i = 0; i < 2; i++) {
                    items.push({});
                }
                this.props.initialize({
                    roles: items,
                    ...model
                });
            }else {
                console.log(model);
                let items = [];
                model.user_roles.map(item => {
                    items.push(item);
                });
                this.props.initialize({
                    roles: items,
                    ...model
                });
            }
        }else {
            let items = [];
            for (let i = 0; i < 2; i++) {
                items.push({});
            }
            this.props.initialize({
                roles: items,
            });
        }
    }

    handleSubmit(formProps) {
        const {model} = this.props;
        let isDistinct = true;
        for(let i = 0; i < formProps.roles.length -1; i ++ ){
            for(let j = i + 1; j < formProps.roles.length; j ++ ){
                if(formProps.roles[i].role === formProps.roles[j].role){
                    isDistinct = false;
                }
            }
        }
        if(!isDistinct){
            toastr.warning('Vai trò trùng lặp');
        } else {
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

    }



    render() {

        const renderRoles = ({fields, meta: {error, submitFailed}}) => (
            <div>
                    {submitFailed && error && <span>{error}</span>}
                {fields.map((item, index) => (
                    <div key={index}>
                        <div className={"row"}>
                            <div className={"col-sm-10"}>
                                <Field
                                name={`${item}.role`}
                                component={SelectInput}
                                label={"Vai trò " + (index + 1)}
                                required={true}
                                validate={[Validator.required]}
                                onChange={() => {
                                    this.props.change("warehouse_id", null);
                                }}
                            >
                                <option value="" key={0}>-</option>
                                {Constant.ROLES.map(role => <option key={role.id} value={role.id}>{role.text}</option>)}
                            </Field>
                            </div>
                            <div className={"col-sm-2"} style={{top: "30px"}}>
                                <button  disabled={fields.length < 2}
                                    className="btn btn-sm btn-danger square"
                                    type="button"
                                    title="Xóa"
                                    onClick={() => fields.remove(index)}
                                ><i className="ft-trash-2"/></button>
                            </div>
                        </div>
                    </div>
                ))}
                    <button  style={{width: '100px'}}type="button" className="btn btn-primary btn-sm btn-block" onClick={() => fields.push({})}>Thêm vai trò</button>
            </div>
        )

        const {model, handleSubmit, submitting, pristine} = this.props;
        const valueRole = Converter.str2int(this.props.valueRole);

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <Field
                    name="name"
                    component={TextInput}
                    label="Tên"
                    required={true}
                    validate={[Validator.required, Validator.noSpecialCharacter]}
                />

                <Field
                    name="username"
                    component={TextInput}
                    label="Tên tài khoản"
                    required={true}
                    validate={[Validator.required, Validator.username]}
                />
                {!model && <div>
                    <Field
                        name="password"
                        component={PasswordInput}
                        label="Mật khẩu"
                        required={true}
                        validate={[Validator.required]}
                    />
                    <Field
                        name="password_confirmation"
                        component={PasswordInput}
                        label="Xác nhận mật khẩu"
                        required={true}
                        validate={[Validator.required]}
                    />
                </div>}

                <Field
                    name="email"
                    component={TextInput}
                    label="Email"
                    required={true}
                    validate={[Validator.required, Validator.email]}
                />

                <Field
                    name="phone"
                    component={TextInput}
                    label="Số điện thoại"
                    validate={[Validator.phoneNumber]}
                />
                <fieldset className="fiedset-verify-customer-order">
                    <legend  className="legend-account-deposited">Thêm vài trò cho người dùng</legend>

                    <FieldArray name="roles" component={renderRoles} />

                </fieldset>
                {(valueRole === Constant.ROLE_USER_WAREHOUSE_VN || valueRole === Constant.ROLE_USER_WAREHOUSE_TQ) &&
                <Field
                    name="warehouse_id"
                    component={Select2Input}
                    select2Data={model && model.warehouse ? [{id: model.warehouse.id, text: model.warehouse.name}] : []}
                    select2Options={{
                        ajax: {
                            url: AppConfig.API_URL + WarehouseConstant.resourcePath("list?type=" + (valueRole === Constant.ROLE_USER_WAREHOUSE_VN ? WarehouseConstant.TYPE_VN : WarehouseConstant.TYPE_TQ)),
                            delay: 250
                        }
                    }}
                    label="Kho hàng"
                    required={true}
                    validate={[Validator.required]}
                />}

                {model && <div>
                    <h4>Đổi mật khẩu</h4>
                    <Field
                        name="password"
                        component={PasswordInput}
                        label="Mật khẩu mới"
                    />
                    <Field
                        name="password_confirmation"
                        component={PasswordInput}
                        label="Xác nhận mật khẩu mới"
                    />
                </div>}


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

const selector = formValueSelector('UserForm');

function mapStateToProps(state) {
    const valueRole = selector(state, 'role');
    return {
        valueRole
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'UserForm'
})(Form))


// Form = reduxForm({
//     form: 'UserForm'
// })(Form);
//
// Form = connect(
//     state => {
//         const valueRole = selector(state, 'role');
//         return {
//             valueRole
//         }
//     }
// )(Form);
//
// export default Form
