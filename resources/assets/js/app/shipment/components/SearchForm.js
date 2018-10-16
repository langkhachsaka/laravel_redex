import React, {Component} from 'react';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import TextInput from "../../../theme/components/TextInput";
import * as commonActions from "../../common/meta/action";
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import SelectInput from "../../../theme/components/SelectInput";
import Constant from "../meta/constant";
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";
import WarehouseConstant from "../../warehouse/meta/constant";

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
        const {handleSubmit, submitting, reset} = this.props;
        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleFilterSubmit.bind(this))}>
                <div className="row">

                    <div className="col-sm-3">
                        <Field name="shipment_code" component={TextInput} label="Mã lô hàng"/>
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="warehouse_id"
                            component={Select2Input}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + WarehouseConstant.resourcePath("list?type="+WarehouseConstant.TYPE_VN),
                                    delay: 250
                                }
                            }}
                            label="Kho nhận"
                        />
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="status"
                            component={SelectInput}
                            label="Trạng thái">
                            <option value="" key={0}>-- Tất cả --</option>
                            {Constant.STATUSES.map(stt =>
                                <option value={stt.id} key={stt.id}>{stt.text}</option>)}
                        </Field>
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="transport_type"
                            component={SelectInput}
                            label="Loại hình vận chuyển">
                            <option value="" key={0}>-- Chọn loại hình vận chuyển --</option>
                            <option value="999" key={999}>Chưa thiết lập</option>
                            {Constant.TRANSPORT_TYPES.map(stt =>
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

function mapStateToProps({warehouseReceivingCN}) {
    return {
        search: warehouseReceivingCN.search,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'WarehouseReceivingVNFilterForm',
})(SearchForm))
