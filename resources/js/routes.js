import React from "react";
import { Redirect } from 'react-router';
import { BrowserRouter, Route, Switch } from "react-router-dom";
import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap/dist/js/bootstrap.min.js';
import AuthLayout from "./components/layouts/AuthLayout";
import PrivateLayout from "./components/layouts/PrivateLayout";

import LoginPage from './components/Login/Login';
import SignUp from './components/SignUp/SignUp';
import CreateEmailTemplate from "./components/EmailTemplate/CreateEmailTemplate";
// import ForgotPassword from './components/ForgotPassword/ForgotPassword';
// import Dashboard from './components/Dashboard/Dashboard';
// import Campaigns from './components/Campaigns/Campaigns';

// import EmailCampaigns from './components/EmailCampaigns/EmailCampaigns';
// import CreateEmailCampaign from './components/CreateEmailCampaign/CreateEmailCampaign';
// import EditEmailCampaign from './components/EditEmailCampaign/EditEmailCampaign';
// import SmsCampaigns from './components/SmsCampaigns/SmsCampaigns';
// import CreateSmsCampaign from './components/CreateSmsCampaign/CreateSmsCampaign';
// import EditSmsCampaign from './components/EditSmsCampaign/EditSmsCampaign';

// import ManageMailingLists from './components/ManageMailingLists/ManageMailingLists';
// import CreateMailingList from './components/CreateMailingList/CreateMailingList';
// import EditMailList from './components/EditMailList/EditMailList';
// import SplitTesting from './components/SplitTesting/SplitTesting';
// import CreateSplitTesting from './components/CreateSplitTesting/CreateSplitTesting';
// import EditSplitTestingCampaign from './components/EditSplitTestingCampaign/EditSplitTestingCampaign';
// import AllContacts from './components/AllContacts/AllContacts';
// import CreateContact from './components/CreateContact/CreateContact';
// import AddMultipleContact from './components/AddMultipleContact/AddMultipleContact';
// import EditContact from './components/EditContact/EditContact';
// import MyTemplates from './components/MyTemplates/MyTemplates';
// import CreateTemplate from './components/CreateTemplate/CreateTemplate';
// import EditTemplate from './components/EditTemplate/EditTemplate';
// import AnalyticsAndReports from './components/AnalyticsAndReports/AnalyticsAndReports';
// import ImportTemplate from './components/ImportTemplate/ImportTemplate';
// import APIs from './components/APIs/APIs';

const AuthLayoutRoute = ({ component: Component, ...rest }) => {
  return (
    <Route
      {...rest}
      render={matchProps => (
        <AuthLayout>
          <Component {...matchProps} />
        </AuthLayout>
      )}
    />
  );
};

const PrivateLayoutRoute = ({ component: Component, ...rest }) => {
  return (
    <Route
      {...rest}
      render={matchProps => (
        <PrivateLayout>
          <Component {...matchProps} />
        </PrivateLayout>
      )}
    />
  );
};

