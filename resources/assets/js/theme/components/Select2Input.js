import React from 'react';
import PropTypes from 'prop-types';
import Select2 from 'react-select2-wrapper';


const Select2Input = ({input, label, required, disabled, select2Data, select2Options, select2OnSelect, meta: {touched, error, invalid}}) => (

    <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
        {!!label && <h5>{label} {input && required ? (<span className="text-danger">*</span>) : ''}</h5>}
        <Select2 {...input} disabled={disabled} className="form-control" data={select2Data} options={select2Options} onSelect={select2OnSelect}/>
        {touched && error && <div className="help-block">{error}</div>}
    </div>

);

Select2Input.propTypes = {
    input: PropTypes.object.isRequired,
    label: PropTypes.string,
    meta: PropTypes.object,
    select2Data: PropTypes.array,
    select2Options: PropTypes.object,
};

export default Select2Input;
