import React, { useState } from 'react';
import PrivateRoute from '../helpers/PrivateRoute';
// import CreateEmailCampaign from '../EmailCampaign/CreateEmailCampaign';
import EmailTemplateListing from './EmailTemplateListing';
import CreateEmailTemplate from './CreateEmailTemplate';
import ImportTemplate from './ImportTemplate';
import AddSmsTemplate from './AddSmsTemplate'
import { withTranslation } from 'react-i18next';
import { Route, Switch } from "react-router-dom";
//import './Dashboard.css';
function EmailTemplate(props) {
	const { t } = props;
	// console.log(props,"propsEmailTemplate");
	return (
		<>
			<Switch>
				<PrivateRoute path="/email-template/create" component={CreateEmailTemplate} />
				<PrivateRoute path="/email-template/:id/edit" component={CreateEmailTemplate} />
				<PrivateRoute path="/template/list" component={EmailTemplateListing} />
				<PrivateRoute path="/email-template/import" component={ImportTemplate} />
				<PrivateRoute path="/sms-template/create" component={AddSmsTemplate} />
				<PrivateRoute path="/sms-template/:id/edit" component={AddSmsTemplate} />
				<PrivateRoute path="/email-template/:id/edithtml" component={ImportTemplate} />
				{/* 
			<PrivateRoute path="/email-campaign/list" component={CloudCallback} />
			<PrivateRoute path="/clouds/manage" component={ManageClouds} />
			<PrivateRoute path="/clouds/migrate" component={MigrateCloud} />
			<PrivateRoute path="/clouds/migration-reports" component={MigrationReports} /> */}
			</Switch>
		</>
	);
}

export default withTranslation()(EmailTemplate);