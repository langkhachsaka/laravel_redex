import React, {Component} from 'react';
import Layout from "../../theme/components/Layout";
import Card from "../../theme/components/Card";

class ForbiddenPage extends Component {
    render() {
        return (
            <Layout>

                <Card title="Lỗi" isLoading={false}>
                    <h2 className="mb-4 mt-2 text-center">Bạn không có quyền truy cập vào trang này</h2>
                </Card>

            </Layout>
        );
    }
}

export default ForbiddenPage