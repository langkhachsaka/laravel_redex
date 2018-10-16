import React from 'react';
import PropTypes from 'prop-types';

const handlePrevent = e => {
    e.preventDefault();
    return false;
};

const PasswordInput = ({input, label, placeholder, required, meta: {touched, error, invalid}}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        <h5>{label} {input && required ? (<span className="text-danger">*</span>) : ''}</h5>
        <div className="controls">
            <input {...input} type="password" placeholder={placeholder} className="form-control"
                   onCopy={handlePrevent}
                   onPaste={handlePrevent}
                   onDrag={handlePrevent}
                   onDrop={handlePrevent}
            />
            {touched && error && <div className="help-block">{error}</div>}
        </div>
    </div>

);

PasswordInput.propTypes = {
    input: PropTypes.object.isRequired,
    label: PropTypes.string,
    meta: PropTypes.object,
};

export default PasswordInput;
