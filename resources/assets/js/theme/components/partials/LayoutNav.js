import React, {Component} from 'react';

import {Link} from "react-router-dom";
import {connect} from "react-redux";
import * as authActions from "../../../app/auth/meta/action";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import favicon from "../../../images/favicon.png";
import moment from "moment";
import NotificationConstant from "../../../app/notification/meta/constant";
import ApiService from "../../../services/ApiService";

let notificationUpdater = null;

class LayoutNav extends Component {

    fnNotificationUpdater() {
        /*notificationUpdater = setTimeout(() => {
            ApiService.get(NotificationConstant.resourcePath("list-user-notifications")).then(({data: {data}}) => {
                this.props.actions.updateNotification(data.notifications, data.countNewNotification);
                this.fnNotificationUpdater();
            });
        }, 1000);*/
    }

    componentDidMount() {
        this.fnNotificationUpdater();
    }

    componentWillUnmount() {
        clearTimeout(notificationUpdater);
    }

    render() {

        const listNotifications = this.props.userNotifications.map(noty =>
            <Link
                key={noty.id}
                to={NotificationConstant.getItemLink(noty.notificationtable_id, noty.notificationtable_type)}
                onClick={() => {
                    if (noty.is_read === NotificationConstant.STATUS_UNREAD) {
                        ApiService.post(NotificationConstant.resourcePath(noty.id), {is_read: NotificationConstant.STATUS_READ}).then(({data}) => {
                            this.props.actions.updateNotification(
                                this.props.userNotifications.map(n => n.id === noty.id ? data.data : n),
                                this.props.userNewNotificationCount - 1
                            );
                        });
                    }
                }}
            >
                <div className="media" style={noty.is_read === NotificationConstant.STATUS_UNREAD ? {backgroundColor: '#f5f5f5'} : {}}>
                    <div className="media-left align-self-center"><i className="ft-info icon-bg-circle bg-cyan"/></div>
                    <div className="media-body">
                        <h6 className="media-heading">{noty.content}</h6>
                        <small>
                            <time className="media-meta text-muted">
                                {moment(noty.created_at).format("DD/MM/YYYY HH:mm")}
                            </time>
                            {noty.is_read === NotificationConstant.STATUS_UNREAD &&
                            <span className="badge badge-default badge-danger float-right m-0">Mới</span>}
                        </small>
                    </div>
                </div>
            </Link>
        );

        return (
            <nav className="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-light bg-info navbar-shadow">
                <div className="navbar-wrapper">
                    <div className="navbar-header">
                        <ul className="nav navbar-nav flex-row">
                            <li className="nav-item mobile-menu d-md-none mr-auto"><a
                                className="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                className="ft-menu font-large-1"/></a></li>
                            <li className="nav-item">
                                <Link className="navbar-brand" to="/">
                                    <img className="brand-logo" alt="Redex" src={favicon}/>
                                        <h3 className="brand-text">Red Express</h3>
                                </Link>
                            </li>
                            <li className="nav-item d-md-none">
                                <a className="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i
                                    className="la la-ellipsis-v"/></a>
                            </li>
                        </ul>
                    </div>
                    <div className="navbar-container content">
                        <div className="collapse navbar-collapse" id="navbar-mobile">
                            <ul className="nav navbar-nav mr-auto float-left">
                                <li className="nav-item d-none d-md-block"><a className="nav-link nav-menu-main menu-toggle hidden-xs"
                                                                          href="#"><i className="ft-menu"/></a></li>
                                {/*<li className="nav-item d-none d-md-block">*/}
                                <li className="nav-item d-none">
                                    <a className="nav-link nav-link-expand" href="#"><i className="ficon ft-maximize"/></a>
                                </li>
                                <li className="dropdown nav-item mega-dropdown" style={{display: 'none'}}>
                                    <a className="dropdown-toggle nav-link" href="#" data-toggle="dropdown">Mega</a>
                                    <ul className="mega-dropdown-menu dropdown-menu row">
                                        <li className="col-md-2">
                                            <h6 className="dropdown-menu-header text-uppercase mb-1"><i className="la la-newspaper-o"/>
                                                News</h6>
                                            <div id="mega-menu-carousel-example">
                                                <div>
                                                    <img className="rounded img-fluid mb-1"
                                                         src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/images/slider/slider-2.png"
                                                         alt="First slide"/><a className="news-title mb-0" href="#">Poster Frame PSD</a>
                                                        <p className="news-content">
                                                            <span className="font-small-2">January 26, 2018</span>
                                                        </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li className="col-md-3">
                                            <h6 className="dropdown-menu-header text-uppercase"><i className="la la-random"/> Drill down
                                                menu</h6>
                                            <ul className="drilldown-menu">
                                                <li className="menu-list">
                                                    <ul>
                                                        <li>
                                                            <a className="dropdown-item" href="layout-2-columns.html"><i
                                                                className="ft-file"/> Page layouts & Templates</a>
                                                        </li>
                                                        <li><a href="#"><i className="ft-align-left"/> Multi level menu</a>
                                                            <ul>
                                                                <li><a className="dropdown-item" href="#"><i
                                                                    className="la la-bookmark-o"/> Second level</a></li>
                                                                <li><a href="#"><i className="la la-lemon-o"/> Second level menu</a>
                                                                    <ul>
                                                                        <li><a className="dropdown-item" href="#"><i
                                                                            className="la la-heart-o"/> Third level</a>
                                                                        </li>
                                                                        <li><a className="dropdown-item" href="#"><i
                                                                            className="la la-file-o"/> Third level</a>
                                                                        </li>
                                                                        <li><a className="dropdown-item" href="#"><i
                                                                            className="la la-trash-o"/> Third level</a>
                                                                        </li>
                                                                        <li><a className="dropdown-item" href="#"><i
                                                                            className="la la-clock-o"/> Third level</a>
                                                                        </li>
                                                                    </ul>
                                                                </li>
                                                                <li><a className="dropdown-item" href="#"><i className="la la-hdd-o"/>
                                                                    Second level, third link</a></li>
                                                                <li><a className="dropdown-item" href="#"><i className="la la-floppy-o"/>
                                                                    Second level, fourth link</a></li>
                                                            </ul>
                                                        </li>
                                                        <li>
                                                            <a className="dropdown-item" href="color-palette-primary.html"><i
                                                                className="ft-camera"/> Color palette system</a>
                                                        </li>
                                                        <li><a className="dropdown-item" href="sk-2-columns.html"><i
                                                            className="ft-edit"/> Page starter kit</a></li>
                                                        <li><a className="dropdown-item" href="changelog.html"><i
                                                            className="ft-minimize-2"/> Change log</a></li>
                                                        <li>
                                                            <a className="dropdown-item" href="https://pixinvent.ticksy.com/"><i
                                                                className="la la-life-ring"/> Customer support center</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <li className="col-md-3">
                                            <h6 className="dropdown-menu-header text-uppercase"><i className="la la-list-ul"/> Accordion
                                            </h6>
                                            <div id="accordionWrap" role="tablist" aria-multiselectable="true">
                                                <div className="card border-0 box-shadow-0 collapse-icon accordion-icon-rotate">
                                                    <div className="card-header p-0 pb-2 border-0" id="headingOne" role="tab"><a
                                                        data-toggle="collapse" data-parent="#accordionWrap"
                                                        href="#accordionOne"
                                                        aria-expanded="true" aria-controls="accordionOne">Accordion Item
                                                        #1</a></div>
                                                    <div className="card-collapse collapse show" id="accordionOne" role="tabpanel"
                                                         aria-labelledby="headingOne"
                                                         aria-expanded="true">
                                                        <div className="card-content">
                                                            <p className="accordion-text text-small-3">Caramels dessert chocolate cake
                                                                pastry jujubes bonbon.
                                                                Jelly wafer jelly beans. Caramels chocolate cake liquorice
                                                                cake wafer jelly beans croissant apple pie.</p>
                                                        </div>
                                                    </div>
                                                    <div className="card-header p-0 pb-2 border-0" id="headingTwo" role="tab"><a
                                                        className="collapsed" data-toggle="collapse"
                                                        data-parent="#accordionWrap"
                                                        href="#accordionTwo" aria-expanded="false"
                                                        aria-controls="accordionTwo">Accordion Item #2</a></div>
                                                    <div className="card-collapse collapse" id="accordionTwo" role="tabpanel"
                                                         aria-labelledby="headingTwo"
                                                         aria-expanded="false">
                                                        <div className="card-content">
                                                            <p className="accordion-text">Sugar plum bear claw oat cake chocolate jelly
                                                                tiramisu
                                                                dessert pie. Tiramisu macaroon muffin jelly marshmallow
                                                                cake. Pastry oat cake chupa chups.</p>
                                                        </div>
                                                    </div>
                                                    <div className="card-header p-0 pb-2 border-0" id="headingThree" role="tab"><a
                                                        className="collapsed" data-toggle="collapse"
                                                        data-parent="#accordionWrap"
                                                        href="#accordionThree" aria-expanded="false"
                                                        aria-controls="accordionThree">Accordion Item #3</a></div>
                                                    <div className="card-collapse collapse" id="accordionThree" role="tabpanel"
                                                         aria-labelledby="headingThree"
                                                         aria-expanded="false">
                                                        <div className="card-content">
                                                            <p className="accordion-text">Candy cupcake sugar plum oat cake wafer
                                                                marzipan jujubes
                                                                lollipop macaroon. Cake dragée jujubes donut chocolate
                                                                bar chocolate cake cupcake chocolate topping.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li className="col-md-4">
                                            <h6 className="dropdown-menu-header text-uppercase mb-1"><i className="la la-envelope-o"/>
                                                Contact Us</h6>
                                            <form className="form form-horizontal">
                                                <div className="form-body">
                                                    <div className="form-group row">
                                                        <label className="col-sm-3 form-control-label" htmlFor="inputName1">Name</label>
                                                        <div className="col-sm-9">
                                                            <div className="position-relative has-icon-left">
                                                                <input className="form-control" type="text" id="inputName1"
                                                                       placeholder="John Doe"/>
                                                                    <div className="form-control-position pl-1"><i className="la la-user"/>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="form-group row">
                                                        <label className="col-sm-3 form-control-label" htmlFor="inputEmail1">Email</label>
                                                        <div className="col-sm-9">
                                                            <div className="position-relative has-icon-left">
                                                                <input className="form-control" type="email" id="inputEmail1"
                                                                       placeholder="john@example.com"/>
                                                                    <div className="form-control-position pl-1"><i
                                                                        className="la la-envelope-o"/></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="form-group row">
                                                        <label className="col-sm-3 form-control-label"
                                                               htmlFor="inputMessage1">Message</label>
                                                        <div className="col-sm-9">
                                                            <div className="position-relative has-icon-left">
                                                    <textarea className="form-control" id="inputMessage1" rows="2"
                                                              placeholder="Simple Textarea"/>
                                                                <div className="form-control-position pl-1"><i
                                                                    className="la la-commenting-o"/></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="col-sm-12 mb-1">
                                                            <button className="btn btn-info float-right" type="button"><i
                                                                className="la la-paper-plane-o"/> Send
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                                <li className="nav-item nav-search" style={{display: 'none'}}>
                                    <a className="nav-link nav-link-search" href="#"><i className="ficon ft-search"/></a>
                                    <div className="search-input">
                                        <input className="input" type="text" placeholder="Explore Modern..."/>
                                    </div>
                                </li>
                            </ul>
                            <ul className="nav navbar-nav float-right">
                                <li className="dropdown dropdown-user nav-item">
                                    <a className="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                        <span className="mr-1">Hello,
                                          <span className="user-name text-bold-700">{this.props.authUser.name}</span>
                                        </span>
                                        <span className="avatar avatar-online">
                  <img src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/images/portrait/small/avatar-s-19.png"
                       alt="avatar"/><i/></span>
                                    </a>
                                    <div className="dropdown-menu dropdown-menu-right">
                                        {/*<a className="dropdown-item" href="#"><i className="ft-user"/> Edit Profile</a>*/}
                                        {/*<a className="dropdown-item" href="#"><i className="ft-mail"/> My Inbox</a>*/}
                                        {/*<a className="dropdown-item" href="#"><i className="ft-check-square"/> Task</a>*/}
                                        {/*<a className="dropdown-item" href="#"><i className="ft-message-square"/> Chats</a>*/}
                                        {/*<div className="dropdown-divider"/>*/}
                                        <a className="dropdown-item" onClick={(e) => {
                                            e.preventDefault();
                                            this.props.actions.logout();
                                        }}>
                                            <i className="ft-power"/> Đăng xuất
                                        </a>
                                    </div>
                                </li>
                                <li className="dropdown dropdown-language nav-item" style={{display: 'none'}}>
                                    <a className="dropdown-toggle nav-link"
                                                                                   id="dropdown-flag" href="#"
                                                                                   data-toggle="dropdown"
                                                                                   aria-haspopup="true" aria-expanded="false"><i
                                    className="flag-icon flag-icon-gb"/><span className="selected-language"/></a>
                                    <div className="dropdown-menu" aria-labelledby="dropdown-flag"><a className="dropdown-item" href="#"><i
                                        className="flag-icon flag-icon-gb"/> English</a>
                                        <a className="dropdown-item" href="#"><i className="flag-icon flag-icon-fr"/> French</a>
                                        <a className="dropdown-item" href="#"><i className="flag-icon flag-icon-cn"/> Chinese</a>
                                        <a className="dropdown-item" href="#"><i className="flag-icon flag-icon-de"/> German</a>
                                    </div>
                                </li>
                                <li className="dropdown dropdown-notification nav-item">
                                    <a className="nav-link nav-link-label" href="#" data-toggle="dropdown"><i className="ficon ft-bell"/>
                                        {this.props.userNewNotificationCount > 0 && <span className="badge badge-pill badge-default badge-danger badge-default badge-up badge-glow">{this.props.userNewNotificationCount}</span>}
                                    </a>
                                    <ul className="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                        <li className="dropdown-menu-header">
                                            <h6 className="dropdown-header m-0">
                                                <span className="grey darken-2">Thông báo</span>
                                            </h6>
                                            {this.props.userNewNotificationCount > 0 ? <span className="notification-tag badge badge-default badge-danger float-right m-0">{this.props.userNewNotificationCount} mới</span> : <span className="notification-tag badge badge-default float-right m-0">-</span>}
                                        </li>
                                        <li className="scrollable-container media-list w-100" style={{overflowY: 'auto'}}>
                                            {this.props.userNotifications.length > 0 ? listNotifications : <h5 className="text-center mt-2 mb-3">Không có thông báo nào</h5>}
                                        </li>
                                        <li className="dropdown-menu-footer">
                                            <Link to="/notification" className="dropdown-item text-muted text-center">
                                                Xem tất cả các thông báo
                                            </Link>
                                        </li>
                                    </ul>
                                </li>
                                <li className="dropdown dropdown-notification nav-item" style={{display: 'none'}}>
                                    <a className="nav-link nav-link-label" href="#" data-toggle="dropdown"><i
                                        className="ficon ft-mail"> </i></a>
                                    <ul className="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                        <li className="dropdown-menu-header">
                                            <h6 className="dropdown-header m-0">
                                                <span className="grey darken-2">Messages</span>
                                            </h6>
                                            <span className="notification-tag badge badge-default badge-warning float-right m-0">4 New</span>
                                        </li>
                                        <li className="scrollable-container media-list w-100">
                                            <a href="javascript:void(0)">
                                                <div className="media">
                                                    <div className="media-left">
                        <span className="avatar avatar-sm avatar-online rounded-circle">
                          <img src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/images/portrait/small/avatar-s-19.png"
                               alt="avatar"/><i/></span>
                                                    </div>
                                                    <div className="media-body">
                                                        <h6 className="media-heading">Margaret Govan</h6>
                                                        <p className="notification-text font-small-3 text-muted">I like your portfolio,
                                                            let's start.</p>
                                                        <small>
                                                            <time className="media-meta text-muted"
                                                                  dateTime="2015-06-11T18:29:20+08:00">Today
                                                            </time>
                                                        </small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="javascript:void(0)">
                                                <div className="media">
                                                    <div className="media-left">
                        <span className="avatar avatar-sm avatar-busy rounded-circle">
                          <img src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/images/portrait/small/avatar-s-2.png"
                               alt="avatar"/><i/></span>
                                                    </div>
                                                    <div className="media-body">
                                                        <h6 className="media-heading">Bret Lezama</h6>
                                                        <p className="notification-text font-small-3 text-muted">I have seen your work,
                                                            there is</p>
                                                        <small>
                                                            <time className="media-meta text-muted"
                                                                  dateTime="2015-06-11T18:29:20+08:00">Tuesday
                                                            </time>
                                                        </small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="javascript:void(0)">
                                                <div className="media">
                                                    <div className="media-left">
                        <span className="avatar avatar-sm avatar-online rounded-circle">
                          <img src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/images/portrait/small/avatar-s-3.png"
                               alt="avatar"/><i/></span>
                                                    </div>
                                                    <div className="media-body">
                                                        <h6 className="media-heading">Carie Berra</h6>
                                                        <p className="notification-text font-small-3 text-muted">Can we have call in
                                                            this week ?</p>
                                                        <small>
                                                            <time className="media-meta text-muted"
                                                                  dateTime="2015-06-11T18:29:20+08:00">Friday
                                                            </time>
                                                        </small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="javascript:void(0)">
                                                <div className="media">
                                                    <div className="media-left">
                        <span className="avatar avatar-sm avatar-away rounded-circle">
                          <img src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/images/portrait/small/avatar-s-6.png"
                               alt="avatar"/><i/></span>
                                                    </div>
                                                    <div className="media-body">
                                                        <h6 className="media-heading">Eric Alsobrook</h6>
                                                        <p className="notification-text font-small-3 text-muted">We have project party
                                                            this saturday.</p>
                                                        <small>
                                                            <time className="media-meta text-muted"
                                                                  dateTime="2015-06-11T18:29:20+08:00">last month
                                                            </time>
                                                        </small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li className="dropdown-menu-footer"><a className="dropdown-item text-muted text-center"
                                                                            href="javascript:void(0)">Read all messages</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        );
    }
}

function mapStateToProps({auth}) {
    return {
        authUser: auth.user,
        userNotifications: auth.notifications,
        userNewNotificationCount: auth.newNotificationCount,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, authActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(LayoutNav)
