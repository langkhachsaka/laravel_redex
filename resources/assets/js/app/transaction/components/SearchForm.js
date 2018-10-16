import React, {Component} from 'react';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import * as commonActions from "../../common/meta/action";
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import SelectInput from "../../../theme/components/SelectInput";
import Constant from "../meta/constant";
import TextInput from "../../../theme/components/TextInput";


class SearchForm extends Component {

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
                    <div className="col-md-4">
                        <Field name="type" component={SelectInput} label="Loại giao dịch">
                            <option value="" key={0}>Tất cả</option>
                            <option value="0" key={1}>Đặt cọc</option>
                        </Field>
                    </div>

                    <div className="col-md-4">
                        <Field name="transactiontable_type" component={SelectInput} label="Loại đơn hàng">
                            <option value="">Tất cả</option>
                            <option value={Constant.MORPH_TYPE_ORDER_VN}>Đơn hàng Việt Nam</option>
                            <option value={Constant.MORPH_TYPE_BILL_OF_LADING}>Đơn hàng vận chuyển</option>
                        </Field>
                    </div>

                    <div className="col-md-4">
                        <Field name="transactiontable_id" component={TextInput} label="Mã đơn hàng">
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

function mapStateToProps({transaction}) {
    return {
        search: transaction.search
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'TransactionFilterForm',
})(SearchForm))
