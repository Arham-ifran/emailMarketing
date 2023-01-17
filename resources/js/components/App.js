import React from "react";
import ReactDOM from "react-dom";
import { Redirect } from "react-router";
import { BrowserRouter, Route, Switch } from "react-router-dom";
import "bootstrap/dist/css/bootstrap.min.css";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import '../i18n';

import AuthLayout from "./layouts/AuthLayout";
import PrivateLayout from "./layouts/PrivateLayout";
import ClientLayout from "./layouts/ClientLayout";
import LoginPage from "./Login/Login";
import ForgotPassword from "./Auth/ForgotPassword";
import VerifyUser from "./Auth/VerifyUser";
import ResetPassword from "./Auth/ResetPassword";

import SignUp from "./SignUp/SignUp";
import Profile from './Auth/Profile';
import AuthCallback from "./SignUp/AuthCallback";
import PrivateRoute from "./helpers/PrivateRoute";
import SetAuthToken from "./helpers/SetAuthToken";
import EmailCampaign from "./EmailCampaign/EmailCampaign";
import EmailTemplate from "./EmailTemplate/EmailTemplate";
import SmsCampaign from "./SMSCampaign/SmsCampaign";
import Contacts from "./Contacts/Contacts";
import MailingLists from "./Contacts/MailingLists";
import SplitTesting from "./SplitTesting/SplitTesting"
import AnalyticsAndReports from './AnalyticsAndReports/AnalyticsAndReports';
import Notifications from './Notifications/Notifications';
import CmsPage from './Client/Others/CmsPage';
import ScrollToTop from "./helpers/ScrollToTop";
import PackagesAndPayments from "./PackagesAndPayments/PackagesAndPayments";
import ResendVerification from "./Auth/ResendVerification";

import "../app.css";
import Client from "./Client/Client";

import Dashboard from "./Dashboard/Dashboard";
import APIs from './APIs/APIs';

import UserSubscribe from "./Contacts/UserSubscribe/UserSubscribe";
import UserUnSubscribe from "./Contacts/UserSubscribe/UserUnSubscribe";
import NotFound from "./NotFound";
import VerifyAccount from "./Auth/VerifyAccount";
import axios from "axios";

const responseSuccessHandler = response => {
    return response;
};

const responseErrorHandler = error => {
    if (error.response.status === 401) {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('user_name');
        localStorage.removeItem('country');
        SetAuthToken(false);
        window.location.href = "/signin";
    }

    return Promise.reject(error);
}

axios.interceptors.response.use(
    response => responseSuccessHandler(response),
    error => responseErrorHandler(error)
);

if (localStorage.jwt_token) {
    SetAuthToken(localStorage.jwt_token);
}

const AuthLayoutRoute = ({ component: Component, ...rest }) => {
    return (
        <Route
            {...rest}
            render={(matchProps) => (
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
            render={(matchProps) => (
                <PrivateLayout>
                    <Component {...matchProps} />
                </PrivateLayout>
            )}
        />
    );
};

const ClientLayoutRoute = ({ component: Component, ...rest }) => {
    return (
        <Route
            {...rest}
            render={matchProps => (
                <ClientLayout>
                    <Component {...matchProps} />
                </ClientLayout>
            )}
        />
    );
};

const routes = [
    {
        path: "/signin",
        access: true,
        exact: true,
        name: "signin",
        layout: AuthLayoutRoute,
        component: LoginPage,
        showInSideBar: false,
    },
    {
        path: "/signup",
        access: true,
        exact: true,
        name: "Sign Up",
        layout: AuthLayoutRoute,
        component: SignUp,
        showInSideBar: false,
    },

    {
        path: "/dashboard",
        access: true,
        exact: true,
        name: "Dashboard",
        layout: PrivateLayoutRoute,
        component: Dashboard,
        showInSideBar: true,
    },
    {
        path: "/auth/google-callback",
        access: true,
        exact: true,
        name: "Auth Callback",
        layout: AuthLayoutRoute,
        component: AuthCallback,
        showInSideBar: false,
    },
];

const App = () => (
    <BrowserRouter>
        <ScrollToTop>
            <Switch>
                <Route exact={true} path="/" component={Client} />
                <Route exact={true} path="/home" component={Client} />
                <Route exact={true} path="/contact-us" component={Client} />
                <Route exact={true} path="/about-us" component={Client} />
                <Route exact={true} path="/faqs" component={Client} />
                <Route exact={true} path="/features" component={Client} />
                <Route exact={true} path="/pages/:slug" component={Client} />
                <Route exact={true} path="/packages" component={Client} />

                {/* <AuthLayoutRoute exact={true} path="/verify/:email?" component={ResendVerification} /> */}
                <AuthLayoutRoute exact={true} path="/signin" component={LoginPage} />
                <AuthLayoutRoute exact={true} path="/signup" component={SignUp} />

                <AuthLayoutRoute exact={true} path="/forgot-password" component={ForgotPassword} />
                <AuthLayoutRoute exact={true} path="/verify-account/:auth" component={VerifyAccount} />
                <AuthLayoutRoute exact={true} path="/verified" component={VerifyUser} />
                <AuthLayoutRoute exact={true} path="/verified-error" component={VerifyUser} />
                <AuthLayoutRoute exact={true} path="/reset-password/:token?" component={ResetPassword} />
                <AuthLayoutRoute exact={true} path="/auth/google-callback" component={AuthCallback} />
                <AuthLayoutRoute exact={true} path="/auth/twitter-callback" component={AuthCallback} />
                <AuthLayoutRoute exact={true} path="/auth/facebook-callback" component={AuthCallback} />

                <PrivateRoute exact={true} path="/dashboard" component={Dashboard} />
                <PrivateRoute exact={true} path="/profile" component={Profile} />
                <Route path="/email-campaign" component={EmailCampaign} />
                <Route path="/email-template" component={EmailTemplate} showSideBar={true} />
                <Route path="/template" component={EmailTemplate} showSideBar={true} />
                <Route path="/sms-template" component={EmailTemplate} showSideBar={true} />
                <Route path="/split-testing" component={SplitTesting} />

                <Route path="/sms-campaign" component={SmsCampaign} />
                <Route path="/contacts" component={Contacts} />
                <Route path="/mailing-lists" component={MailingLists} />
                <Route path="/packages" component={PackagesAndPayments} />

                <Route exact={true} path="/subscribe-contact/:id?" component={UserSubscribe} />
                <Route exact={true} path="/unsubscribe-contact/:id?" component={UserUnSubscribe} />

                <Route exact={true} path="/analytics-and-reports" component={AnalyticsAndReports} />
                <Route exact={true} path="/apis" component={APIs} />
                <Route exact={true} path="/notifications" component={Notifications} />
                <Route exact={true} path="/new-report" component={AnalyticsAndReports} />
                <Route component={NotFound} />

            </Switch>
        </ScrollToTop>
    </BrowserRouter>
);
export default App;

ReactDOM.render(
    <React.StrictMode>
        <App />
    </React.StrictMode>,
    document.getElementById("app")
);
