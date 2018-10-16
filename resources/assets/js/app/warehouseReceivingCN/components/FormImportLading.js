import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import {toastr} from 'react-redux-toastr';
import Dropzone from 'react-dropzone';
import UrlHelper from "../../../helpers/Url";

class FormImportLading extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedFile: null,
            errorFields : [],
        };
    }

    handleSubmit(formProps) {
        const {model} = this.props;

        const formData = new FormData();
        if (model && model.id) formData.append('customer_order_id', model.id);
        formData.append('file', this.state.selectedFile);

        return ApiService.post(Constant.resourcePath('import'), formData)
            .then(({data}) => {
            if(data.message =="error"){
                this.setState({
                    errorFields: data.data,
                })
            }else if(data.data.length > 0){
                if (this.props.onImportSuccess) {
                    this.props.onImportSuccess(data.data);
                }
                toastr.success(data.message);
                this.props.actions.closeMainModal();
            } else {
                toastr.error(data.message);
            }

            });
    }

    render() {
        const {handleSubmit, submitting} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                <div>
                    Xem mẫu file excel <a href={UrlHelper.assetUrl("downloads/samples/nhap_kho_trung_quoc.xlsx")}>tại đây</a>
                </div>
                <div className="customer-order-import-form">
                    <Dropzone
                        style={{}}
                        multiple={false}
                        className="drop-zone"
                        accept=".xls,.xlsx"
                        onDrop={(acceptedFiles) => {
                            this.setState({selectedFile: acceptedFiles[0]});
                        }}
                    >
                        {!this.state.selectedFile && <div className="drop-zone-text">Kéo thả hoặc chọn tệp import</div>}
                        {!!this.state.selectedFile && <div className="drop-zone-uploaded-text">
                            <i className="ft-file-text"/> {this.state.selectedFile.name}
                        </div>}
                    </Dropzone>

                    <div className="form-group">
                        <button type="submit" className="btn btn-lg btn-primary"
                                disabled={submitting || !this.state.selectedFile}>
                            <i className="fa fa-fw fa-check"/> Import file
                        </button>
                    </div>

                </div>
                {this.state.errorFields.length > 0 && <div>
                    <h3  style={{ color:"red"}}> {this.state.errorFields.length==1 ?"Có 1 " : "Có một số "} mã vận đơn trong file Excel đã tồn tại trong kho hàng Trung Quốc </h3>
                    <h3  style={{ color:"red"}}> {this.state.errorFields.map(item =>{
                        return <span>Dòng {item.row_number}-{' Mã vận đơn :'} <b>{item.lading_code} </b>{'; '}</span>
                    })}</h3>
                </div>}
            </form>
        );
    }
}

FormImportLading.propTypes = {
    handleSubmit: PropTypes.func,
    submitting: PropTypes.bool,
    model: PropTypes.object,
    onImportSuccess: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'CustomerOrderImportItemForm'
})(FormImportLading))
