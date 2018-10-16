import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types';
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr';
import TextArea from "../../../theme/components/TextArea";
import SelectInput from "../../../theme/components/SelectInput";
import {Redirect} from "react-router-dom";


class Form extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedFile: null,
            redirectToDetailId: null,
            customer_id: props.initValues.customer_id || {},
        };
    }

    componentDidMount() {
        const {model} = this.props;

        let initData = this.props.initValues || {};
        if (model) {
            initData = _.assign(initData, model);
        }
        this.props.initialize(initData);
    }

    handleSubmit(formProps) {
        const {model} = this.props;
        const formData = new FormData();

        _.forOwn(formProps, (value, key) => {
            formData.append(key, value);
        });

        if (model) {
            return ApiService.post(Constant.resourcePath(model.id), formData)
                .then(({data}) => {
                    if (this.props.setDetailState) {
                        this.props.setDetailState({model: data.data});
                    } else if (this.props.setListState) {
                        this.props.setListState(({models}) => {
                            return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                        });
                    }

                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        } else {
            return ApiService.post(Constant.resourcePath(), formData)
                .then(({data}) => {
                    this.setState({redirectToDetailId: data.data.id});
                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        }
    }

    render() {
        if (this.state.redirectToDetailId) {
            return <Redirect to={"/transaction/" + this.state.redirectToDetailId}/>;
        }

        const formDisabled = _.get(this.props.userPermissions, 'transaction.form_disabled', {});
        const {model, handleSubmit, submitting, pristine} = this.props;
        const initValues = this.props.initValues || {};

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                {initValues.transactiontable_id &&
                <div className="form-group">
                    {initValues.transactiontable_type === Constant.MORPH_TYPE_ORDER_VN && <h5>Mã đơn hàng VN</h5>}
                    {initValues.transactiontable_type === Constant.MORPH_TYPE_BILL_OF_LADING && <h5>Mã đơn hàng vận chuyển</h5>}
                    <div className="controls">
                        <input className="form-control" value={initValues.transactiontable_id} disabled={true}/>
                    </div>
                </div>}

                <Field name="type" component={SelectInput} label="Loại giao dịch" required={true} validate={[Validator.required]}>
                    <option value="" key={0}>Chọn loại giao dịch</option>
                    <option value="0" key={1}>Đặt cọc</option>
                </Field>

                <Field
                    name="money"
                    component={TextInput}
                    label="Số tiền"
                    required={true}
                    validate={[Validator.required]}
                />

                <Field
                    name="note"
                    component={TextArea}
                    label="Nội dung"
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
    form: 'TransactionForm'
})(Form))