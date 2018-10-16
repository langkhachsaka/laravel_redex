import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import ConstantLadingCode from "../../ladingCode/meta/constant";
import {toastr} from 'react-redux-toastr'
import TextInput from "../../../theme/components/TextInput";
import Validator from "../../../helpers/Validator";

class FormUpdateLadingCode extends Component {

    constructor(props) {
        super(props);

        this.state = {
            height: 1,
            width: 1,
            length: 1,
            conversion_volume: 0,
            rate: 1,
        };

        this.changeStateHeight = this.changeStateHeight.bind(this);
        this.changeStateLength = this.changeStateLength.bind(this);
        this.changeStateWidth = this.changeStateWidth.bind(this);
        this.countConversionVolume = this.countConversionVolume.bind(this);
    }

    componentDidMount() {
        const {model} = this.props;

        this.props.initialize({
            id: model.id,
            ladingcodetable_id: model.ladingcodetable_id,
            ladingcodetable_type: ConstantLadingCode.MORPH_TYPE_BILL_OF_LADING,
            code: model.code,
            height: model.height,
            width: model.width,
            weight: model.weight,
            length: model.length
        });

        this.getRate(model.id);
        this.getConversionVolume(model.id);
        this.setState({
            height : model.height,
            length : model.length,
            width: model.width
        });
    }

    handleSubmit(formProps) {
        const {model} = this.props;

        if (model.ladingcodetable_id) {
            return ApiService.post(ConstantLadingCode.resourcePath(model.id), formProps)
                .then(({data}) => {
                    this.props.setDetailState((prevState) => {
                        const bills = prevState.models.map(item => item.id === data.data.id ? data.data : item);

                        return {models: bills};
                    });
                    toastr.success(data.message);
                    this.props.actions.closeMainModal();
                });
        }
    }

    getRate(id) {
        const apiGetRate = 'get-rate/' + id;
        return ApiService.get(ConstantLadingCode.resourcePath(apiGetRate))
            .then(({data}) => {
                this.setState({rate: data.data});
            });
    }

    getConversionVolume(id) {
        const apiGetConversionVolume = 'get-conversion-volume/' + id;
        return ApiService.get(ConstantLadingCode.resourcePath(apiGetConversionVolume))
            .then(({data}) => {
                this.setState({conversion_volume: data.data});
            });
    }

    countConversionVolume() {
        this.setState({conversion_volume : (this.state.height * this.state.length * this.state.width) / this.state.rate});
    }

    changeStateHeight(e) {
        this.setState({height: e.target.value});
    }
    changeStateLength(e) {
        this.setState({length: e.target.value});
    }
    changeStateWidth(e) {
        this.setState({width: e.target.value});
    }

    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <Field
                    name="code"
                    component={TextInput}
                    label="Mã vận đơn"
                    required={true}
                    validate={[Validator.required]}
                />
                <div className="row">
                    <div className="col-sm-4">
                        <Field
                            name="height"
                            component={TextInput}
                            label="Chiều cao"
                            required={true}
                            validate={[Validator.required]}
                            onBlur={this.countConversionVolume}
                            onChange={this.changeStateHeight}
                        />
                    </div>
                    <div className="col-sm-4">
                        <Field
                            name="width"
                            component={TextInput}
                            label="Chiều rộng"
                            required={true}
                            validate={[Validator.required]}
                            onChange={this.changeStateWidth}
                            onBlur={this.countConversionVolume}
                        />
                    </div>
                    <div className="col-sm-4">
                        <Field
                            name="length"
                            component={TextInput}
                            label="Chiều dài"
                            required={true}
                            validate={[Validator.required]}
                            onChange={this.changeStateLength}
                            onBlur={this.countConversionVolume}
                        />
                    </div>
                </div>
                <div className="row">
                    <div className="col-sm-4">
                        <Field
                            name="weight"
                            component={TextInput}
                            label="Khối lượng thực"
                            required={true}
                            validate={[Validator.required]}
                        />
                    </div>
                    <div className="col-sm-4">
                        <h5>Hệ số quy đổi <span className="text-danger">*</span></h5>
                        <div className="controls">
                            <input name="weight_rate" className="form-control" type="text" readOnly value={this.state.rate}/>
                        </div>
                    </div>
                    <div className="col-sm-4">
                        <h5>Khối lượng quy đổi <span className="text-danger">*</span></h5>
                        <div className="controls">
                            <input name="conversion_volume" className="form-control" type="text" readOnly value={this.state.conversion_volume}/>
                        </div>
                    </div>
                </div>

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

FormUpdateLadingCode.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    pristine: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setDetailState: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'FormUpdateLadingCode'
})(FormUpdateLadingCode))
