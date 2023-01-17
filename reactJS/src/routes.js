import React from "react";
import { Redirect } from 'react-router';
import { BrowserRouter, Route, Switch } from "react-router-dom";
import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap/dist/js/bootstrap.min.js';
import LoginLayout from "./layouts/LoginLayout";
import AppLayout from "./layouts/AppLayout";

import LoginPage from './components/Login/Login';
import SignUp from './components/SignUp/SignUp';
import ForgotPassword from './components/ForgotPassword/ForgotPassword';
import Dashboard from './components/Dashboard/Dashboard';
import Campaigns from './components/Campaigns/Campaigns';

import EmailCampaigns from './components/EmailCampaigns/EmailCampaigns';
import CreateEmailCampaign from './components/CreateEmailCampaign/CreateEmailCampaign';
import EditEmailCampaign from './components/EditEmailCampaign/EditEmailCampaign';
import SmsCampaigns from './components/SmsCampaigns/SmsCampaigns';
import CreateSmsCampaign from './components/CreateSmsCampaign/CreateSmsCampaign';
import EditSmsCampaign from './components/EditSmsCampaign/EditSmsCampaign';

import ManageMailingLists from './components/ManageMailingLists/ManageMailingLists';
import CreateMailingList from './components/CreateMailingList/CreateMailingList';
import EditMailList from './components/EditMailList/EditMailList';
import SplitTesting from './components/SplitTesting/SplitTesting';
import CreateSplitTesting from './components/CreateSplitTesting/CreateSplitTesting';
import EditSplitTestingCampaign from './components/EditSplitTestingCampaign/EditSplitTestingCampaign';
import AllContacts from './components/AllContacts/AllContacts';
import CreateContact from './components/CreateContact/CreateContact';
import AddMultipleContact from './components/AddMultipleContact/AddMultipleContact';
import EditContact from './components/EditContact/EditContact';
import MyTemplates from './components/MyTemplates/MyTemplates';
import CreateTemplate from './components/CreateTemplate/CreateTemplate';
import EditTemplate from './components/EditTemplate/EditTemplate';
import AnalyticsAndReports from './components/AnalyticsAndReports/AnalyticsAndReports';
import ImportTemplate from './components/ImportTemplate/ImportTemplate';
import APIs from './components/APIs/APIs';

const LoginLayoutRoute = ({ component: Component, ...rest }) => {
	return (
	  <Route
		{...rest}
		render={matchProps => (
		  <LoginLayout>
			<Component {...matchProps} />
		  </LoginLayout>
		)}
	  />
	);
  };
  
  const AppLayoutRoute = ({ component: Component, ...rest }) => {
	return (
	  <Route
		{...rest}
		render={matchProps => (
		  <AppLayout>
			<Component {...matchProps} />
		  </AppLayout>
		)}
	  />
	);
  };

const routes = [
    { path: "/", access: true, exact: true, name: "signin", layout: LoginLayoutRoute, component: LoginPage, showInSideBar: false},
    { path: "/signin", access: true, exact: true, name: "signin", layout: LoginLayoutRoute, component: LoginPage, showInSideBar: false},
    { path: "/signup", access: true, exact: true, name: "Sign Up", layout: LoginLayoutRoute, component: SignUp, showInSideBar: false},
    { path: "/forgot-password", access: true, exact: true, name: "Forgot Password", layout: LoginLayoutRoute, component: ForgotPassword, showInSideBar: false},

    { path: "/dashboard", access: true, exact: true, name: "Dashboard", layout: AppLayoutRoute, component: Dashboard, showInSideBar: true,},
    
    { access: true, exact: true, name: "Campaigns", layout: AppLayoutRoute, showInSideBar: true,
        submenus: [
            { path: "/email-campaigns", access: true, exact: true, name: "Email Campaigns", layout: AppLayoutRoute, component: EmailCampaigns, showInSideBar: true },
            { path: "/sms-campaigns", access: true, exact: true, name: "SMS Campaigns", layout: AppLayoutRoute, component: SmsCampaigns, showInSideBar: true }
        ]
    },
    
        { path: "/create-email-campaign", access: true, exact: true, name: "Create Email Campaigns", layout: AppLayoutRoute, component: CreateEmailCampaign, showInSideBar: false },
        { path: "/email-campaigns/:campaignId?", access: true, exact: true, name: "Edit Email Campaigns", layout: AppLayoutRoute, component: EditEmailCampaign, showInSideBar: false },
        
        { path: "/create-sms-campaigns", access: true, exact: true, name: "Create SMS Campaigns", layout: AppLayoutRoute, component: CreateSmsCampaign, showInSideBar: false },
        { path: "/sms-campaigns/:campaigsId?", access: true, exact: true, name: "Edit SMS Campaigns", layout: AppLayoutRoute, component: EditSmsCampaign, showInSideBar: false },

    { access: true, exact: true, name: "Subscribers List", layout: AppLayoutRoute, showInSideBar: true,
        submenus: [
            { path: "/contacts", access: true, exact: true, name: "All Contacts", layout: AppLayoutRoute, component: AllContacts, showInSideBar: true },
            { path: "/mailing-lists", access: true, exact: true, name: "Manage Mailing List", layout: AppLayoutRoute, component: ManageMailingLists, showInSideBar: true },
        ]
    },

        { path: "/contacts/create", access: true, exact: true, name: "Create Contact", layout: AppLayoutRoute, component: CreateContact, showInSideBar: false },
        { path: "/contacts/add-multiple", access: true, exact: true, name: "Add Multiple Contacts", layout: AppLayoutRoute, component: AddMultipleContact, showInSideBar: false },
        { path: "/contacts/:contactId?", access: true, exact: true, name: "Edit Contact", layout: AppLayoutRoute, component: EditContact, showInSideBar: false },

        { path: "/mailing-lists/create", access: true, exact: true, name: "Create Maling List", layout: AppLayoutRoute, component: CreateMailingList, showInSideBar: false },
        { path: "/mailing-list/:listID?", access: true, exact: true, name: "Edit Maling List", layout: AppLayoutRoute, component: EditMailList, showInSideBar: false },

    { path: "/my-templates", access: true, exact: true, name: "My Templates", layout: AppLayoutRoute, component: MyTemplates, showInSideBar: true },
    { path: "/create-template", access: true, exact: true, name: "Create Template", layout: AppLayoutRoute, component: CreateTemplate, showInSideBar: false },
    { path: "/import-template", access: true, exact: true, name: "Import Template", layout: AppLayoutRoute, component: ImportTemplate, showInSideBar: false },
    { path: "/templates/:templateId?", access: true, exact: true, name: "Edit Template", layout: AppLayoutRoute, component: EditTemplate, showInSideBar: false },

    { path: "/split-testing", access: true, exact: true, name: "Split Testing", layout: AppLayoutRoute, component: SplitTesting, showInSideBar: true },
    { path: "/create-split-testing", access: true, exact: true, name: "Create Split Testing", layout: AppLayoutRoute, component: CreateSplitTesting, showInSideBar: false },
    { path: "/split-testing/:campaigsId?", access: true, exact: true, name: "Edit Split Testing", layout: AppLayoutRoute, component: EditSplitTestingCampaign, showInSideBar: false },

    { path: "/analytics-and-reports", access: true, exact: true, name: "Analytics And Reports", layout: AppLayoutRoute, component: AnalyticsAndReports, showInSideBar: true},

    { path: "/api", access: true, exact: true, name: "APIs", layout: AppLayoutRoute, component: APIs, showInSideBar: true}

];

export default routes;