import React, {Component} from 'react';

class LayoutFooter extends Component {
    render() {
        return (
            <footer className="footer footer-static footer-light navbar-border navbar-shadow" style={{display: 'none'}}>
                <p className="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
      <span className="float-md-left d-block d-md-inline-block">Copyright &copy; 2018 <a
          className="text-bold-800 grey darken-2"
          href="https://themeforest.net/user/pixinvent/portfolio?ref=pixinvent"
          target="_blank">PIXINVENT </a>, All rights reserved. </span>
                    <span className="float-md-right d-block d-md-inline-blockd-none d-lg-block">Hand-crafted & Made with <i
                        className="ft-heart pink"/></span>
                </p>
            </footer>
        );
    }
}

export default LayoutFooter
