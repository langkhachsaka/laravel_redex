import React, {Component} from 'react';
import PropTypes from "prop-types";

class MonthBoxTransaction extends Component {
    constructor(props, context) {
        super(props, context);

        this.state = {
            value: this.props.value || 'N/A',
        };

        this._handleClick = this._handleClick.bind(this)
    }

    componentWillReceiveProps(nextProps) {
        this.setState({
            value: nextProps.value || 'N/A',
        })
    }

    render() {

        return (
            <div className="form-group">
                <h5>Chọn khoảng thời gian</h5>
                <div className="box" onClick={this._handleClick}>
                    <input value={this.state.value} className="form-control" readOnly style={{backgroundColor : "#fff"}}/>
                </div>
            </div>
        )
    }

    _handleClick(e) {
        // this.refs.pickRange.show();
        this.props.onClick && this.props.onClick(e)
    }
}

MonthBoxTransaction.propTypes = {
    value: PropTypes.string,
    onClick: PropTypes.func,
};

export default MonthBoxTransaction;

