import React, {Component} from 'react';
import PropTypes from 'prop-types';


class CheckboxInput extends Component {
    constructor(props) {
        super(props);
        this.state = {
            inputId: "checkbox_" + Math.random().toString().replace(".", ""),
            isChecked: false,
        };
    }

    componentWillReceiveProps(nextProps) {
        this.setState({isChecked: nextProps.input.value === 1 || nextProps.input.value === true});
    }

    render() {
        const {input, label} = this.props;
        const {inputId} = this.state;

        return (

            <div className="checkbox">
                <input {...input} type="checkbox" value="1" id={inputId} checked={this.state.isChecked}
                       onChange={() => {
                           this.setState(prevState => {
                               return {isChecked: !prevState.isChecked}
                           });
                       }}/>
                <label htmlFor={inputId}>{label}</label>
            </div>
        );
    }
}

CheckboxInput.propTypes = {
    input: PropTypes.object.isRequired,
    label: PropTypes.string,
    meta: PropTypes.object,
};

export default CheckboxInput;
