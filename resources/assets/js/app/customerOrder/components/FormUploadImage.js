import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import ImageConstant from "../../image/meta/constant";
import Dropzone from 'react-dropzone'
import AppConfig from "../../../config";
import UrlHelper from "../../../helpers/Url";

const maxImageUpload = value =>
    value && value.length > Constant.ORDER_ITEM_MAX_IMAGES ?
        "Chỉ được phép chọn tối đa " + Constant.ORDER_ITEM_MAX_IMAGES + " ảnh" : undefined;

const minImageUpload = value =>
    !value || value.length === 0 ?
        "Chưa có ảnh sản phẩm nào được chọn" : undefined;

class FormUploadImage extends Component {

    renderImageInputs = ({fields, meta: {error}}) => (
        <div className={`form-group ${error ? 'error' : ''}`}>
            <h5>Ảnh sản phẩm <span className="text-danger">*</span></h5>
            <div className="images-input-preview">
                {fields.map((image, index) =>
                    <div key={index} className="img-preview">
                        <a onClick={(e) => {
                            e.preventDefault();
                            fields.remove(index);
                            // TODO optimize
                            //ApiService.delete(ImageConstant.resourcePath('delete'), {image: fields.get(index)});
                        }}><i className="ft-x"/></a>
                        <img src={UrlHelper.imageUrl(fields.get(index))} alt=""/>
                        <Field name={image} type="hidden" component="input"/>
                    </div>)}
                {fields.length < Constant.ORDER_ITEM_MAX_IMAGES && <Dropzone
                    style={{}}
                    className="drop-zone"
                    accept="image/*"
                    onDrop={(acceptedFiles) => {
                        let formData = new FormData();
                        acceptedFiles.forEach((acceptedFile) => {
                            formData.append('images[]', acceptedFile);
                        });

                        ApiService.post(ImageConstant.resourcePath(), formData)
                            .then(({data}) => {
                                data.data.forEach((item) => fields.push(item));
                            });
                    }}
                >
                    <div className="drop-zone-text">Thêm ảnh sản phẩm</div>
                </Dropzone>}
            </div>
            {error && <div className="help-block">{error}</div>}
        </div>
    );

    componentDidMount() {
        this.props.initialize({
            images: this.props.images
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

FormUploadImage.propTypes = {
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
})(FormUploadImage))
