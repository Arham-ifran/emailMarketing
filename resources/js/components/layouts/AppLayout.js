import React, { useState } from "react";
import { Link } from "react-router-dom";
import SiteLogo from '../../assets/images/main-logo.svg';
import MainNavigation from '../MainNavigation/MainNavigation';
import Header from '../Header/Header';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faBars } from '@fortawesome/free-solid-svg-icons'
import './AppLayout.css';

const AppLayout = ({ children }) => {
	const [isActive, setActive] = useState(false);

	const toggleClass = () => {
		setActive(!isActive);
	};

	return (
		<div className={"wrapper d-flex " + localStorage.lang}>
			<aside id="sidebar" className={isActive ? 'sidebar show' : 'sidebar'}>
				<div className="menu-icon cur-poi" onClick={toggleClass}>
					<FontAwesomeIcon icon={faBars} />
				</div>
				<strong className="logo w-100 d-flex justify-content-center">
					<Link to="/">
						<img className="img-fluid" src={SiteLogo} alt="" />
					</Link>
				</strong>
				<MainNavigation />
				{/* <div className="link-lagout-holder">
					<Link to="/signin" className="link-logout text-theme">Logout</Link>
				</div> */}
			</aside>
			<div id="content " className="content flex-fill">
				<Header />
				{children}
			</div>
		</div>
	);
};
export default AppLayout;
