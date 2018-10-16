import React from 'react';
import PropTypes from 'prop-types';


const Widget = ({title, strShow, classBackground}) => (

    <div className="col-lg-3 col-md-6">
        <div className={'card text-white ' + classBackground}>
            <div className="card-header">
                <div className="row">
                    <div className="col-3">
                        <i className="la la-opencart" style={{fontSize: '5rem'}}></i>
                    </div>
                    <div className="col-9 text-right">
                        <div>{title}</div>
                        <div style={{fontSize: '3rem', lineHeight: '3rem'}}>{strShow}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

);


Widget.propTypes = {
    title: PropTypes.string,
    strShow: PropTypes.string,
    classBackground: PropTypes.string
};

Widget.defaultProps = {
    title: 'Dasboard',
    strShow: '',
    classBackground: ''
};

export default Widget;