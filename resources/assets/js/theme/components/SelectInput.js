import React from 'react';
import PropTypes from 'prop-types';


const SelectInput = ({input, label, required, disabled, meta: {touched, error, invalid, warning}, children}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        {!!label && <h5>{label} {input && required ? (<span className="text-danger">*</span>) : ''}</h5>}
        <select {...input} disabled={disabled} className="form-control">
            {children}
        </select>
        {touched && error && <div className="help-block">{error}</div>}
    </div>

);

SelectInput.propTypes = {
    input: PropTypes.object.isRequired,
    label: PropTypes.string,
    meta: PropTypes.object,
};

export default SelectInput;
