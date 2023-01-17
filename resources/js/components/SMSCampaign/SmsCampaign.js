import React from 'react';
import PrivateRoute from '../helpers/PrivateRoute';
import { Switch } from "react-router-dom";

import AllSmsCampaigns from "./AllSmsCampaigns";
import CreateSmsCampaign from "./CreateSmsCampaign";
import Newreport from './Newreport';
import ViewCampaign from './ViewCampaign';

function SmsCampaign(props) {
	return (
		<Switch>
			<PrivateRoute path="/sms-campaign" exact component={AllSmsCampaigns} />
			<PrivateRoute path="/sms-campaign/create/" exact component={CreateSmsCampaign} />
			<PrivateRoute path="/sms-campaign/:campaignId/edit" exact component={CreateSmsCampaign} />
			<PrivateRoute path="/sms-campaign/:campaignId?/report" exact component={Newreport} />
			<PrivateRoute path="/sms-campaign/view/:campaignId" exact component={ViewCampaign} />
		</Switch>
	);
}

export default SmsCampaign;