import React from 'react';
import PropTypes from 'prop-types';
import ReactModal from 'react-modal';

class Modal extends React.Component {
    static propTypes = {
        isOpen: PropTypes.bool,
        title: PropTypes.string,
        onRequestClose: PropTypes.func,
    };

    render() {
        const {isOpen, title, footer} = this.props;

        const customStyles = {
            content: {
                position: 'static',
                padding: '0',
                borderRadius: '0',
                border: 'none',
                margin: '30px auto',
            },
            overlay: {
                zIndex: '150',
                backgroundColor: 'rgba(0, 0, 0, 0.4)',
            }
        };

        return (
            <ReactModal isOpen={isOpen} style={customStyles} ariaHideApp={false}
                        onRequestClose={this.props.onRequestClose}>
                <div className="modal-content">
                    <div className="modal-header">
                        <h4 className="modal-title">{title}</h4>
                        <button type="button" className="close" onClick={this.props.onRequestClose}>×</button>
                    </div>
                    <div className="modal-body">
                        {this.props.children}
                    </div>
                    {footer && <div className="modal-footer">{footer}</div>}
                </div>
            </ReactModal>
        );
    }
}

export default Modal;
