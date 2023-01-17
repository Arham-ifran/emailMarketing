import React from 'react';
import PrivateRoute from '../helpers/PrivateRoute';
import { Route, Switch } from "react-router-dom";

import AllContacts from "./Contacts/AllContacts";
import CreateContact  from "./Contacts/CreateContact";
import EditContact  from "./Contacts/EditContact";
import AddMultipleContact  from "./Contacts/AddMultipleContact";


function Contacts(props) {
	return (
		<Switch>
			<PrivateRoute path="/contacts" exact component={AllContacts} />
			<PrivateRoute path="/contacts/create/" exact component={CreateContact} />
			<PrivateRoute path="/contacts/add-multiple" exact component={AddMultipleContact} />
			<PrivateRoute path="/contacts/:contactId?" exact component={EditContact} />
		</Switch>
	);
}

export default Contacts;