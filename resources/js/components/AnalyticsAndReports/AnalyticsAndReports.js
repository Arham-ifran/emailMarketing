import React from 'react';
import PrivateRoute from '../helpers/PrivateRoute';
import { Route, Switch } from "react-router-dom";

import AnalyticReports from "./AnalyticReports";
import Newreport from './Newreport';


function AnalyticsAndReports(props) {
	return (
		<Switch>
			<PrivateRoute path="/analytics-and-reports" exact component={AnalyticReports} />
			<PrivateRoute path="/new-report" exact component={Newreport} />
			{/* <PrivateRoute path="/contacts/create/" exact component={CreateContact} />
			<PrivateRoute path="/contacts/add-multiple" exact component={AddMultipleContact} />
			<PrivateRoute path="/contacts/:contactId?" exact component={EditContact} /> */}
		</Switch>
	);
}

export default AnalyticsAndReports;