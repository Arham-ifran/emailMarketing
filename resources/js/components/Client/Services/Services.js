import React, { useEffect, useState } from "react";
import { Container, Row, Col } from "react-bootstrap";
import Spinner from '../../includes/spinner/Spinner';
import one from "../../../assets/images/email-service.png";
import two from '../../../assets/images/sms-service.png';
import three from '../../../assets/images/split-service.png';
import four from '../../../assets/images/template-service.png';
import five from '../../../assets/images/report-service.png';
import { withTranslation } from 'react-i18next';
import { Link } from "react-router-dom";

function Services(props) {
  const { t } = props;
  const [loading, setLoading] = useState(false);
  const [sectionText, setSectionText] = useState();
  const [sectionFeatures, setSectionFeatures] = useState();
  const [contents, setContents] = useState([]);
  const [serviceslist, setServices] = useState([]);

  useEffect(() => {
    if (props.contents.length) {
      setContents(props.contents);
      var allcontents = props.contents;
      setSectionText(allcontents.find(content => content.id == 7));
    }
    getServices();
  }, [props]);

  const getServices = () => {
    // setLoading(true);
    axios
      .get("/api/get-home-services?lang=" + localStorage.lang)
      .then((response) => {
        // setLoading(false);
        setServices(response.data.data);
      })
      .catch((error) => {
        // setLoading(false);
      });
  }

  var NUMBER = {
    1: one,
    2: two,
    3: three,
    4: four,
    5: five
  }

  return (
    <>
      {loading ? <Spinner /> : null}
      <section className="services">
        <div className="container-width">
          <Row className="align-items-center justify-content-center">
            <Col md="6" xs="12" className="">
              <div className="services-content-header ms-md-3 me-md-auto">
                {sectionText ?
                  <div dangerouslySetInnerHTML={{ __html: sectionText.description }} />
                  : ""}
              </div>
            </Col>
            {serviceslist.map((service, index) => (
              <Col key={index} md="6" xs="12" className="mb-lg-5">
                <div className="services-content">
                  <img src={NUMBER[index + 1]} alt="" className="img-fluid" />
                  <h2 className="all-h2">{service.name}</h2>
                  <p>
                    {service.description}
                  </p>
                  <h3>
                    <Link to="/features" className="green-three">
                      {" "}
                      {t('Learn More')}
                    </Link>
                  </h3>
                </div>
              </Col>
            ))}

          </Row>
        </div>
      </section>
    </>
  );
}

export default withTranslation()(Services);
