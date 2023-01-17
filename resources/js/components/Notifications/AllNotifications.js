import React, { useEffect, useState } from 'react';
import { Container, Table } from "react-bootstrap";
import { Link } from 'react-router-dom';
import { Dropdown, Row } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faBell } from '@fortawesome/free-regular-svg-icons'
import { faCircle } from '@fortawesome/free-solid-svg-icons'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
import ProfilePic from '../../assets/images/user.png';
import SearchIcon from '../../assets/images/search.svg';
import Notification from '../../assets/images/notification.svg';
import SiteLogo from '../../assets/images/main-logo.svg';
import Spinner from "../includes/spinner/Spinner";
import SetAuthToken from "../helpers/SetAuthToken";
import Pagination from "react-js-pagination";
import { withTranslation } from 'react-i18next';
import './AllNotifications.css';
import GetUserPackage from "../Auth/GetUserPackage.js";
function AllNotifications(props) {
	const { t } = props;
	const [loading, setLoading] = useState(false);
	const [notifications, setNotifications] = useState([]);
	const [pageNumber, setPageNumber] = useState(1);
	const [perPage, setperPage] = useState(0);
	const [totalItems, setTotalItems] = useState(0);
	const [pageRange, setPageRange] = useState(5);


	const getNotifications = () => {
		setLoading(true);
		axios
			.get(
				"/api/get-notifications?page=" +
				pageNumber + "&lang=" + localStorage.lang
			)
			.then((response) => {
				setLoading(false);
				if (!$.trim(response.data.notifications.data) && pageNumber !== 1) {
					setPageNumber(pageNumber - 1);
					setFilter(!filter);
				}
				// console.log(response.data);
				// console.log(response.data.notifications.data);
				setNotifications(response.data.notifications.data);
				setperPage(response.data.notifications.per_page);
				setTotalItems(response.data.notifications.total);
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setLoading(false);
					setErrors(error.response.data.errors);
				}
			});
	};

	useEffect(() => {
		getNotifications();
		// readNotifications()
	}, [pageNumber]);

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
	}, [userPackage])

	const readNotifications = () => {
		setLoading(true);
		axios.get("/api/read-notifications?lang=" + localStorage.lang)
			.then((response) => {
				setLoading(false);
				location.reload();
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setLoading(false);
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

	const getTextHeading = (text) => {
		return text.match(/'([^']+)'/)[1];
	}

	const getTextRemainder = (text) => {
		return text.replace(" '" + text.match(/'([^']+)'/)[1] + "'", '');
		// return text;
	}
	return (

		<React.Fragment>
			<GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
			{loading ? <Spinner /> : null}
			<section className="right-canvas email-campaign desktop-notifcation-page">
				<Container fluid>
					<div className="d-flex flex-column mb-3">
						<div className='d-flex align-items-center justify-content-between'>
							<div className="page-title">
								<h1>{t('Notifications')}</h1>
							</div>

							<div className="page-title">
								<button type="button" class="btn btn-secondary" onClick={() => readNotifications()}><span>{t('mark_all_as_read')}</span></button>
							</div>
						</div>

						{notifications.length ?
							<div className="status-table mt-3">
								<ul className="list-unstyled snooze-des">
									{notifications.map(notification =>
									(<>
										<a >
											<li key={notification.id} className={"m-1 p-2 " + (notification.is_read ? "oldNotification" : "newNotification")} >
												<div className='cross-btn-wrapper d-flex justify-content-end btn-style-note' onClick={() => deleteNotification(notification.id)}>
													<button type="button" className="cross-btn" ><FontAwesomeIcon icon={faTimes} /></button>
												</div>
												<div onClick={() => readNotification(notification.id, notification.redirect_to)}>
													<span className="notifcation-heading">{getTextHeading(notification.notification_text)} </span>
													<div className='d-flex'>
														{t(getTextRemainder(notification.notification_text))}

													</div>
												</div>
											</li>
										</a>
									</>
									)
									)}
								</ul>
								{/* pagination starts here */}
								<div className="mt-2">
									<Pagination
										activePage={pageNumber}
										itemsCountPerPage={perPage}
										totalItemsCount={totalItems}
										pageRangeDisplayed={pageRange}
										onChange={(e) => setPageNumber(e)}
									/>
								</div>
								{/* pagination ends here */}
							</div>
							:
							<Row className="row d-flex align-items-center _height">
								<div className="d-flex justify-content-center ">
									<h5>{t('You have No notification yet')}</h5>
								</div>
							</Row>
						}
					</div>
				</Container>
			</section>
		</React.Fragment>
	);
}
export default withTranslation()(AllNotifications);