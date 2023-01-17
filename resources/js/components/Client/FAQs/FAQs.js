import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faAngleRight } from "@fortawesome/free-solid-svg-icons";
import Feature1 from "../../../assets/images/feature1.svg";
import feature2 from "../../../assets/images/feature2.svg";
import Feature3 from "../../../assets/images/feature3.svg";
import Feature4 from "../../../assets/images/feature4.svg";
import Feature5 from "../../../assets/images/feature5.svg";
import Feature6 from "../../../assets/images/feature6.svg";
import Feature7 from "../../../assets/images/feature7.svg";
import { withTranslation } from 'react-i18next';
import { Container, Row, Col } from "react-bootstrap";

function FAQs(props) {
  const { t } = props;
  const [loading, setLoading] = useState(false);
  const [sectionText, setSectionText] = useState();
  const [contents, setContents] = useState([]);
  const [faqs, setFaqs] = useState([]);

  useEffect(() => {
    const getSections = () => {
      setLoading(true);
      axios
        .get("/api/get-home-sections?lang=" + localStorage.lang)
        .then((response) => {
          setLoading(false);
          setContents(response.data.data);
          setSectionText(response.data.data.find(content => content.id == 10));
        })
        .catch((error) => {
          setLoading(false);
        });
    };
    getSections();
    getFaqs();
  }, []);

  const getFaqs = () => {
    setLoading(true);
    axios
      .get("/api/get-home-faqs?lang=" + localStorage.lang)
      .then((response) => {
        setLoading(false);
        setFaqs(response.data.data);
      })
      .catch((error) => {
        setLoading(false);
      });
  }

  return (
    <>
      <section className="questions faq-sec" id="questions">
        <div className="container-width">
          <Row>
            <Col md="12" xs="12">
              <div className="questions-content text-center">
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
            <Col md="12" xs="12">
              <div className="openclosebtn questions-accordion">
                <div class="accordion" id="accordionExample">

                  {faqs.map(question => (
                    <div key={question.id} class="accordion-item">
                      <div className="accordion-heading">
                        <h3 class="accordion-header" id="headingThree">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target={"#collapse" + question.id} aria-expanded="false" aria-controls={"collapse" + question.id}>
                            {question.question}
                          </button>
                        </h3>
                      </div>
                      <div id={"collapse" + question.id} class="accordion-collapse collapse questions-accordion" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                          {question.answer}
                        </div>
                      </div>
                    </div>
                  ))}

                </div>
              </div>
            </Col>
          </Row>
        </div>
      </section>
    </>
  );
}

export default withTranslation()(FAQs);
