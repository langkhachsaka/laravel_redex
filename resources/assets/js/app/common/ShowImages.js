import React, {Component} from 'react';
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as themeActions from "../../theme/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import UrlHelper from "../../helpers/Url";


class ShowImages extends Component {

    constructor(props) {
        super(props);

        this.state = {
            img: props.images[0],
        };
    }

    render() {
        const {images} = this.props;

        return (
            <div className="order-item-images-preview">
                <div className="image-preview">
                    <img className="img-fluid" src={UrlHelper.imageUrl(this.state.img.path)} alt=""/>
                </div>
                <div className="images-thumb">
                    {images.map(img =>
                        <div key={img.id}
                             className={"img-thumb" + (this.state.img.id === img.id ? " active" : "")}>
                            <a onClick={e => {
                                e.preventDefault();
                                this.setState({img: img});
                            }}>
                                <img className="img-fluid" src={UrlHelper.imageUrl(img.path)} alt=""/>
                            </a>
                        </div>
                    )}
                </div>
            </div>
        );
    }
}

ShowImages.propTypes = {
    images: PropTypes.array.isRequired
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(ShowImages)
