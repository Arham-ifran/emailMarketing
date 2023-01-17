import React, { useEffect, useState } from "react";
import { Container, Row, Col } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faAngleRight } from "@fortawesome/free-solid-svg-icons";
import Spinner from '../../includes/spinner/Spinner';
import { withTranslation } from 'react-i18next';
import { Link } from "react-router-dom";

function Plan(props) {
  const { t } = props;
  const [sectionText, setSectionText] = useState();
  const [contents, setContents] = useState([]);
  const [packages, setPackages] = useState([]);
  const [loading, setLoading] = useState(false);
  const [monthly, setMonthly] = useState(true);
  const [vat_rate, setVat_rate] = useState(null);

  useEffect(() => {
    if (localStorage.jwt_token) {

      axios.get('/api/auth/profile?lang=' + localStorage.lang).then(response => {
        setVat_rate(response.data.vat_rate);
      });
    } else {
      const state = this;
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          let result = JSON.parse(xhttp.responseText);
          axios.get('/api/get-country-vat?country_name=' + result.data.country + '&country_code=' + result.data.countryCode + '&lang=' + localStorage.lang)
            .then(response => {
              setVat_rate(response.data.data.vat)
              // setCountryVatLoading(false)
            })
        }
      };
      const url = '/api/get-geo-location';
      xhttp.open("GET", url, true);
      xhttp.send();
    }
  }, [])


  useEffect(() => {
    if (props.contents.length) {
      setContents(props.contents);
      var allcontents = props.contents;
      setSectionText(allcontents.find(content => content.id == 11));
    }
    getPackages();
  }, [props]);

  const getPackages = () => {
    // setLoading(true);
    axios
      .get("/api/packages?lang=" + localStorage.lang)
      .then((response) => {
        // setLoading(false);
        setPackages(response.data.data);
      })
      .catch((error) => {
        // setLoading(false);
      });
  }

  return (
    <>
      {loading ? <Spinner /> : null}
      <section className="plan" id="plan">
        <div className="container-width">
          <div className="plan-heading text-center">
            <h2 className="all-h2">{t('Choose The Right Plan For Your Campaign')}</h2>
          </div>

          <Row className="border-row w-100">
            <Col lg="6" className="text-center">
              <div class="plan-content" onClick={() => setMonthly(true)}><button class={monthly ? "plan-btn active" : "plan-btn"} >Monthly</button></div>
            </Col>
            <Col lg="6" className="text-center">
              <div class="plan-content" onClick={() => setMonthly(false)}><button class={monthly ? "plan-btn" : "plan-btn active"}>Yearly</button></div>
            </Col>
          </Row>

          {packages.filter(row => row.id != 9).map((pkg, index) =>
            <Row key={index} className=" border-row w-100">
              <Col lg="3">
                <div className="plan-content">
                  <h3>{pkg.title}</h3>
                </div>
              </Col>
              <Col lg="3">
                <div className="plan-content">
                  {/* <h4>10 Gb Storage</h4> */}
                  <div dangerouslySetInnerHTML={{ __html: pkg.description }} />
                </div>
              </Col>
              <Col lg="3">
                <div className="plan-content">
                  <h2 className="all-h2">€ {monthly ? (vat_rate != null ? (pkg.monthly_price + ((pkg.monthly_price * Number(vat_rate) / 100))).toFixed(2) : pkg.monthly_price) : (vat_rate != null ? (pkg.yearly_price + ((pkg.yearly_price * Number(vat_rate) / 100))).toFixed(2) : pkg.yearly_price)}</h2>
                  <p>{monthly ? t('Monthly') : t('Yearly')}</p>
                </div>
              </Col>
              <Col lg="3">
                <div className="plan-content">
                  <Link to={localStorage.jwt_token ? "/packages/upgrade-package" : "/packages"}>
                    {pkg.recommended ?
                      <button className="plan-btn active">
                        <span className="recommended">{t('Recommended')}</span>
                        {t('See more')}
                      </button>
                      :
                      <button className="plan-btn">{t('See more')}</button>
                    }
                  </Link>
                </div>
              </Col>
            </Row>
          )}
          {packages.filter(row => row.id == 9).map((pkg, index) =>
            <Row key={index} className=" border-row w-100">
              <Col lg="3">
                <div className="plan-content">
                  <h3>{pkg.title}</h3>
                </div>
              </Col>
              <Col lg="3">
                <div className="plan-content">
                  {/* <h4>10 Gb Storage</h4> */}
                  <div dangerouslySetInnerHTML={{ __html: pkg.description }} />
                </div>
              </Col>
              <Col lg="3">
                <div className="plan-content">
                  <h2 className="all-h2">{t('Paid')}</h2>
                  {/* <p>{monthly ? t('Monthly') : t('Yearly')}</p> */}
                  <p>{t('Weekly')}</p>
                </div>
              </Col>
              <Col lg="3">
                <div className="plan-content">
                  <Link to={localStorage.jwt_token ? "/packages/upgrade-package" : "/packages"}>
                    {pkg.recommended ?
                      <button className="plan-btn active">
                        <span className="recommended">{t('Recommended')}</span>
                        {t('See more')}
                      </button>
                      :
                      <button className="plan-btn">{t('See more')}</button>
                    }
                  </Link>
                </div>
              </Col>
            </Row>
          )}
          <div className="question-see-btn d-flex align-items-center justify-content-center">
            <Link to="/packages" className="questions-btn">
              {t('See more')}
              <FontAwesomeIcon
                className="right-arrow"
                icon={faAngleRight}
              />
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}

export default withTranslation()(Plan);
