import React, {Component} from 'react';
import PropTypes from "prop-types";

class PaginationPageSize extends Component {
    render() {
        const {onChange} = this.props;
        const pageSizes = [5, 10, 20, 50];
        const defaultPageSize = this.props.defaultPageSize;

        return (
            <select onChange={(e) => {
                if (onChange) onChange(e.target.value);
            }} defaultValue={defaultPageSize}>
                {pageSizes.map(v => <option key={v} value={v}>{v}</option>)}
            </select>
        );
    }
}

PaginationPageSize.propTypes = {
    onChange: PropTypes.func,
};

export default PaginationPageSize;
