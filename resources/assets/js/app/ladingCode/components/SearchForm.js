import React, {Component} from 'react';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import TextInput from "../../../theme/components/TextInput";
import * as commonActions from "../../common/meta/action";
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";


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
                    <div className="col-sm-6">
                        <Field name="code" component={TextInput} label="Mã vận đơn"/>
                    </div>
                    <div className="col-sm-6">
                        <Field name="bill_code" component={TextInput} label="Mã giao dịch"/>
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

function mapStateToProps({ladingCode}) {
    return {
        search: ladingCode.search,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'LadingCodeFilterForm',
})(SearchForm))
