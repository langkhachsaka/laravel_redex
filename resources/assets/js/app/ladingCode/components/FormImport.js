import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import Dropzone from 'react-dropzone';

class FormImport extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedFile: null,
            importResult: []
        };
    }

    handleSubmit(formProps) {
        this.setState({
            importResult: []
        });

        const formData = new FormData();
        formData.append('file', this.state.selectedFile);

        return ApiService.post(Constant.resourcePath('import'), formData)
            .then(({data: {data}}) => {
                this.setState({
                    selectedFile: null,
                    importResult: data
                });
            });
    }

    render() {
        const {handleSubmit, submitting} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>

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

                {this.state.importResult.length > 0 && <div className="mt-2">
                    <h4>Kết quả import</h4>
                    <div className="table-responsive">
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                <th>Mã vận đơn</th>
                                <th>Mã giao dịch</th>
                                <th>Trạng thái</th>
                            </tr>
                            </thead>
                            <tbody>
                            {this.state.importResult.map((result, index) => (
                                <tr key={index}>
                                    <td>{result.model.code}</td>
                                    <td>{result.model.bill_code}</td>
                                    <td>
                                        {result.status === 'ok' && <span className="text-success">
                                            {result.message}
                                        </span>}
                                        {result.status === 'error' && <span className="text-danger">
                                            {result.message}
                                        </span>}
                                    </td>
                                </tr>
                            ))}
                            </tbody>
                        </table>
                    </div>
                </div>}

            </form>
        );
    }
}

FormImport.propTypes = {
    handleSubmit: PropTypes.func,
    submitting: PropTypes.bool,
    onImportSuccess: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'LadingCodeImportForm'
})(FormImport))
