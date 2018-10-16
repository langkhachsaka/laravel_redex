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
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'


class NormalShipping extends Component {

    componentDidMount() {
        this.props.initialize(this.props.model);
    }

    handleSubmit(formProps) {
        return ApiService.post(Constant.resourcePath(), formProps)
            .then(({data}) => {
                this.props.setListState(({models}) => {
                    return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                });

                toastr.success(data.message);
                this.props.actions.closeMainModal();
            });
    }

    render() {
        const {handleSubmit, submitting, pristine} = this.props;
        const {model} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                {model.key === "less_than_30_normal" &&
                <Field
                    name="price"
                    component={TextInput}
                    label="Trọng lượng dưới 30kg"
                />}

                {model.key === "more_than_30_normal" &&
                <Field
                    name="price"
                    component={TextInput}
                    label="Trọng lượng từ 30kg trở lên"
                />}

                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting || pristine}>
                        <i className="fa fa-fw fa-check"/> Cập nhật
                    </button>
                </div>

            </form>
        );
    }
}

NormalShipping.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setListState: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'PriceListForm'
})(NormalShipping))
