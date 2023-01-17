import React from 'react';
import PublicRoute from '../helpers/PublicRoute';
import { Route, Switch } from "react-router-dom";

import Home from "./Home/Home";
import ContactUs from "./ContactUs/ContactUs";
import AboutUs from "./AboutUs/AboutUs";
import Features from "./Features/Features";
import FAQs from "./FAQs/FAQs";
import CmsPages from "./Others/CmsPage"
import Packages from './Packages/Packages';

function Contacts(props) {
	return (
		<Switch>
			<PublicRoute path="/" exact component={Home} />
			<PublicRoute path="/home" exact component={Home} />
			<PublicRoute path="/contact-us" exact component={ContactUs} />
			<PublicRoute path="/about-us" exact component={AboutUs} />
			<PublicRoute path="/features" exact component={Features} />
			<PublicRoute path="/faqs" exact component={FAQs} />
			<PublicRoute path="/pages/:slug" component={CmsPages} />
			<PublicRoute path="/packages" component={Packages} />

		</Switch>
	);
}

export default Contacts;