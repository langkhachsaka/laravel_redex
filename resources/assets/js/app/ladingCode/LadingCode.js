import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import * as themeActions from "../../theme/meta/action";
import Card from "../../theme/components/Card";
import Layout from "../../theme/components/Layout";
import ForbiddenPage from "../common/ForbiddenPage";
import Form from "./components/Form";
import FormImport from "./components/FormImport";
import UrlHelper from "../../helpers/Url";


class LadingCode extends Component {

    componentDidMount() {
        this.props.actions.changeThemeTitle("Mã vận đơn");
    }

    render() {
        const {userPermissions} = this.props;
        if (!userPermissions.ladingCode || !userPermissions.ladingCode.create) return <ForbiddenPage/>;

        return (
            <Layout>

                <Card>
                    <h3>Thêm mã vận đơn</h3>
                    <Form/>
                </Card>

                <Card>
                    <h3>Import mã vận đơn</h3>
                    <div className="mt-1 mb-2">
                        Tính năng import cho phép  thêm nhiều  mã vận đơn vào  nhiều đơn hàng theo mã giao dịch thông qua việc sử dụng một file excel để import vào hệ thống.
                        Xem mẫu file excel <a href={UrlHelper.assetUrl("downloads/samples/lading_code_sample.xlsx")}>tại đây</a>
                    </div>
                    <FormImport/>
                </Card>

            </Layout>
        );
    }
}

function mapStateToProps(state) {
    return {
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(LadingCode)
