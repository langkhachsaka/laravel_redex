import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import UrlHelper from "../../../helpers/Url";

const maxImageUpload = value =>
    value && value.length > Constant.ORDER_ITEM_MAX_IMAGES ?
        "Chỉ được phép chọn tối đa " + Constant.ORDER_ITEM_MAX_IMAGES + " ảnh" : undefined;

const minImageUpload = value =>
    !value || value.length === 0 ?
        "Chưa có ảnh sản phẩm nào được chọn" : undefined;

class FormLinkImage extends Component {

    renderImageInputs = ({fields, meta: {error}}) => (
        <div className={`form-group ${error ? 'error' : ''}`}>
            {fields.map((image, index) =>
                <div key={index} className="input-group mb-1">
                    <Field
                        name={image}
                        component="input"
                        className="form-control"
                        placeholder={"Ảnh #" + (index + 1)}
                        validate={[Validator.required]}
                    />
                    <div className="input-group-append">
                        <button className="btn btn-danger" type="button" onClick={(e) => {
                            e.preventDefault();
                            fields.remove(index);
                        }}><i className="ft-x"/></button>
                    </div>
                </div>)}
            {fields.length < Constant.ORDER_ITEM_MAX_IMAGES &&
            <button
                type="button"
                className="drop-zone btn btn-sm btn-success"
                onClick={() => {
                    fields.push("")
                }}
            >
                <div className="drop-zone-text">Thêm ảnh sản phẩm</div>
            </button>}
            {error && <div className="help-block">{error}</div>}
        </div>
    );

    componentDidMount() {
        this.props.initialize({
            images: this.props.images.map(img => UrlHelper.imageUrl(img))
        });
    }

    handleSubmit(formProps) {
        this.props.onSave(formProps.images);
    }

    render() {
        const {handleSubmit, submitting} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <FieldArray name="images"
                            component={this.renderImageInputs.bind(this)}
                            required={true}
                            validate={[minImageUpload, maxImageUpload]}
                />

                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary" disabled={submitting}>
                        <i className="fa fa-fw fa-check"/> Lưu
                    </button>
                </div>

            </form>
        );
    }
}

FormLinkImage.propTypes = {
    handleSubmit: PropTypes.func,
    submitting: PropTypes.bool,
    onSave: PropTypes.func,
    images: PropTypes.array,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'CustomerOrderUploadImageForm'
})(FormLinkImage))
