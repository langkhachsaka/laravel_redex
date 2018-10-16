import React from 'react';
import PropTypes from 'prop-types';


const Card = ({isLoading, title, children}) => (

    <div className="card">
        {title && <div className="card-header">
            <h4 className="card-title">{title}</h4>
            {/*<div className="heading-elements">*/}
            {/*<ul className="list-inline mb-0">*/}
            {/*<li><a data-action="collapse"><i className="ft-minus"/></a></li>*/}
            {/*<li><a data-action="reload"><i className="ft-rotate-cw"/></a></li>*/}
            {/*<li><a data-action="expand"><i className="ft-maximize"/></a></li>*/}
            {/*<li><a data-action="close"><i className="ft-x"/></a></li>*/}
            {/*</ul>*/}
            {/*</div>*/}
        </div>}
        <div className="card-content collapse show">
            <div className="card-body">
                {children}
            </div>
        </div>
        {isLoading && <div>
            <div className="card-loading-overlay"/>
            <div className="card-loading-msg">
                <div className="ft-refresh-cw icon-spin font-medium-2"/>
            </div>
        </div>}
    </div>

);

Card.propTypes = {
    isLoading: PropTypes.bool,
    title: PropTypes.string,
};

export default Card;
