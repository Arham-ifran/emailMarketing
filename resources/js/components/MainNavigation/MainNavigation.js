import React, { useState, useEffect } from "react";
import { Link, useLocation } from "react-router-dom";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import routes from "../../routes.js";
import { faSortDown } from '@fortawesome/free-solid-svg-icons'
import "./MainNavigation.css";
import GetUserPackage from "../Auth/GetUserPackage.js";
import { withTranslation } from 'react-i18next';
function MainNavigation(props) {
    const { t } = props;
    const location = useLocation();
    const [route, setRoute] = useState(location.pathname);

    const [userPackage, setUserPackage] = useState({});
    const [canSplitTest, setCanSplitTest] = useState(0);

    const [canDesign, setCanDesign] = useState(0);
    const [canImportBasic, setCanImportBasic] = useState(0);
    const [canImportHTML, setCanImportHTML] = useState(0);
    const [canAddSMS, setCanAddSMS] = useState(0);

    const [haveApiAccess, setHaveApiAccess] = useState(0);

    const [canViewReport, setCanViewReport] = useState(0);

    useEffect(() => {
        const load = () => {
            if (userPackage != {}) {
                if (userPackage.features) {
                    if (Object.keys(userPackage.features).findIndex(val => val === "12") >= 0) { // split allowed
                        // console.log(Object.values(userPackage.features)[Object.keys(userPackage.features).findIndex(val => val === "12")])
                        setCanSplitTest(true)
                    } else {
                        setCanSplitTest(false)
                    }
                    if (Object.keys(userPackage.features).findIndex(val => val === "5") >= 0) { // import basic
                        setCanImportBasic(true)
                    } else {
                        setCanImportBasic(false)
                    }
                    if (Object.keys(userPackage.features).findIndex(val => val === "6") >= 0) { // design
                        setCanDesign(true)
                    } else {
                        setCanDesign(false)
                    }
                    if (Object.keys(userPackage.features).findIndex(val => val === "7") >= 0) { // import html
                        setCanImportHTML(true)
                    } else {
                        setCanImportHTML(false)
                    }
                    if (Object.keys(userPackage.features).findIndex(val => val === "8") >= 0) { // add sms
                        setCanAddSMS(true)
                    } else {
                        setCanAddSMS(false)
                    }
                    if (Object.keys(userPackage.features).findIndex(val => val === "13") >= 0) { // api access
                        setHaveApiAccess(true)
                    } else {
                        setHaveApiAccess(false)
                    }
                    if (Object.keys(userPackage.features).findIndex(val => val === "9") >= 0) { // view report
                        setCanViewReport(true)
                    } else {
                        setCanViewReport(false)
                    }
                }
            }
        }
        load();
    }, [userPackage])

    return (
        <React.Fragment>
            <GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
            <ul className="main-nav list-unstyled" style={{ fontFamily: "Sofia Pro" }}>
                <li className={route == "/dashboard" ? "active" : ""}><Link to="/dashboard">{t('dashboard')}</Link></li>
                {routes.map((route, key) => {
                    if (route.showInSideBar) {
                        return (
                            <li
                                key={key}
                                className={route.path ? "" : "has-dropdown"}
                            >
                                {route.path ? (
                                    <Link to={route.path}>{route.name}</Link>
                                ) : (
                                    <>
                                        <span>{route.name}</span>
                                        <ul className="list-unstyled sub-menu">
                                            {route.submenus.map(
                                                (subroute, subkey) => {
                                                    return (
                                                        <li key={subkey}>
                                                            <Link
                                                                to={
                                                                    subroute.path
                                                                }
                                                            >
                                                                {subroute.name}
                                                            </Link>
                                                        </li>
                                                    );
                                                }
                                            )}
                                        </ul>
                                    </>
                                )}
                            </li>
                        );
                    } else {
                    }
                })}

                {/* Temporary routes */}
                <li className="has-dropdown">
                    <span className={(route == "/mailing-lists" || route == "/contacts" ? "dropdown-active" : "")} >{t('Subscriber Lists')}
                        <FontAwesomeIcon icon={faSortDown} className="arrow-icon"></FontAwesomeIcon></span>

                    <ul className={"list-unstyled sub-menu " + (route == "/mailing-lists" || route == "/contacts" ? "d-block" : "")}>
                        <li className={route == "/contacts" ? "active" : ""}>
                            <Link to="/contacts">{t('All Contacts')}</Link>
                        </li>
                        <li className={route == "/mailing-lists" ? "active" : ""}>
                            <Link to="/mailing-lists">
                                {t('Manage Mailing Lists')}
                            </Link>
                        </li>
                    </ul>
                </li>
                <li className="has-dropdown">
                    <span className={(route == "/email-campaign/list" || route == "/sms-campaign" ? "dropdown-active" : "")}>{t('Campaigns')}
                        <FontAwesomeIcon icon={faSortDown} className="arrow-icon"></FontAwesomeIcon></span>
                    <ul className={"list-unstyled sub-menu " + (route == "/email-campaign/list" || route == "/sms-campaign" ? "d-block" : "")}>
                        <li className={route == "/email-campaign/list" ? "active" : ""}><Link to="/email-campaign/list">{t('Email Campaigns')}</Link></li>
                        <li className={route == "/sms-campaign" ? "active" : ""}><Link to="/sms-campaign">{t('SMS Campaigns')}</Link></li>
                    </ul>
                </li>
                {canSplitTest ? <li className={route == "/split-testing/list" ? "active" : ""}><Link to="/split-testing/list">{t('Split Testing')}</Link></li> : ""}
                {canDesign || canImportBasic || canImportHTML || canAddSMS ? <li className={route == "/template/list" ? "active" : ""}><Link to="/template/list">{t('My Templates')}</Link></li> : ""}
                {canViewReport ? <li className={route == "/analytics-and-reports" ? "active" : ""}><Link to="/analytics-and-reports">{t('Analytics & Reports')}</Link></li> : ""}
                {haveApiAccess ? <li className={route == "/apis" ? "active" : ""}><Link to="/apis">{t('APIs')}</Link></li> : ""}
                {/* 
				<li className="has-dropdown">
					<span>Subscriber Lists</span>
					<ul className="list-unstyled sub-menu">
						<li><Link to="/contacts">All Contacts</Link></li>
						<li><Link to="/mailing-lists">Manage Mailing Lists</Link></li>
					</ul>
				</li>
				<li><Link to="/my-templates">My Templates</Link></li>
				<li><Link to="/split-testing">Split Testing</Link></li>
				<li><Link to="/analytics-and-reports">Analytics & Reports</Link></li>
				<li><Link to="/apis">Api's</Link></li> */}
            </ul>
        </React.Fragment>
    );
}

export default withTranslation()(MainNavigation);