import React, { useEffect, useState } from "react";
import { Link, useHistory } from "react-router-dom";
import { Container, Row, Col } from "react-bootstrap";
import SiteLogo from "../../../assets/images/client/logo.svg";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faFacebookF } from "@fortawesome/free-brands-svg-icons";
import { faTwitter } from "@fortawesome/free-brands-svg-icons";
import { faLinkedinIn } from "@fortawesome/free-brands-svg-icons";
import { faInstagram } from "@fortawesome/free-brands-svg-icons";
import Spinner from '../../includes/spinner/Spinner';
import { withTranslation } from 'react-i18next';

function Footer(props) {
  const [loading, setLoading] = useState(false);
  const [contents, setContents] = useState([]);
  const [sectionText, setSectionText] = useState();
  const [facebook, setFacebook] = useState('');
  const [linkedIn, setLinkedIn] = useState('');
  const [instagram, setInstagram] = useState('');
  const [twitter, setTwitter] = useState('');
  const { t } = props;

  const getSocials = () => {
    axios
      .get("/api/get-socials?lang=" + localStorage.lang)
      .then((res) => {
        setLoading(false);
        // console.log(res.data);
        var data = res.data;
        setFacebook(data.facebook);
        setInstagram(data.instagram);
        setLinkedIn(data.linkedIn);
        setTwitter(data.twitter);
      })
      .catch((error) => {
        setLoading(false);
      });
  }
  const getSections = () => {
    setLoading(true);
    axios
      .get("/api/get-home-sections?lang=" + localStorage.lang)
      .then((response) => {
        setLoading(false);
        // console.log(response.data);
        setContents(response.data.data);
        // console.log(response.data.data);
        if (response.data.data.length) {
          var allcontents = response.data.data;
          // console.log(allcontents);
          setSectionText(allcontents.find(content => content.id == 9));
        }
      })
      .catch((error) => {
        setLoading(false);
      });
  };

  useEffect(() => {
    getSocials();
    getSections();
  }, []);

  return (
    <>
      {loading ? <Spinner /> : null}
      <section className="footer">
        <div className="container-width">
          <Row>
            <Col lg="3" xs="12" className=" d-flex justify-content-lg-end mb-lg-0 mb-2">
              <div className="footer-content">
                <div className="footer-logo">
                  <Link to="/">
                    <img src={SiteLogo} alt=" Site Logo" />
                  </Link>
                </div>
                {sectionText ?
                  <div className="mb-lg-5 row" dangerouslySetInnerHTML={{ __html: sectionText.description }} />
                  : ""}
                <div className="footer-icons social-icons">
                  {(facebook != '' && facebook) ?
                    <a href={facebook}>
                      <FontAwesomeIcon
                        className="right-arrow"
                        icon={faFacebookF}
                      />
                    </a>
                    : ""}
                  {(twitter != '' && twitter) ?
                    <a href={twitter}>
                      <FontAwesomeIcon
                        className="right-arrow"
                        icon={faTwitter}
                      />
                    </a>
                    : ""}
                  {(linkedIn != '' && linkedIn) ?
                    <a href={linkedIn}>
                      <FontAwesomeIcon
                        className="right-arrow"
                        icon={faLinkedinIn}
                      />
                    </a>
                    : ""}
                  {(instagram != '' && instagram) ?
                    <a href={instagram}>
                      <FontAwesomeIcon
                        className="right-arrow"
                        icon={faInstagram}
                      />
                    </a>
                    : ""}
                </div>
              </div>
            </Col>
            <Col lg="3" xs="12" className=" d-flex justify-content-lg-end  mb-lg-0 mb-2">
              <div className="footer-content">
                <h3>{t('Useful Links')}</h3>
                <ul className="list footer-list p-0">
                  <li>
                    <Link to="/features" className="list-item">{t('Features')}</Link>
                  </li>
                  <li>
                    <Link to="/" className="list-item ">{t('Home')}</Link>
                  </li>
                  <li>
                    <a href="/#plan" className="list-item">{t('Pricing')}</a>
                  </li>
                </ul>
              </div>
            </Col>
            <Col lg="3" xs="12" className=" d-flex justify-content-lg-end  mb-lg-0 mb-2">
              <div className="footer-content">
                <h3>{t('Legal')}</h3>
                <ul className="list footer-list p-0">
                  <li>
                    <Link to="/pages/imprint" className="list-item">{t('Imprint')}</Link>
                  </li>
                  <li>
                    <Link to="/pages/privacy-policy" className="list-item">{t('Privacy Policy')}</Link>
                  </li>
                  <li>
                    <Link to="/pages/terms-and-conditions" className="list-item ">{t('terms_and_conditions')}</Link>
                  </li>
                  <li>
                    <Link to="/pages/license-agreement" className="list-item">{t('License Agreement')}</Link>
                  </li>
                </ul>
              </div>
            </Col>
            <Col lg="3" xs="12" className=" d-flex justify-content-lg-center">
              <div className="footer-content">
                <h3>{t('Contact')}</h3>
                <ul className="list footer-list p-0">
                  <li>
                    <Link to="/about-us" className="list-item">{t('About Us')}</Link>
                  </li>
                  <li>
                    <Link to="/contact-us" className="list-item ">{t('Contact Us')}</Link>
                  </li>
                  <li>
                    <Link to="/faqs" className="list-item">{t('FAQs')}</Link>
                  </li>
                </ul>
              </div>
            </Col>
          </Row>
        </div>
      </section>
    </>
  );
}

export default withTranslation()(Footer);
