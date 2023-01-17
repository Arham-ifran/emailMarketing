import React from 'react';
import { Link } from "react-router-dom";
import routes from "../../routes.js";
import './MainNavigation.css';

function MainNavigation(props) {
	return (
		<React.Fragment>
			<ul className="main-nav list-unstyled">
			{routes.map((route, key) => {
				if (route.showInSideBar) {
					return(
						<li key={key} className={route.path? '':'has-dropdown'}>

							{
								route.path? 
								<Link to={route.path}>{route.name}</Link>
								:
								<>
									<span>{route.name}</span>
									<ul className="list-unstyled sub-menu">
										{route.submenus.map((subroute, subkey) => {
											return (
												<li><Link to={subroute.path}>{subroute.name}</Link></li>
											);
										})}
									</ul>
								</>
							}


						</li>
					);
				}
				else{

				}
			})}
				{/* <li className="active"><Link to="/dashboard">Dashboard</Link></li>
				<li className="has-dropdown">
					<span>Campaigns</span>
					<ul className="list-unstyled sub-menu">
						<li><Link to="/email-campaigns">Email Campaigns</Link></li>
						<li><Link to="/sms-campaigns">SMS Campaigns</Link></li>
					</ul>
				</li>
				<li className="has-dropdown">
					<span>Subscriber Lists</span>
					<ul className="list-unstyled sub-menu">
						<li><Link to="/contacts">All Contacts</Link></li>
						<li><Link to="/mailing-lists">Manage Mailing Lists</Link></li>
					</ul>
				</li>
				<li><Link to="/my-templates">My Templates</Link></li>
				<li><Link to="/split-testing">Split Testing</Link></li>
				<li><Link to="/analytics-and-reports">Analytics &amp; Reports</Link></li>
				<li><Link to="/apis">Api's</Link></li> */}
			</ul>
		</React.Fragment>
	);
}

export default MainNavigation;