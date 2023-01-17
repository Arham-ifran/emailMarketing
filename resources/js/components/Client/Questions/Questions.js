import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { Container, Row, Col } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faAngleRight } from "@fortawesome/free-solid-svg-icons";
import { withTranslation } from 'react-i18next';

function Questions(props) {
  const { t } = props;
  const [loading, setLoading] = useState(false);
  const [sectionText, setSectionText] = useState();
  const [contents, setContents] = useState([]);
  const [faqs, setFaqs] = useState([]);

  useEffect(() => {
    if (props.contents.length) {
      setContents(props.contents);
      var allcontents = props.contents;
      setSectionText(allcontents.find(content => content.id == 10));
    }
    getFaqs();
  }, [props]);

  const getFaqs = () => {
    // setLoading(true);
    axios
      .get("/api/get-home-faqs?lang=" + localStorage.lang)
      .then((response) => {
        // setLoading(false);
        // console.log(response.data);
        setFaqs(response.data.data);
      })
      .catch((error) => {
        // setLoading(false);
      });
  }

  return (
    <>
      <section className="questions" id="questions">
        <div className="container-width">
          <Row>
            <Col md="6" xs="12">
              <div className="questions-content">
                <h2 className="all-h2">{t('Frequently Asked Questions')}</h2>
                <p>
                  {sectionText ?
                    <div dangerouslySetInnerHTML={{ __html: sectionText.description }} />
                    : ""}
                </p>
                <Link to="/contact-us" className="plan-btn">
                  {t('Contact Us')}{" "}
                </Link>
              </div>
            </Col>
            <Col md="6" xs="12">
              <div className="openclosebtn questions-accordion">
                <div className="accordion" id="accordionExample">

                  {faqs.slice(0, 2).map(question => (
                    <div key={question.id} className="accordion-item">
                      <div className="accordion-heading">
                        <h3 className="accordion-header" id="headingThree">
                          <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target={"#collapse" + question.id} aria-expanded="false" aria-controls={"collapse" + question.id}>
                            {question.question}
                          </button>
                        </h3>
                      </div>
                      <div id={"collapse" + question.id} className="accordion-collapse collapse questions-accordion" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                        <div className="accordion-body">
                          {question.answer}
                        </div>
                      </div>
                    </div>
                  ))}

                </div>
              </div>
              <div className="text-center">
                <Link to="/faqs" className="questions-btn">
                  {t('See more')}
                  <FontAwesomeIcon
                    className="right-arrow"
                    icon={faAngleRight}
                  />
                </Link>
              </div>
            </Col>
          </Row>
        </div>
      </section>
      <section className="get-started">
        <div className="container-width">
          <div className="get-started-content d-flex flex-md-row flex-column align-items-center justify-content-center">
            <h2 className="all-h2">{t('Ready To Get Started?')}</h2>

            <Link to="/signup" className="get-started-btn mt-md-2 mt-3">{t('Get Started')}</Link>
          </div>
        </div>
      </section>
    </>
  );
}

export default withTranslation()(Questions);
