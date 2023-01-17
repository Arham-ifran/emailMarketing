import React from "react";
import { Col, Container, Nav, Navbar, NavDropdown, Row } from "react-bootstrap";
import { withTranslation } from 'react-i18next';
import { Link, useHistory } from "react-router-dom";
import SiteLogo from "../../../assets/images/sitelogo.svg";
import LanguageSelector from '../../helpers/LanguageSelector';

function Header(props) {
  // console.log("props");
  const { t } = props;
  const history = useHistory();
  const pathname = history.location.pathname;
  return (
    <header id="header" className="main-page-header headerForClient p-0">
      <nav className="navbar navbar-expand-lg navbar-light ">
        <Container fluid className=" container-width">
          <div className="align-items-center w-100 row d-flex">
            <Col lg={2}>
              <strong className="logo">
                <Link to="/">
                  <img src={SiteLogo} alt=" Site Logo" />
                </Link>
              </strong>
            </Col>
            <Col lg={10} className="navigation-holder ms-auto">
              <Navbar expand="lg" className="pos-stat">
                <Navbar.Toggle aria-controls="basic-navbar-nav" />
                <Navbar.Collapse
                  id="basic-navbar-nav"
                  className="flex-row justify-content-end"
                >
                  <ul className="navbar-nav ml-auto">
                    <li className="nav-item">
                      <Link
                        className={`nav-link ${pathname == "/" ? "active" : ""
                          }`}
                        to="/"
                      >
                        {t('Home')}{" "}
                      </Link>
                    </li>
                    <li className="nav-item">
                      <Link
                        className={`nav-link ${pathname == "/features" ? "active" : ""
                          }`}
                        to="/features"
                      >
                        {t('Features')}
                      </Link>
                    </li>
                    <li className="nav-item">
                      <Link
                        className={`nav-link ${pathname == "/about-us" ? "active" : ""
                          }`}
                        to="/about-us"
                      >
                        {t('About Us')}
                      </Link>
                    </li>
                    <li className="nav-item">
                      <Link
                        className={`nav-link ${pathname == "/contact-us" ? "active" : ""
                          }`}
                        to="/contact-us"
                      >
                        {t('Contact Us')}
                      </Link>
                    </li>
                    {!localStorage.jwt_token ?
                      <>
                        <li className="nav-item">
                          <Link
                            className={`nav-link ${pathname == "/signin" ? "active" : ""
                              }`}
                            to="/signin"
                          >
                            {t('Sign In')}
                          </Link>
                        </li>
                        <li className="">
                          <form className="d-flex header-buttons m-0">
                            <Link to="/signup" className="h-register-btn">{t('Register')}</Link>
                          </form>
                        </li>
                      </>
                      :

                      <li className="">
                        <form className="d-flex header-buttons m-0">
                          <Link to="/dashboard" className="h-register-btn">{t('dashboard')}</Link>
                        </form>
                      </li>
                    }
                    <li className="nav-item d-lg-block d-none m-auto">
                      <LanguageSelector />
                    </li>
                  </ul>
                </Navbar.Collapse>
              </Navbar>
            </Col>
            <Col className="d-lg-none d-block">
              <div className="language-place">
                <LanguageSelector />
              </div>
            </Col>
          </div>
        </Container>
      </nav>

    </header>
  );
}
export default withTranslation()(Header);
