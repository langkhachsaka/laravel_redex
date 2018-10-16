import React, {Component} from 'react';
import {bindActionCreators} from 'redux';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types';
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import * as themeActions from "../../../theme/meta/action";
import Constant from "../meta/constant";
import {toastr} from 'react-redux-toastr';
import TextArea from "../../../theme/components/TextArea";
import moment from "moment";
import swal from "sweetalert";


class ComplaintHistory extends Component {

    handleSubmit(formProps) {
        const {model} = this.props;

        formProps.complaint_id = model.id;

        return ApiService.post(Constant.resourceHistoryPath(), formProps)
            .then(({data}) => {
                this.props.setDetailState(({model}) => {
                    model.complaint_histories.push(data.data);
                    return {model: model};
                });
                this.props.reset();
                toastr.success(data.message);
                this.props.actions.closeMainModal();
            });
    }


    render() {
        const {model, handleSubmit, submitting} = this.props;

        const listHistories = model.complaint_histories.map(history =>
            <div key={history.id} className="mb-1">
                <div><strong>{_.get(history, 'user.name')}:</strong> {history.content}</div>
                <div>
                    <small>{moment(history.created_at).format("DD/MM/YYYY")}</small>
                    <small className="display-inline-block ml-2">
                        <a className="text-danger" onClick={(e) => {
                            e.preventDefault();
                            swal({
                                title: "Xoá lịch sử khiếu nại",
                                text: "Bạn có chắc chắn muốn xoá lịch sử khiếu nại này?",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            })
                                .then((willDelete) => {
                                    if (willDelete) {
                                        ApiService.delete(Constant.resourceHistoryPath(history.id))
                                            .then(({data}) => {
                                                this.props.setDetailState((prevState) => {
                                                    const complaint = prevState.model;
                                                    complaint.complaint_histories = complaint.complaint_histories.filter(m => m.id !== history.id);
                                                    return {model: complaint};
                                                });
                                                swal(data.message, {icon: "info"});
                                            });
                                    }
                                });
                        }}><i className="ft-trash-2"/> Xoá</a>
                    </small>
                </div>
            </div>);

        return (
            <div>
                <h3>Lịch sử khiếu nại</h3>

                <div>{listHistories}</div>

                <h4 className="mt-2">Thêm nội dung vào lịch sử khiếu nại</h4>
                <form className="form-horizontal" onSubmit={handleSubmit(this.handleSubmit.bind(this))}>
                    <Field
                        name="content"
                        component={TextArea}
                        label="Nội dung"
                    />

                    <div className="form-group">
                        <button type="submit" className="btn btn-primary" disabled={submitting}>
                            <i className="fa fa-fw fa-check"/> Thêm
                        </button>
                    </div>

                </form>
            </div>
        );
    }
}

ComplaintHistory.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    model: PropTypes.object,
    setDetailState: PropTypes.func,
};

function mapStateToProps({auth}) {
    return {
        userPermissions: auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'ComplaintHistoryForm'
})(ComplaintHistory))
