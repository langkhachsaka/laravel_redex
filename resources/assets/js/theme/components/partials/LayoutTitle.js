import React, {Component} from 'react';
import {connect} from "react-redux";
import {Link} from "react-router-dom";


class LayoutTitle extends Component {

    render() {
        document.title = this.props.title;

        return (
            <div className="content-header row">
                <div className="content-header-left col-md-6 col-12 mb-2">
                    <h3 className="content-header-title">{this.props.title}</h3>
                    {/*<div className="row breadcrumbs-top">*/}
                        {/*<div className="breadcrumb-wrapper col-12">*/}
                            {/*<ol className="breadcrumb">*/}
                                {/*<li className="breadcrumb-item"><Link to="/">Trang chủ</Link></li>*/}
                                {/*{this.props.breadcrumb}*/}
                            {/*</ol>*/}
                        {/*</div>*/}
                    {/*</div>*/}
                </div>
                {/*<div className="content-header-right col-md-6 col-12">*/}
                    {/*<div className="btn-group float-md-right" role="group"*/}
                         {/*aria-label="Button group with nested dropdown">*/}
                        {/*<button className="btn btn-info round dropdown-toggle dropdown-menu-right box-shadow-2 px-2"*/}
                                {/*id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true"*/}
                                {/*aria-expanded="false">*/}
                            {/*<i className="ft-settings icon-left"/> Settings*/}
                        {/*</button>*/}
                        {/*<div className="dropdown-menu" aria-labelledby="btnGroupDrop1">*/}
                            {/*<a className="dropdown-item" href="card-bootstrap.html">Cards</a>*/}
                            {/*<a className="dropdown-item" href="component-buttons-extended.html">Buttons</a>*/}
                        {/*</div>*/}
                    {/*</div>*/}
                {/*</div>*/}
            </div>
        );
    }
}

function mapStateToProps({theme}) {
    return {
        title: theme.title,
    }
}

export default connect(mapStateToProps)(LayoutTitle)
