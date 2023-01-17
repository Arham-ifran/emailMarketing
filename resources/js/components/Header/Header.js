import React, { useEffect, useState } from 'react';
import { withTranslation } from 'react-i18next'
import LanguageSelector from '../helpers/LanguageSelector';

import { Link } from 'react-router-dom';
import { Dropdown } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faBell } from '@fortawesome/free-regular-svg-icons'
import { faChevronCircleDown } from '@fortawesome/free-solid-svg-icons'
import { faUser } from '@fortawesome/free-solid-svg-icons'
import { faSignOutAlt } from '@fortawesome/free-solid-svg-icons'
import { faCircle } from '@fortawesome/free-solid-svg-icons'
import { faArrowUp } from '@fortawesome/free-solid-svg-icons'
import { faMoneyBill } from '@fortawesome/free-solid-svg-icons'
import { faBoxOpen } from '@fortawesome/free-solid-svg-icons'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
import { faHome } from '@fortawesome/free-solid-svg-icons'

import { faXmark } from '@fortawesome/free-solid-svg-icons'
import { faMinusCircle } from '@fortawesome/free-solid-svg-icons'
import ProfilePic from '../../assets/images/user.png';
import SearchIcon from '../../assets/images/search.svg';
import Notification from '../../assets/images/notification.svg';
import SiteLogo from '../../assets/images/main-logo.svg';
import Spinner from "../includes/spinner/Spinner";
import SetAuthToken from "../helpers/SetAuthToken";
import GetUserPackage from "../Auth/GetUserPackage.js";

