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

class FormImportItem extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedFile: null,
        };
    }

    handleSubmit(formProps) {
        const {model} = this.props;

        const formData = new FormData();
        if (model && model.id) formData.append('customer_order_id', model.id);
        formData.append('file', this.state.selectedFile);

        return ApiService.post(Constant.resourceItemPath('import'), formData)
            .then(({data}) => {
                if (this.props.onImportSuccess) {
                    this.props.onImportSuccess(data.data);
                }

                toastr.success(data.message);
                this.props.actions.closeMainModal();
            });
    }

    render() {
        const {handleSubmit, submitting} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

                <div className="mb-2">
                    Chức năng này cho phép bạn thêm một file excel chứa thông tin những sản phẩm cần thêm vào đơn hàng.
                    Bạn có thể tải mẫu excel
                    <a href={UrlHelper.assetUrl("storage/upload/example/example.xlsx")}> tại đây</a>
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

            </form>
        );
    }
}

FormImportItem.propTypes = {
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
})(FormImportItem))
