import React from 'react';
import PropTypes from 'prop-types';


const TextInput = ({input, label, type, disabled, placeholder, required, meta: {touched, error, invalid}}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        {!!label && <h5>{label} {input && required ? (<span className="text-danger">*</span>) : ''}</h5>}
        <div className="controls">
            <input {...input} type={type} disabled={disabled} placeholder={placeholder} className="form-control"/>
            {touched && error && <div className="help-block">{error}</div>}
        </div>
    </div>

);

TextInput.propTypes = {
    input: PropTypes.object.isRequired,
    label: PropTypes.string,
    meta: PropTypes.object,
};

export default TextInput;
