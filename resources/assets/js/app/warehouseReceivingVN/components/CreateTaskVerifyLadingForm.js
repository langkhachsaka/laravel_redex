import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";

import UserConstant from "../../user/meta/constant";
import Constant from "../meta/constant";
import Select2Input from "../../../theme/components/Select2Input";
import CheckboxInput from "../../../theme/components/CheckboxInput";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import SelectInput from "../../../theme/components/SelectInput";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import WarehouseConstant from "../../warehouse/meta/constant";
import AppConfig from "../../../config";
import ShipmentConstant from "../../shipment/meta/constant";
import moment from "moment";

class CreateTaskVerifyLadingForm extends Component {

    constructor(props) {
        super(props);

        this.state = {
            models : this.props.model,
            allowSelect : true,
            shipmentSelected: null,
            shipmentCode : null,
        };
    }

    componentDidMount() {

    }

    handleSubmit(formProps) {


        return ApiService.post(Constant.resourcePath('createTaskVerify'), {...formProps,lading_code : this.state.models})
        .then(({data}) => {
            data.data.map(item => {
                this.props.setListState(({models}) => {
                    return {models: models.map(m => m.id === item.id ? item : m)};
                });
            });
            if (this.props.onImportSuccess) {
                this.props.onImportSuccess(true);
            }
            toastr.success(data.message);
            this.props.actions.closeMainModal();
        })
    }

    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;
        const newRow = '</div><div className="row">'
        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <fieldset className="fiedset-verify-customer-order bgc-grey">
                    <legend  className="legend-account-deposited">Danh sách mã vận đơn</legend>
                    <div className="row">
                        {this.state.models.map((model,index) => {

                            return (
                            <div key ={index} className="col-sm-3">
                                {index + 1}.{model}
                            </div>)
                                {index > 0 && index%4==0 ? newRow :""}
                            })
                        }
                    </div>
                </fieldset>


                <div className={"row"}>
                    <div className="col-sm-3">
                        <Field
                            name="verifier_id"
                            component={Select2Input}
                            select2Data={[]}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + UserConstant.resourcePath("list?role="+UserConstant.ROLE_USER_WAREHOUSE_VN),
                                    delay: 250
                                }
                            }}
                            label="Chọn nhân viên kiểm hàng"
                            required={true}
                            validate={[Validator.required]}
                        />
                    </div>
                </div>

                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                        <i className="fa fa-fw fa-check"/>
                         Xác nhận
                    </button>
                </div>
            </form>
        );
    }
}

/*Form.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setListState: PropTypes.func,
};*/


function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'CreateTaskVerifyLadingForm'
})(CreateTaskVerifyLadingForm))
