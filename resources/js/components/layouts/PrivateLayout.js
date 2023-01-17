import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import SiteLogo from '../../assets/images/main-logo.svg';
import MainNavigation from '../MainNavigation/MainNavigation';
import Header from '../Header/Header';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faBars } from '@fortawesome/free-solid-svg-icons'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
import './AppLayout.css';
import { rest } from "lodash";
import SetAuthToken from "../helpers/SetAuthToken";
import GetUserPackage from '../Auth/GetUserPackage';
import axios from "axios";

const PrivateLayout = ({ showSideBar, children }) => {
	// console.log(showSideBar);
	const [isActive, setActive] = useState(false);
	const [loading, setLoading] = useState(false);
	const [errors, setErrors] = useState([]);

	const toggleClass = () => {
		setActive(!isActive);
	};

	const handleLogout = (event) => {
		setLoading(true);

		if (localStorage.jwt_token) {
			setErrors([])

			axios.post('/api/auth/logout')
				.then(response => {

					localStorage.removeItem('jwt_token');
					localStorage.removeItem('user_name');
					localStorage.removeItem('profile_image_path');
					localStorage.removeItem('country');
					SetAuthToken(false);
					window.location.href = "/signin";
				})
				.catch(error => {
				})
		}
		else {
			window.location.href = "/signin";
		}
	}

	const [userPackage, setUserPackage] = useState({});

	useEffect(() => {
		const load = () => {
			if (userPackage != {}) {
				let parseUriSegment = window.location.pathname.split("/")
				console.log(parseUriSegment);
				if (userPackage.features) {

					//api page
					if (Object.keys(userPackage.features).findIndex(val => val === "13") >= 0) { // api access
					} else {
						if (parseUriSegment[1] == 'apis') {
							window.location = "/dashboard"
						}
					}

					// report route
					if (Object.keys(userPackage.features).findIndex(val => val === "12") >= 0) { // can split test
					} else {
						if (parseUriSegment[1] == 'split-testing') {
							window.location = "/dashboard"
						}
					}

					// report route
					if (Object.keys(userPackage.features).findIndex(val => val === "9") >= 0) { // view report
					} else {
						if (parseUriSegment[1] == 'analytics-and-reports' && parseUriSegment[3] == 'report') {
							window.location = "/dashboard"
						}
					}
					if (Object.keys(userPackage.features).findIndex(val => val === "6") >= 0) { // design
					} else {
						if (parseUriSegment[1] == 'email-template' && parseUriSegment[2] == 'create') {
							window.location = "/dashboard"
						}
					}
					if (Object.keys(userPackage.features).findIndex(val => val === "7") >= 0) { // import html
					} else {
						if (parseUriSegment[1] == 'email-template' && parseUriSegment[2] == 'import') {
							window.location = "/dashboard"
						}
					}
					if (Object.keys(userPackage.features).findIndex(val => val === "8") >= 0) { // add sms
					} else {
						if (parseUriSegment[1] == 'sms-template' && parseUriSegment[2] == 'create') {
							window.location = "/dashboard"
						}
					}
				}
			}
		}
		load();
		const checkUserType = () => {
			if (localStorage.userType == 'admin') {
				axios.get("/admin/logout").then((errors) => { });
				localStorage["userType"] = 'user';
			} else {
				localStorage["userType"] = 'user';
			}
		}
		checkUserType();
	}, [userPackage])

	return (
		<div className={"wrapper d-flex campaign-overly-holder " + localStorage.lang}>
			<GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
			{showSideBar == false ?
				"" :
				<aside id="sidebar" className={isActive ? 'sidebar show' : 'sidebar'}>
					{isActive ?
						<span className="cross-btn-sidebar">
							<FontAwesomeIcon className="no-cross" icon={faTimes} onClick={toggleClass} />
						</span>
						:
						<div className="menu-icon cur-poi" onClick={toggleClass}>
							<FontAwesomeIcon icon={faBars} />
						</div>
					}
					<strong className="logo w-100 d-flex justify-content-center">
						<Link to="/">
							<img className="img-fluid" src={SiteLogo} alt="" />
						</Link>
					</strong>
					<MainNavigation />
					{/* <div className="link-lagout-holder">
						<Link onClick={handleLogout} className="link-logout text-theme">Logout</Link>
					</div> */}
				</aside>
			}

			<div id="content " className=" content flex-fill campaign-overly-holder">
				<Header />
				{children}
			</div>
		</div>
	);
};
export default PrivateLayout;
