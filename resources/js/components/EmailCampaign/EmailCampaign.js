import React, { useState } from 'react';
import PrivateRoute from '../helpers/PrivateRoute';
import CreateEmailCampaign from '../EmailCampaign/CreateEmailCampaign';
import EmailCampaignList from './EmailCampaignList';
import { Route, Switch } from "react-router-dom";
import Newreport from "./Newreport"
import ViewCampaign from './ViewCampaign';
//import './Dashboard.css';
function EmailCampaign(props) {
	const [count, setCount] = useState(10);
	return (
		<>
			<Switch>
				<PrivateRoute path="/email-campaign/create" component={CreateEmailCampaign} />
				<PrivateRoute path="/email-campaign/:id/edit" component={CreateEmailCampaign} />
				<PrivateRoute path="/email-campaign/view/:id" component={ViewCampaign} />
				<PrivateRoute path="/email-campaign/:id/report" component={Newreport} />
				<PrivateRoute path="/email-campaign/list" component={EmailCampaignList} />
			</Switch>
		</>
	);
}

export default EmailCampaign;