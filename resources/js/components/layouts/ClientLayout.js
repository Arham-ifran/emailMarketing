import React, { useState } from "react";
import { Link } from "react-router-dom";
import SiteLogo from "../../assets/images/main-logo.svg";
import MainNavigation from "../MainNavigation/MainNavigation";
import Header from "../Client/HeaderClient/Header";
import Footer from "../Client/Footer/Footer";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faBars } from "@fortawesome/free-solid-svg-icons";
// import './AppLayout.css';
import "../../custom.css";

const ClientLayout = ({ children }) => {
  const [isActive, setActive] = useState(false);

  const toggleClass = () => {
    setActive(!isActive);
  };

  return (
    <div className={"wrapper " + localStorage.lang}>
      <Header />
      {children}
      <Footer />
    </div>
  );
};
export default ClientLayout;
