import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import Dropzone from 'react-dropzone'
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import TextInput from "../../../theme/components/TextInput";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import moment from "moment";
import TextArea from "../../../theme/components/TextArea";
import ImageConstant from "../../image/meta/constant";
import UrlHelper from "../../../helpers/Url";

const maxImageUpload = value =>
    value && value.length > Constant.ORDER_ITEM_MAX_IMAGES ?
        "Chỉ được phép chọn tối đa " + Constant.ORDER_ITEM_MAX_IMAGES + " ảnh" : undefined;

const CheckboxInput = ({input, label, checked}) => (
    <div>
        {label} : <input {...input} type="checkbox" checked={checked} className="checkbox-item-verify"/>
    </div>
);

class BillOfLadingForm extends Component {

    constructor(props) {
        super(props);

        this.state = {
            ladingCode : null,
        };
    }

    componentDidMount() {
        const {model} = this.props;
        if (model) {
            this.props.initialize(model);
            this.setState({
                ladingCode: model.code,
            });
        }
    }


    renderImageInputs = ({fields, meta: {error}}) => (
        <div className={`form-group ${error ? 'error' : ''}`}>
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
                    <div className="drop-zone-text">Thêm ảnh</div>
                </Dropzone>}
            </div>
            {error && <div className="help-block">{error}</div>}
        </div>
    );


    handleSubmit(formProps){

        return ApiService.post(Constant.resourcePath("storeVerifyBillOfLading"), formProps)
            .then(({data}) => {
                this.props.setListState(({models}) => {
                    models.unshift(data.data);
                    return {models: models};
                });
                toastr.success(data.message);
                this.props.actions.closeMainModal();
            });
    }


    render() {
        const {model, handleSubmit, detailView} = this.props;
        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

            <div>
                <div>
                    Mã vận đơn
                    <input  type="text" value={model.code ? model.code : model.lading_code} disabled style={{display :'inline', width : '200px',marginLeft:'20px'}} className="form-control"/>
                </div>
                <div className="row">
                    <div className="col-sm-6">
                       <div className="row">
                            <fieldset className="fiedset-verify-bill-of-lading">
                                <legend  className="legend-account-deposited">Kích thước :   </legend>
                                <div className="row">

                                    <div className="col-sm-5">
                                        &nbsp;
                                    </div>
                                    <div className="col-sm-7">
                                        <div className="row">
                                            <div className="col-sm-6">
                                                Dài
                                            </div>
                                            <div className="col-sm-6">
                                                <Field
                                                    name="length"
                                                    disabled={detailView}
                                                    component={TextInput}
                                                    required={true}
                                                    validate={[Validator.required]}
                                                />
                                            </div>
                                        </div>
                                        <div className="row">
                                            <div className="col-sm-6">
                                                Rộng
                                            </div>
                                            <div className="col-sm-6">
                                                <Field
                                                    name="width"
                                                    disabled={detailView}
                                                    component={TextInput}
                                                    required={true}
                                                    validate={[Validator.required]}
                                                />
                                            </div>
                                        </div>
                                        <div className="row">
                                            <div className="col-sm-6">
                                                Cao
                                            </div>
                                            <div className="col-sm-6">
                                                <Field
                                                    name="height"
                                                    disabled={detailView}
                                                    component={TextInput}
                                                    required={true}
                                                    validate={[Validator.required]}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset className="fiedset-verify-bill-of-lading">
                                <legend  className="legend-account-deposited"></legend>
                                <div className="row">

                                    <div className="col-sm-6">
                                        Cân nặng
                                    </div>
                                    <div className="col-sm-2">
                                    </div>
                                    <div className="col-sm-3" style={{marginLeft:'14px'}}>
                                        <Field
                                            name="weight"
                                            disabled={detailView}
                                            component={TextInput}
                                            validate={[Validator.required]}
                                        />
                                    </div>

                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div className="col-sm-6">
                        <div className="row">
                            {!detailView && <div className="col-sm-4">
                                <fieldset className="fiedset-verify-bill-of-lading">
                                    <legend  className="legend-account-deposited"> Trạng thái</legend>
                                    <div>
                                        <Field
                                            label="Bóc tem"
                                            name="is_gash_stamp"
                                            component={CheckboxInput}
                                        />
                                    </div>
                                    <div className="mt-1">
                                        <Field
                                            label="Vỡ, bẹp, rách"
                                            name="is_broken_gash"
                                            component={CheckboxInput}
                                        />
                                    </div>
                                </fieldset>
                            </div>}
                            {detailView && <div className="col-sm-4">
                                <fieldset className="fiedset-verify-bill-of-lading">
                                    <legend  className="legend-account-deposited"> Trạng thái</legend>
                                    <div>
                                        <Field
                                            label="Bóc tem"
                                            name="is_gash_stamp"
                                            checked={model.is_gash_stamp && model.is_gash_stamp == 1 ? true : false}
                                            component={CheckboxInput}
                                        />
                                    </div>
                                    <div className="mt-1">
                                        <Field
                                            label="Vỡ, bẹp, rách"
                                            name="is_broken_gash"
                                            checked={model.is_broken_gash && model.is_broken_gash == 1 ? true : false}
                                            component={CheckboxInput}
                                        />
                                    </div>
                                </fieldset>
                            </div>}
                            <div className="col-sm-8">
                                Ghi chú
                                <Field
                                        name="note"
                                        disabled={detailView}
                                        component={TextArea}
                                        rows="3"
                                    />
                            </div>
                        </div>
                        {!detailView &&<div className="row">
                            <FieldArray name="images"
                                        component={this.renderImageInputs.bind(this)}
                                        validate={maxImageUpload}
                            />
                        </div>}
                        {detailView &&
                            <div>
                                <div style={{float :'left'}}>
                                        {!!model.image1 &&
                                        <a onClick={e => {
                                            e.preventDefault();
                                            window.open(UrlHelper.imageUrl(model.image1));
                                        }}><img src={UrlHelper.imageUrl(model.image1)} className="img-thumbnail-verify"
                                                alt=""/></a>}
                                </div>
                                <div style={{float :'left'}}>
                                    {!!model.image2 &&
                                    <a onClick={e => {
                                        e.preventDefault();
                                        window.open(UrlHelper.imageUrl(model.image2));
                                    }}><img src={UrlHelper.imageUrl(model.image2)} className="img-thumbnail-verify"
                                            alt=""/></a>}
                                </div>
                                <div style={{float :'left'}}>
                                    {!!model.image3 &&
                                    <a onClick={e => {
                                        e.preventDefault();
                                        window.open(UrlHelper.imageUrl(model.image3));
                                    }}><img src={UrlHelper.imageUrl(model.image3)} className="img-thumbnail-verify"
                                            alt=""/></a>}
                                </div>
                                <div style={{float :'left'}}>
                                    {!!model.image4 &&
                                    <a onClick={e => {
                                        e.preventDefault();
                                        window.open(UrlHelper.imageUrl(model.image4));
                                    }}><img src={UrlHelper.imageUrl(model.image4)} className="img-thumbnail-verify"
                                            alt=""/></a>}
                                </div>
                                <div style={{float :'left'}}>
                                    {!!model.image5 &&
                                    <a onClick={e => {
                                        e.preventDefault();
                                        window.open(UrlHelper.imageUrl(model.image5));
                                    }}><img src={UrlHelper.imageUrl(model.image5)} className="img-thumbnail-verify"
                                            alt=""/></a>}
                                </div>
                            </div>
                        }
                    </div>

                </div>
                {!detailView &&<div style={{display : 'inline', float : 'right'}}>
                    <button type="submit" name="submit" value="submit" className="btn btn-lg btn-warning" >
                        <i className="fa fa-fw fa-check"/>
                        Cập nhật
                    </button>
                </div> }
            </div>
            </form>
        );
    }
}

BillOfLadingForm.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    detailView: PropTypes.bool,
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
})(BillOfLadingForm))
