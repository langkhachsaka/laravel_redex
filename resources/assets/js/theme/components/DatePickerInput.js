import React, {Component} from 'react';
import PropTypes from "prop-types";
import DatePicker from 'react-datepicker';
import moment from "moment";


class DatePickerInput extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedDate: props.input.value ? moment(props.input.value, "YYYY-MM-DD") : null
        };
    }

    componentWillReceiveProps(nextProps) {
        this.setState({
            selectedDate: nextProps.input.value ? moment(nextProps.input.value, "YYYY-MM-DD") : null
        });
    }

    handleChange(date) {
        this.props.onDateChange(date);
        this.setState({
            selectedDate: date
        });
    }

    render() {
        const {input, label, disabled, placeholder, required, meta: {touched, error, invalid}} = this.props;
        const {selectsStart, selectsEnd, startDate, endDate} = this.props;

        const dpkProps = {
            placeholderText: placeholder,
            dateFormat: "DD/MM/YYYY",
            className: "form-control",
            onChange: this.handleChange.bind(this),
            selected: this.state.selectedDate,
        };
        if (selectsStart) {
            dpkProps.selectsStart = true;
            dpkProps.startDate = this.state.selectedDate;
            dpkProps.endDate = endDate;
        } else if (selectsEnd) {
            dpkProps.selectsEnd = true;
            dpkProps.endDate = this.state.selectedDate;
            dpkProps.startDate = startDate;
        }

        return (
            <div className={`form-group ${touched && invalid ? 'error' : ''}`}>
                {!!label && <h5>{label} {required ? (<span className="text-danger">*</span>) : ''}</h5>}
                <div className="controls">
                    <DatePicker {...dpkProps} disabled={disabled}/>
                    <input {...input} type="hidden"/>
                    {touched && error && <div className="help-block">{error}</div>}
                </div>
            </div>
        );
    }
}

DatePickerInput.propTypes = {
    input: PropTypes.object.isRequired,
    label: PropTypes.string,
    meta: PropTypes.object,
};

export default DatePickerInput
