import React from 'react';
import { Link } from 'react-router-dom';
import { Dropdown } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faBell } from '@fortawesome/free-regular-svg-icons'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
import ProfilePic from '../../assets/images/user.png';
import SearchIcon from '../../assets/images/search.svg';
import Notification from '../../assets/images/notification.svg';
import SiteLogo from '../../assets/images/logo.svg';

import './Header.css';
function Header(props) {
	return (
		<header id="header" className="header d-flex justify-content-between">
			<div className="mobile-lgoo-holder">
				<div className="d-block d-lg-none">
					<img className="imng-fluid" src={SiteLogo} />
				</div>
			</div>
			<div className="d-flex flex-row-reverse align-items-center">
				<div className="profile-holder">
					<Dropdown>
						<Dropdown.Toggle variant="success" id="dropdown-basic">
							<img src={ProfilePic} alt="prifle pic" />
						</Dropdown.Toggle>
						<Dropdown.Menu>
							<div className="logout-block d-flex">
								<div className="img-holder me-2">
									<img src={ProfilePic} alt="prifle pic" />
								</div>
								<div className="name-holder d-flex flex-column">
									<strong className="user-name mb-2">Aasma</strong>
									<a className="email-id mb-3" href="mailto:aasma@arhamsoft.com">aasma@arhamsoft.com</a>
									<Link to="/signin" className="logout-link text-theme">Log Out</Link>
								</div>
							</div>
						</Dropdown.Menu>
					</Dropdown>
				</div>
				<div className="notification-holder">
					<Dropdown>
						<Dropdown.Toggle variant="success" id="dropdown-basic">
							<img src={Notification} alt="Search" />
							{/* <FontAwesomeIcon icon={faBell} /> */}
						</Dropdown.Toggle>
							<Dropdown.Menu>
								<div className="logout-block d-flex">
									<div class="inner" id="notifyInner">
										<div className="notification-box d-flex align-items-center justify-content-between">
											<span>Notification</span>
											<button type="button" className="cross-btn"><FontAwesomeIcon icon={faTimes} /></button>
										</div>
										<ul className="list-unstyled snooze-des">
											<li><a href="#">Lorem ipsum dolor sit amet, consectetur adipiscing elit</a></li>
											<li><a href="#">Lorem ipsum dolor sit amet, consectetur adipiscing elit</a></li>
											<li><a href="#">Lorem ipsum dolor sit amet, consectetur adipiscing elit</a></li>
											<li><a href="#">Lorem ipsum dolor sit amet, consectetur adipiscing elit</a></li>
										</ul>
									</div>
								</div>
							</Dropdown.Menu>
					</Dropdown>
				</div>
				<div className="search-holder">
				<input className="searchInput" type="text" name="" placeholder="Search" />
					<button className="searchButton" href="#">
						<img src={SearchIcon} alt="Search" />
					</button>
				</div>
			</div>
		</header>
	);
}

export default Header;