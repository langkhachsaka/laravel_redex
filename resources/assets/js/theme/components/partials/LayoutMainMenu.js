import React, {Component} from 'react';
import {Link} from "react-router-dom";
import {connect} from "react-redux";

class LayoutMainMenu extends Component {
    render() {
        const {userPermissions} = this.props;

        return (
            <div className="main-menu menu-fixed menu-light menu-accordion menu-shadow"
                 data-scroll-to-active="true">
                <div className="main-menu-content" style={{overflow: 'auto', paddingBottom: '30px'}}>
                    <ul className="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

                        {userPermissions.customer_order && userPermissions.customer_order.index &&
                        <li className="nav-item">
                            <Link to="/customer-order">
                                <i className="la la-shopping-cart"/>
                                <span className="menu-title">Đơn hàng</span>
                            </Link>
                        </li>}

                        {/*{userPermissions.china_order && userPermissions.china_order.index &&*/}
                        {/*<li className="nav-item">*/}
                        {/*<Link to="/china-order">*/}
                        {/*<i className="la la-cart-arrow-down"/>*/}
                        {/*<span className="menu-title">Đơn hàng TQ</span>*/}
                        {/*</Link>*/}
                        {/*</li>}*/}

                        {userPermissions.bill_of_lading && userPermissions.bill_of_lading.index &&
                        <li className="nav-item">
                            <Link to="/bill-of-lading">
                                <i className="la la-opencart"/>
                                <span className="menu-title">Đơn hàng vận chuyển</span>
                            </Link>
                        </li>}

                        {userPermissions.rate && userPermissions.rate.index &&
                        <li className="nav-item">
                            <Link to="/rate">
                                <i className="la la-dollar"/>
                                <span className="menu-title">Tỷ giá</span>
                            </Link>
                        </li>}

                        {userPermissions.customer && userPermissions.customer.index &&
                        <li className="nav-item">
                            <Link to="/customer">
                                <i className="la la-users"/>
                                <span className="menu-title">Khách hàng</span>
                            </Link>
                        </li>}

                        {userPermissions.complaint && userPermissions.complaint.index &&
                        <li className="nav-item">
                            <Link to="/complaint">
                                <i className="la la-exclamation-triangle"/>
                                <span className="menu-title">Khiếu nại</span>
                            </Link>
                        </li>}

                        {userPermissions.user && userPermissions.user.index &&
                        <li className="nav-item">
                            <Link to="/user">
                                <i className="la la-user"/>
                                <span className="menu-title">Nhân viên</span>
                            </Link>
                        </li>}

                        {userPermissions.task && userPermissions.task.index &&
                        <li className="nav-item">
                            <Link to="/task">
                                <i className="la la-tasks"/>
                                <span className="menu-title">Nhiệm vụ</span>
                            </Link>
                        </li>}

                        {userPermissions.area_code && userPermissions.area_code.index &&
                        <li className="nav-item">
                            <Link to="/area-code">
                                <i className="la la-map-marker"/>
                                <span className="menu-title">Mã vùng</span>
                            </Link>
                        </li>}

                        {userPermissions.shop && userPermissions.shop.index &&
                        <li className="nav-item">
                            <Link to="/shop">
                                <i className="la la-archive"/>
                                <span className="menu-title">Nguồn hàng</span>
                            </Link>
                        </li>}

                        {userPermissions.inventory && userPermissions.inventory.index &&
                        <li className="nav-item">
                            <Link to="/inventory">
                                <i className="la la-recycle"/>
                                <span className="menu-title">Hàng tồn kho</span>
                            </Link>
                        </li>}

                        {userPermissions.warehouse && userPermissions.warehouse.index &&
                        <li className="nav-item">
                            <Link to="/warehouse">
                                <i className="la la-home"/>
                                <span className="menu-title">Kho hàng</span>
                            </Link>
                        </li>}

                        {userPermissions.warehouse_receiving_cn && userPermissions.warehouse_receiving_cn.index &&
                        <li className="nav-item">
                            <Link to="/warehouse-receiving-cn">
                                <i className="la la-industry"/>
                                <span className="menu-title">Kho hàng Trung Quốc</span>
                            </Link>
                        </li>}

                        <li className="nav-item">
                            <Link to="/shipment">
                                <i className="la la-truck"/>
                                <span className="menu-title">Lô hàng vận chuyển</span>
                            </Link>
                        </li>

                        {userPermissions.warehouse_receiving_vn && userPermissions.warehouse_receiving_vn.index &&
                        <li className="nav-item">
                            <Link to="/warehouse-receiving-vn">
                                <i className="la la-industry"/>
                                <span className="menu-title">Kho hàng Việt Nam</span>
                            </Link>
                        </li>}

                        {userPermissions.ladingCode && userPermissions.ladingCode.index &&
                        <li className="nav-item">
                            <Link to="/lading-code">
                                <i className="la la-barcode"/>
                                <span className="menu-title">Mã vận đơn</span>
                            </Link>
                        </li>}

                        <li className="nav-item">
                            <Link to="/verify-lading-code">
                                <i className="la la-check-square-o"/>
                                <span className="menu-title">Kiểm tra kiện hàng</span>
                            </Link>
                        </li>

                        {userPermissions.delivery && userPermissions.delivery.index &&
                        <li className="nav-item">
                            <Link to="/delivery">
                                <i className="la la-sign-out"/>
                                <span className="menu-title">Xuất hàng</span>
                            </Link>
                        </li>}

                        {userPermissions.courier_company && userPermissions.courier_company.index &&
                        <li className="nav-item">
                            <Link to="/courier-company">
                                <i className="la la-car"/>
                                <span className="menu-title">Công ty chuyển phát</span>
                            </Link>
                        </li>}

                        {userPermissions.statistical && userPermissions.statistical.index &&
                        <li className="nav-item">
                            <Link to="/statistical">
                                <i className="la la-area-chart"/>
                                <span className="menu-title">Thống kê</span>
                            </Link>
                        </li>}

                        {userPermissions.transaction && userPermissions.transaction.index &&
                        <li className="nav-item">
                            <Link to="/transaction">
                                <i className="la la-money"/>
                                <span className="menu-title">Giao dịch</span>
                            </Link>
                        </li>}

                        {userPermissions.setting && userPermissions.setting.index &&
                        <li className="nav-item">
                            <Link to="/setting">
                                <i className="la la-cogs"/>
                                <span className="menu-title">Cấu hình</span>
                            </Link>
                        </li>}

                        {userPermissions.priceList && userPermissions.priceList.index &&
                        <li className="nav-item">
                            <Link to="/price-list">
                                <i className="la la-cogs"/>
                                <span className="menu-title">Bảng giá cước</span>
                            </Link>
                        </li>}

                         {userPermissions.blog && userPermissions.blog.index &&
                        <li className="nav-item">
                            <Link to="/blog">
                                <i className="la la-home"/>
                                <span className="menu-title">Blog</span>
                            </Link>
                        </li>}


                    </ul>
                </div>
            </div>
        );
    }
}

function mapStateToProps({auth}) {
    return {
        userPermissions: auth.permissions,
    }
}

export default connect(mapStateToProps)(LayoutMainMenu)
