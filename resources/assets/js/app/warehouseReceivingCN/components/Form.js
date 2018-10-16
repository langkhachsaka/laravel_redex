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
import Select2Input from "../../../theme/components/Select2Input";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import SelectInput from "../../../theme/components/SelectInput";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import WarehouseConstant from "../../warehouse/meta/constant";
import AppConfig from "../../../config";
import UserConstant from "../../user/meta/constant";
import moment from "moment";

class Form extends Component {

    componentDidMount() {
        const {model} = this.props;
        if (model) {
            this.props.initialize(model);
        } else {
            this.props.initialize({
                user_receive_id: this.props.authUser.id,
                date_receiving : moment().format("YYYY-MM-DD")
            });
        }
    }

    handleSubmit(formProps) {
        const {model} = this.props;
        if (model) {
            return ApiService.post(Constant.resourcePath(model.id), formProps)
                .then(({data}) => {
                    if(data.message =="error") {
                        toastr.warning('Mã vận đơn đã được nhập vào kho TQ. Vui lòng kiểm tra lại');
                    } else {
                        this.props.setListState(({models}) => {
                            return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                        });
                        toastr.success(data.message);
                        this.props.actions.closeMainModal();
                    }
                });
        } else {
            return ApiService.post(Constant.resourcePath(), formProps)
                .then(({data}) => {
                    if(data.message =="error") {
                        toastr.warning('Mã vận đơn đã được nhập vào kho TQ. Vui lòng kiểm tra lại');
                    } else {
                        this.props.setListState(({models}) => {
                            models.unshift(data.data);
                            return {models: models};
                        });
                        toastr.success(data.message);
                        this.props.actions.closeMainModal();
                    }
                });
        }
    }

    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <div className="row">
                    
                    <div className="col-sm-4">
                        <Field
                            name="bill_of_lading_code"
                            component={TextInput}
                            label="Mã vận đơn"
                            onChange = {e => {
                                const id = e.target.value.trim();
                                if(id){
                                    ApiService.get(Constant.resourceLadingCodePath("get-order/"+id)).then(({data : {data}}) => {
                                        /*this.props.change("status", data.results);*/
                                        if(data){
                                            this.props.change("status", Constant.STATUS_MATCH);
                                            this.props.change("height", data.height);
                                            this.props.change("length", data.length);
                                            this.props.change("weight", data.weight);
                                            this.props.change("width", data.width);
                                        } else {
                                            this.props.change("status", Constant.STATUS_UNMATCH);
                                            this.props.change("height", "");
                                            this.props.change("length", "");
                                            this.props.change("weight", "");
                                            this.props.change("width", "");
                                        }
                                    })
                                }

                            }}
                            required={true}
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="col-sm-4">
                        <Field
                            name="user_receive_id"
                            component={Select2Input}
                            select2Data={model && model.user_receive ? [{
                                id: model.user_receive.id,
                                text: model.user_receive.name
                            }] : [{
                                id: this.props.authUser.id,
                                text : this.props.authUser.name + " ( Bạn )"
                            }
                            ]}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + UserConstant.resourcePath("list?role="+UserConstant.ROLE_USER_WAREHOUSE_TQ),
                                    delay: 250
                                }
                            }}
                            label="Người nhận hàng"
                            required={true} 
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="col-sm-4">
                        <Field 
                            name="status" 
                            component={SelectInput}
                            disabled = {true}
                            label="Trạng thái">
                            <option value="" key={0}>----</option>
                            {Constant.MATCH_STATUSES.map(stt =>
                                <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                        </Field>
                    </div>
                </div>

                <div className="row">
                    <div className="col-sm-4">
                        <Field
                            name="warehouse_id"
                            component={Select2Input}
                            select2Data={model && model.warehouse ? [{
                                id: model.warehouse.id,
                                text: model.warehouse.name
                            }] :(  this.props.authUser.warehouse && this.props.authUser.warehouse_id ?
                                [{
                                    id : this.props.authUser.warehouse_id,
                                    text : this.props.authUser.warehouse.name,
                                }] :
                                []
                            ) }
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + WarehouseConstant.resourcePath("list?type="+WarehouseConstant.TYPE_TQ),
                                    delay: 250
                                }
                            }}
                            label="Kho hàng"
                            required={true} 
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="col-sm-4">
                        <Field
                            name="weight"
                            component={TextInput}
                            label="Khối lượng (kg)"
                            required={true}
                            validate={[Validator.requireFloat, Validator.greaterThan0,Validator.required]}
                        />
                    </div>
                    <div className="col-sm-4">
                        <Field
                            name="date_receiving"
                            component={DatePickerInput}

                            onDateChange={(date) => {
                                this.props.change("date_receiving", date.format("YYYY-MM-DD"));
                            }}
                            label="Ngày nhận hàng"
                            required={true}
                            validate={[Validator.required]}
                        />
                    </div>

                </div>
                {/*<div className="row">

                    <div className="col-sm-4">
                        <Field
                            name="height"
                            component={TextInput}
                            label="Chiều cao"

                        />
                    </div>
                    <div className="col-sm-4">
                        <Field
                            name="width"
                            component={TextInput}
                            label="Chiều rộng"
                        />
                    </div>
                    <div className="col-sm-4">
                        <Field
                            name="length"
                            component={TextInput}
                            label="Chiều dài"
                        />
                    </div>
                </div>*/}
                <Field
                    name="note"
                    component={TextInput}
                    label="Ghi chú"
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
        authUser: auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'WarehouseReceivingCNForm'
})(Form))
