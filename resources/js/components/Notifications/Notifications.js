import React from 'react';
import PrivateRoute from '../helpers/PrivateRoute';
import { Route, Switch } from "react-router-dom";

import AllNotifications from "./AllNotifications";


function Notifications(props) {
	return (
		<Switch>
			<PrivateRoute path="/notifications" exact component={AllNotifications} />
		</Switch>
	);
}

export default Notifications;