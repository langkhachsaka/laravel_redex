import React, {Component} from 'react';
import Layout from "../theme/components/Layout";
import Card from "../theme/components/Card";
import * as themeActions from "../theme/meta/action";
import {connect} from "react-redux";
import {bindActionCreators} from "redux";
import _ from 'lodash';

class Dashboard extends Component {

    componentDidMount() {
        this.props.actions.changeThemeTitle("Dashboard");
    }

    render() {
        return (
            <Layout>

                <Card title="Dashboard" isLoading={false}>
                    <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                </Card>

            </Layout>
        );
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(Dashboard)
