import React from 'react';
import PropTypes from 'prop-types';


const TextArea = ({input, label, placeholder,disabled, required, rows, meta: {touched, error, invalid, warning}}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        <h5>{label} {input && required ? (<span className="text-danger">*</span>) : ''}</h5>
        <div className="controls">
            <textarea {...input} disabled={disabled} placeholder={placeholder} rows={rows || 2} className="form-control"/>
            {touched && error && <div className="help-block">{error}</div>}
        </div>
    </div>

);

TextArea.propTypes = {
    input: PropTypes.object.isRequired,
    label: PropTypes.string,
    meta: PropTypes.object,
};

export default TextArea;
