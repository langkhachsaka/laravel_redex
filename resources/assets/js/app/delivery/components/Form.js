import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import Validator from "../../../helpers/Validator";
import {toastr} from 'react-redux-toastr'
import DatePickerInput from "../../../theme/components/DatePickerInput";
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";
import TextArea from "../../../theme/components/TextArea";
import moment from "moment";
import UrlHelper from "../../../helpers/Url";
import ImageConstant from "../../image/meta/constant";
import Dropzone from 'react-dropzone'
import DatePicker from 'react-datepicker';

class Form extends Component {

    constructor(props) {
        super(props);

        this.state = {
            startDate: moment(),
            file:null,
            image:null,
        };
    }

    componentDidMount() {
        const {model} = this.props;

        if (model) {
            this.props.initialize(model);
        } else {
            this.props.initialize({date_delivery: moment().format("YYYY-MM-DD")});
        }
    }

    handleSubmit() {
        const {model} = this.props;
        var formData = new FormData();

        formData.append('file',this.state.file);
        formData.append('date',this.state.startDate.format("YYYY-MM-DD"));

        return ApiService.post(Constant.confirmPath(model.id), formData)
            .then(({data}) => {
                console.log(data);
                this.props.setListState(({models}) => {
                    return {models: models.map(m => m.id === data.data.id ? data.data : m)};
                });
                toastr.success(data.message);

                this.props.actions.closeMainModal();
            });
    }

    onImageChange(event) {
        if (event.target.files && event.target.files[0]) {
            this.setState({file: event.target.files[0]});
            let reader = new FileReader();
            reader.onload = (e) => {
                this.setState({image: e.target.result});
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    render() {
        const {model, handleSubmit, submitting, pristine} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <div className="form-group">
                    <h5>Ngày xuất hàng</h5>
                    <DatePicker
                        className="form-control"
                        name="date_delivery"
                        selected={this.state.startDate}
                        onChange={(date) => {
                            this.setState({startDate: date.format("YYYY-MM-DD")})
                        }}
                    />
                </div>

                <div className="form-group">
                    <h5>Ảnh biên bản</h5>
                    <input type="file" onChange={this.onImageChange.bind(this)} className="filetype" id="group_image"/>
                    <img id="target" src={this.state.image} style={{width:"200px",height:"200px"}}/>
                </div>

                <div className="form-group">
                    <button type="submit" className="btn btn-lg btn-primary">
                        <i className="fa fa-fw fa-check"/>
                        Xác nhận
                    </button>
                </div>

            </form>
        );
    }
}

Form.propTypes = {
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
    form: 'DeliveryForm'
})(Form))
