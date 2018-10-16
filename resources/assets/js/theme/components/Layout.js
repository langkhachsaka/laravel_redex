import React, {Component} from 'react';
import LayoutNav from './partials/LayoutNav';
import LayoutFooter from './partials/LayoutFooter';
import LayoutMainMenu from './partials/LayoutMainMenu';
import LayoutTitle from "./partials/LayoutTitle";
import LayoutMainModal from "./partials/LayoutMainModal";
import LayoutCustomizer from "./partials/LayoutCustomizer";


class Layout extends Component {
    render() {
        return (
            <div>

                <LayoutNav/>

                <LayoutMainMenu/>

                <div className="app-content content">
                    <div className="content-wrapper">

                        <LayoutTitle/>

                        <div className="content-body">
                            {this.props.children}
                        </div>
                    </div>
                </div>

                <LayoutCustomizer/>

                <LayoutFooter/>

                <LayoutMainModal/>

            </div>
        );
    }
}

export default Layout
