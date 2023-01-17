import React from 'react';
import PrivateRoute from '../helpers/PrivateRoute';
import { Route, Switch } from "react-router-dom";

import AllMailingLists from "./MailingLists/AllMailingLists";
import CreateMailingList from "./MailingLists/CreateMailingList";
import MailingListContacts from "./MailingLists/MailingListContacts";


function MailingLists(props) {
	return (
		<Switch>
			<PrivateRoute path="/mailing-lists" exact component={AllMailingLists} />
			<PrivateRoute path="/mailing-lists/create" exact component={CreateMailingList} />
			{/* view list */}
			<PrivateRoute path="/mailing-lists/:listID?" exact component={MailingListContacts} />
			<PrivateRoute path="/mailing-lists/:listID/edit" exact component={CreateMailingList} />
		</Switch>
	);
}

export default MailingLists;