const routes = [
  { path: "/", access: true, exact: true, name: "signin", layout: AuthLayoutRoute, component: LoginPage, showInSideBar: false },
  { path: "/signin", access: true, exact: true, name: "signin", layout: AuthLayoutRoute, component: LoginPage, showInSideBar: false },
  { path: "/signup", access: true, exact: true, name: "Sign Up", layout: AuthLayoutRoute, component: SignUp, showInSideBar: false },
  // { path: "/forgot-password", access: true, exact: true, name: "Forgot Password", layout: AuthLayoutRoute, component: ForgotPassword, showInSideBar: false},

  // { path: "/dashboard", access: true, exact: true, name: "Dashboard", layout: PrivateLayoutRoute, component: Dashboard, showInSideBar: true,},

  // { access: true, exact: true, name: "Campaigns", layout: PrivateLayoutRoute, showInSideBar: true,
  //     submenus: [
  //         { path: "/email-campaigns", access: true, exact: true, name: "Email Campaigns", layout: PrivateLayoutRoute, component: EmailCampaigns, showInSideBar: true },
  //         { path: "/sms-campaigns", access: true, exact: true, name: "SMS Campaigns", layout: PrivateLayoutRoute, component: SmsCampaigns, showInSideBar: true }
  //     ]
  // },

  //     { path: "/create-email-campaign", access: true, exact: true, name: "Create Email Campaigns", layout: PrivateLayoutRoute, component: CreateEmailCampaign, showInSideBar: false },
  //     { path: "/email-campaigns/:campaignId?", access: true, exact: true, name: "Edit Email Campaigns", layout: PrivateLayoutRoute, component: EditEmailCampaign, showInSideBar: false },

  //     { path: "/create-sms-campaigns", access: true, exact: true, name: "Create SMS Campaigns", layout: PrivateLayoutRoute, component: CreateSmsCampaign, showInSideBar: false },
  //     { path: "/sms-campaigns/:campaigsId?", access: true, exact: true, name: "Edit SMS Campaigns", layout: PrivateLayoutRoute, component: EditSmsCampaign, showInSideBar: false },

  // { access: true, exact: true, name: "Subscribers List", layout: PrivateLayoutRoute, showInSideBar: true,
  //     submenus: [
  //         { path: "/contacts", access: true, exact: true, name: "All Contacts", layout: PrivateLayoutRoute, component: AllContacts, showInSideBar: true },
  //         { path: "/mailing-lists", access: true, exact: true, name: "Manage Mailing List", layout: PrivateLayoutRoute, component: ManageMailingLists, showInSideBar: true },
  //     ]
  // },

  //     { path: "/contacts/create", access: true, exact: true, name: "Create Contact", layout: PrivateLayoutRoute, component: CreateContact, showInSideBar: false },
  //     { path: "/contacts/add-multiple", access: true, exact: true, name: "Add Multiple Contacts", layout: PrivateLayoutRoute, component: AddMultipleContact, showInSideBar: false },
  //     { path: "/contacts/:contactId?", access: true, exact: true, name: "Edit Contact", layout: PrivateLayoutRoute, component: EditContact, showInSideBar: false },

  //     { path: "/mailing-lists/create", access: true, exact: true, name: "Create Maling List", layout: PrivateLayoutRoute, component: CreateMailingList, showInSideBar: false },
  //     { path: "/mailing-list/:listID?", access: true, exact: true, name: "Edit Maling List", layout: PrivateLayoutRoute, component: EditMailList, showInSideBar: false },

  // { path: "/my-templates", access: true, exact: true, name: "My Templates", layout: PrivateLayoutRoute, component: MyTemplates, showInSideBar: true },
  // { path: "/create-template", access: true, exact: true, name: "Create Template", layout: PrivateLayoutRoute, component: CreateTemplate, showInSideBar: false },
  // { path: "/import-template", access: true, exact: true, name: "Import Template", layout: PrivateLayoutRoute, component: ImportTemplate, showInSideBar: false },
  // { path: "/templates/:templateId?", access: true, exact: true, name: "Edit Template", layout: PrivateLayoutRoute, component: EditTemplate, showInSideBar: false },

  // { path: "/split-testing", access: true, exact: true, name: "Split Testing", layout: PrivateLayoutRoute, component: SplitTesting, showInSideBar: true },
  // { path: "/create-split-testing", access: true, exact: true, name: "Create Split Testing", layout: PrivateLayoutRoute, component: CreateSplitTesting, showInSideBar: false },
  // { path: "/split-testing/:campaigsId?", access: true, exact: true, name: "Edit Split Testing", layout: PrivateLayoutRoute, component: EditSplitTestingCampaign, showInSideBar: false },

  // { path: "/analytics-and-reports", access: true, exact: true, name: "Analytics And Reports", layout: PrivateLayoutRoute, component: AnalyticsAndReports, showInSideBar: true},

  // { path: "/api", access: true, exact: true, name: "APIs", layout: PrivateLayoutRoute, component: APIs, showInSideBar: true}

  { path: "/email-template/create", access: true, exact: true, name: "My Templates", layout: PrivateLayoutRoute, component: CreateEmailTemplate, showInSideBar: false },


];

export default routes;