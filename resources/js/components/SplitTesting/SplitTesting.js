import React from 'react';
import PrivateRoute from '../helpers/PrivateRoute';
import CreateSplitTesting from '../SplitTesting/CreateSplitTesting';
import SplitTestingList from '../SplitTesting/SplitTestingList';
import { Switch } from "react-router-dom";
import Newreport from './Newreport';
import ViewCampaign from './ViewCampaign';
//import './Dashboard.css';
function EmailCampaign(props) {
	return (
		<>
			<Switch>
				<PrivateRoute path="/split-testing/create" component={CreateSplitTesting} />
				<PrivateRoute path="/split-testing/:id/edit" component={CreateSplitTesting} />
				<PrivateRoute path="/split-testing/list" component={SplitTestingList} />
				<PrivateRoute path="/split-testing/:campaignId?/report" exact component={Newreport} />
				<PrivateRoute path="/split-testing/view/:campaignId" exact component={ViewCampaign} />
			</Switch>
		</>
	);
}

export default EmailCampaign;