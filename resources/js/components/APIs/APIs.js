import React from 'react';
import PrivateRoute from '../helpers/PrivateRoute';
import { Route, Switch } from "react-router-dom";

import AllApis from "./AllApis";


function Contacts(props) {
	return (
		<Switch>
			<PrivateRoute path="/apis" exact component={AllApis} />
		</Switch>
	);
}

export default Contacts;