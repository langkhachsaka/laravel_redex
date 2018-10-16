import React, {Component} from 'react';
import {connect} from "react-redux";
import Modal from "../../../app/common/Modal";
import * as themeActions from "../../meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';


class LayoutMainModal extends Component {
    render() {
        const {isOpen, title, body} = this.props;
        return (
            <Modal isOpen={isOpen} title={title} onRequestClose={() => {
                this.props.actions.closeMainModal();
            }}>
                {body}
            </Modal>
        );
    }
}

function mapStateToProps({theme: {modal}}) {
    return {
        isOpen: modal.isOpen,
        title: modal.title,
        body: modal.body,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(LayoutMainModal)
