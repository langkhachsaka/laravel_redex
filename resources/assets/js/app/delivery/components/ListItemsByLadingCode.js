import React, {Component} from 'react';
import PropTypes from 'prop-types'
import _ from 'lodash';
import ApiService from "../../../services/ApiService";
import LadingCodeConstant from "../../ladingCode/meta/constant";
import UrlHelper from "../../../helpers/Url";
import Card from "../../../theme/components/Card";


class ListItemsByLadingCode extends Component {

    constructor(props) {
        super(props);

        this.state = {
            ladingCodeSelected: {},
            isLoading: true,
        };
    }

    componentDidMount() {
        const {ladingCode} = this.props;

        ApiService.get(LadingCodeConstant.resourcePath("get-order-items/" + ladingCode))
            .then(({data: {data}}) => {
                this.setState({
                    ladingCodeSelected: data,
                    isLoading: false,
                });
            });
    }

    render() {
        const items = this.state.ladingCodeSelected.items;

        return (
            <Card isLoading={this.state.isLoading}>
                {!!items && items.length > 0 &&
                <div>

                    <div className="mb-2">
                        <h5>Thông tin người nhận</h5>
                        Tên: <b>{_.get(items, "shipping.name")}</b><br/>
                        Địa chỉ: <b>{_.get(items, "shipping.address")}</b><br/>
                        Điện thoại: <b>{_.get(items, "shipping.phone")}</b>
                    </div>

                    {/*CustomerOrderItem*/}
                    {items.order_items && <div>
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                <th>STT</th>
                                <th style={{width: '100px'}}>Ảnh</th>
                                <th>Mô tả</th>
                                <th>Khối lượng</th>
                                <th>Số lượng</th>
                                <th>Ghi chú</th>
                            </tr>
                            </thead>
                            <tbody>

                            {items.order_items.map((item, index) =>
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td className="pr-0 pl-0">
                                        {!!item.images.length &&
                                        <img src={UrlHelper.imageUrl(item.images[0].path)} className="img-fluid"
                                             alt=""/>}
                                    </td>
                                    <td>
                                        <a href={item.link} target="_blank">{item.description}</a><br/>
                                        Màu sắc: <b>{item.colour}</b><br/>
                                        Kích cỡ (cm): <b>{item.size}</b><br/>
                                    </td>
                                    <td>
                                        {item.weight} kg
                                    </td>
                                    <td>
                                        {item.quantity}<br/>
                                        <small className="font-italic">{item.unit}</small>
                                    </td>
                                    <td>{item.note}</td>
                                </tr>
                            )}

                            </tbody>
                        </table>
                    </div>}

                    {/*BillOfLading*/}
                    {items.bill_of_ladings && <div>
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                <th>Mã đơn vận chuyển</th>
                                <th>Cty chuyển phát</th>
                                <th>Tệp đính kèm</th>
                            </tr>
                            </thead>
                            <tbody>

                            {items.bill_of_ladings.map((item, index) =>
                                <tr key={item.id}>
                                    <td>{item.id}</td>
                                    <td>{_.get(item, 'courier_company.name')}</td>
                                    <td>
                                        <i className="ft-file-text"/> {item.file_name}<br/>
                                        <a href={item.link_download_file} className="mr-2 d-inline-block">
                                            <i className="ft-download"/> Tải xuống
                                        </a>
                                        <a href={item.link_view_file_online} target="_blank" className="d-inline-block">
                                            <i className="ft-eye"/> Xem Online
                                        </a>
                                    </td>
                                </tr>
                            )}

                            </tbody>
                        </table>
                    </div>}

                </div>}
            </Card>
        );
    }
}

ListItemsByLadingCode.propTypes = {
    ladingCode: PropTypes.string.isRequired
};

export default ListItemsByLadingCode;