import './Header.css';
function Header(props) {

	const [loading, setLoading] = useState(false);
	const [notifications, setNotifications] = useState([]);
	const [username, setUsername] = useState('');
	const [profileImagePath, setProfileImagePath] = useState('');
	const [count, setCount] = useState(0);
	const [seen, setSeen] = useState(0);
	const [errors, setErrors] = useState([]);
	const [showPkgUnpaid, setShowPkgUnpaid] = useState(false);
	const [showPkgPaid, setShowPkgPaid] = useState(false);


	const getNotifications = () => {
		axios
			.get(
				"/api/get-notifications?lang=" + localStorage.lang
			)
			.then((response) => {
				setNotifications(response.data.notifications.data);
				setSeen(!(response.data.notseen));
				setCount(response.data.notseen);
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
			});
	};

	const showMore = () => {
		window.location.href = "/notifications";
	}

	const [userPackage, setUserPackage] = useState({});
	const [canViewReport, setCanViewReport] = useState(0);

	useEffect(() => {
		const load = () => {
			if (userPackage != {}) {
				if (userPackage.features) {
					if (Object.keys(userPackage.features).findIndex(val => val === "9") >= 0) { // view report
						setCanViewReport(true)
					} else {
						setCanViewReport(false)
					}
				}
			}
		}
		load();
		getNotifications();
		axios.get('/api/auth/profile?lang=' + localStorage.lang)
			.then(response => {
				let data = response.data.data;
				setProfileImagePath(data.profile_image_path);
				setUsername(data.name);
				if (data.unpaid_package_email_by_admin == 1) {
					setShowPkgUnpaid(true);
				}
				if (data.package_updated_by_admin == 1) {
					setShowPkgPaid(true);
				}
			})
			.catch(error => {
			})
	}, [userPackage])

	const readNotifications = () => {
		// setLoading(true);
		axios.get("/api/read-notifications?lang=" + localStorage.lang)
			.then((response) => {
				// setLoading(false);
				getNotifications();
			})
			.catch((error) => {
				if (error.response.data.errors) {
					// setLoading(false);
					setErrors(error.response.data.errors);
				}
			});
	}

	const readNotification = (id, link) => {
		// setLoading(true);
		axios.get("/api/read-notification/" + id + "?lang=" + localStorage.lang)
			.then((response) => {
				// setLoading(false);
				getNotifications();
				canViewReport ? window.location.href = link : null;

			})
			.catch((error) => {
				if (error.response.data.errors) {
					// setLoading(false);
					setErrors(error.response.data.errors);
				}
			});
	}

	const deleteNotification = (id) => {
		setLoading(true);
		axios.get("/api/delete-notification/" + id + "?lang=" + localStorage.lang)
			.then((response) => {
				setLoading(false);
				getNotifications();
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setLoading(false);
					setErrors(error.response.data.errors);
				}
			});
	}

	const hidePaymentNoti = () => {
		setShowPkgUnpaid(false);
		axios.get("/api/read-payment-notification")
			.then((response) => { });
	}

	const hidePaidNoti = () => {
		setShowPkgPaid(false);
		axios.get("/api/read-paid-notification")
			.then((response) => { });
	}

	const handleLogout = (event) => {
		setLoading(true);

		if (localStorage.jwt_token) {
			setErrors([])

			axios.post('/api/auth/logout?lang=' + localStorage.lang)
				.then(response => {

					localStorage.removeItem('jwt_token');
					localStorage.removeItem('user_name');
					localStorage.removeItem('country');
					//localStorage.removeItem('profile_image_path');
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

	const getTextHeading = (text) => {
		return text.match(/'([^']+)'/)[1];
	}

	const getTextRemainder = (text) => {
		return text.replace(" '" + text.match(/'([^']+)'/)[1] + "'", '');
		// return text;
	}

	const closepopup = () => {
		document.getElementsByClassName('dropdown-menu')[0].classList.toggle("show");
	}
	const { t } = props;
	return (
		<>
			<header id="header" className="header d-flex justify-content-between">
				<GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
				{loading ? <Spinner /> : null}
				<div className="mobile-lgoo-holder">
					<div className="d-block d-lg-none">
						<Link to="/">
							<img className="img-fluid" src={SiteLogo} alt="" />
						</Link>
					</div>
				</div>
				<div className="d-flex align-items-center">
					<div className="notification-holder styled-notify">
						<Dropdown>
							<Dropdown.Toggle variant="success" id="dropdown-basic">
								{/* <span onClick={() => { readNotifications(); setSeen(true) }}> */}
								<span>
									<img src={Notification} alt="Notification" />
								</span>
								{/* {!seen ? <small style={{ fontSize: '40%', color: 'red' }} > {<FontAwesomeIcon icon={faCircle} />} </small> : ""} */}
								{!seen && count > 0 ? <span className='mark'>{count}</span> : ""}
							</Dropdown.Toggle>
							<Dropdown.Menu>
								<div className="logout-block d-flex">
									<div className="inner" id="notifyInner">
										<div className="notification-box d-flex align-items-center justify-content-between">
											<span>{t('Notification')}</span>
											{/* <button type="button" className="cross-btn"><FontAwesomeIcon icon={faTimes}  /></button> */}
										</div>
										<div className="notification-end d-flex align-items-center justify-content-end">
											<span onClick={() => { readNotifications(); getNotifications(); }}>{t('mark_all_as_read')}</span>
										</div>
										<ul className="list-unstyled snooze-des">
											{notifications.map(notification => (
												<li key={notification.id} className={notification.is_read ? "" : "newNotification"} >
													<a onClick={() => readNotification(notification.id, notification.redirect_to)}>
														<span className="notifcation-heading">{getTextHeading(notification.notification_text)} </span>
														{t(getTextRemainder(notification.notification_text))}
													</a>
													<button type="button" className="cross-btn " onClick={() => deleteNotification(notification.id)} ><FontAwesomeIcon icon={faTimes} /></button>
												</li>
											))}
										</ul>
										{notifications.length ?
											<div className="notification-end d-flex align-items-center justify-content-between">
												<span onClick={showMore}>{t('See more')}</span>
											</div>
											:
											<div className="no-notify-des d-flex align-items-center justify-content-between">
												<span>{t('No notifications yet')}</span>
											</div>
										}
									</div>
								</div>
							</Dropdown.Menu>
						</Dropdown>
					</div>
					<div className='d-flex upgrade-btn d-md-block d-none'>
						<Link to="/packages/upgrade-package">
							<button type="submit" class="btn btn-primary btn btn-primary me-md-3 me-1"><span className='me-1'><FontAwesomeIcon icon={faArrowUp} /></span><span>{t('Upgrade Package')}</span></button>
						</Link>
					</div>
					<div className="header-dropdown-holder">
						<div className="profile-holder">
							<span className="profile-img-wrap">
								<img src={profileImagePath ? profileImagePath : ProfilePic} alt="prifle pic" />
							</span>
						</div>
						<div className="search-holder">
							<div className="name-holder d-flex align-items-center h-100">
								{username}
							</div>
							{/* <input className="searchInput" type="text" name="" placeholder="Search" />
						<button className="searchButton" href="#">
							<img src={SearchIcon} alt="Search" />
						</button> */}
						</div>
						<Dropdown>
							<Dropdown.Toggle variant="success">
								<FontAwesomeIcon icon={faChevronCircleDown} />
							</Dropdown.Toggle>
							<Dropdown.Menu>
								<div className="logout-block d-flex flex-column">
									<Link to="/" className="logout-link text-theme w-100">
										<span><FontAwesomeIcon icon={faHome} /></span>
										<span className="drop-content">{t('Home')}</span>
									</Link>
									<Link to="/profile" className="logout-link text-theme w-100">
										<span><FontAwesomeIcon icon={faUser} /></span>
										<span className="drop-content">{t('Profile')}</span>
									</Link>
									<Link to="/packages/billing" className="logout-link text-theme w-100">
										<span><FontAwesomeIcon icon={faBoxOpen} /></span>
										<span className="drop-content">{t('Billing')}</span>
									</Link>
									<Link to="/packages/payments" className="logout-link text-theme w-100">
										<span><FontAwesomeIcon icon={faMoneyBill} /></span>
										<span className="drop-content">{t('Payments')}</span>
									</Link>
									<Link to="/packages/upgrade-package" className="logout-link text-theme w-100">
										<span><FontAwesomeIcon icon={faArrowUp} /></span>
										<span className="drop-content">{t('Upgrade Package')}</span>
									</Link>
									<Link to="#" onClick={handleLogout} className="logout-link text-theme w-100">
										<span><FontAwesomeIcon icon={faSignOutAlt} /></span>
										<span className="drop-content">{t('Log Out')}</span>
									</Link>
								</div>
							</Dropdown.Menu>
						</Dropdown>
					</div>
					<LanguageSelector />
				</div>

			</header>
			{showPkgUnpaid ?
				<div className='pkg-info payment-procees-msg mb-5 text-center rounded d-flex juistify-content-between'>
					<div className='d-flex'>
						{t('please_check_your_email_for_payment_process_against_package_updated_by_administrator')}
					</div>
					<div className='d-flex flex-fill justify-content-end align-items-center'>
						<span className='cross-sign' onClick={() => hidePaymentNoti()}> ×</span>
					</div>
				</div>
				: ""
			}
			{showPkgPaid ?
				<div className='pkg-info payment-procees-msg mb-5 text-center rounded d-flex juistify-content-between'>
					<div className='d-flex'>
						{t('your_package_has_been_updated_free_of_cost_by_the_administrator')}
					</div>
					<div className='d-flex flex-fill justify-content-end align-items-center'>
						<span className='cross-sign' onClick={() => hidePaidNoti()}> ×</span>
					</div>
				</div>
				: ""
			}
		</>
	);
}
export default withTranslation()(Header);