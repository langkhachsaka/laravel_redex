import React, {Component} from 'react';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import TextInput from "../../../theme/components/TextInput";
import * as commonActions from "../../common/meta/action";
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import SelectInput from "../../../theme/components/SelectInput";
import Validator from "../../../helpers/Validator";
import Constant from "../meta/constant";


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
                    <div className="col-sm-4">
                        <Field name="name" component={TextInput} label="Tên"/>
                    </div>
                    <div className="col-sm-4">
                        <Field name="type" component={SelectInput} label="Loại">
                            <option value="" key={0}>Tất cả</option>
                            {Constant.TYPES.map(type => <option key={type.id} value={type.id}>{type.text}</option>)}
                        </Field>
                    </div>
                    <div className="col-sm-4">
                        <Field name="address" component={TextInput} label="Địa chỉ"/>
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

function mapStateToProps({blog}) {
    return {
        search: blog.search,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'BlogFilterForm',
})(SearchForm))
