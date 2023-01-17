import React, { useEffect, useState } from "react";
import Feature1 from "../../../assets/images/feature1.svg";
import feature2 from "../../../assets/images/feature2.svg";
import Feature3 from "../../../assets/images/feature3.svg";
import Feature4 from "../../../assets/images/feature4.svg";
import Feature5 from "../../../assets/images/feature5.svg";
import Feature6 from "../../../assets/images/feature6.svg";
import Feature7 from "../../../assets/images/feature7.svg";
import { withTranslation } from 'react-i18next';
import { Container, Row, Col } from "react-bootstrap";
import { Link } from "react-router-dom";

function Features(props) {
  const { t } = props;
  const [loading, setLoading] = useState(false);
  const [contents, setContents] = useState([]);

  useEffect(() => {
    const getSections = () => {
      setLoading(true);
      axios
        .get("/api/get-features-sections?lang=" + localStorage.lang)
        .then((response) => {
          setLoading(false);
          // console.log(response.data);
          // console.log(response.data[0].name);
          setContents(response.data.data);
        })
        .catch((error) => {
          setLoading(false);
        });
    };
    getSections();
  }, []);

  return (
    <>
      <section className="features-pg">
        <div className="container-width">

          {contents.slice(0, 1).map((content, index) => (
            <Row className="f-info" key={content.id}>
              {content.image && content.image_position == 1 ?
                <Col lg="5" className=" d-flex align-items-center justify-content-lg-end justify-content-center">
                  <div className="d-flex justify-content-lg-end justify-content-center ">
                    <img className="Feature2" src={content.image} alt={content.name} />
                  </div>
                </Col>
                : ""
              }
              <Col lg="7" className=" d-flex align-items-center mx-auto">
                <div className="detail-ft">
                  <div className="d-flex justify-content-sm-start justify-content-center">
                    <h2>{content.name}</h2>
                  </div>
                  <div className="d-flex">
                    <p>
                      {content.description}
                    </p>
                  </div>
                  {index == 0 ?
                    <div className="d-flex justify-content-md-start justify-content-center">
                      <Link to="/signin">
                        <button type="submit" className="growing-btn">
                          {t('Get Started')}
                        </button>
                      </Link>
                    </div>
                    : ""}
                </div>
              </Col>
              {content.image && content.image_position == 2 ?
                <Col lg="5" className=" d-flex align-items-center justify-content-lg-end justify-content-center">
                  <div className="d-flex justify-content-lg-end justify-content-center ">
                    <img className="Feature2" src={content.image} alt={content.name} />
                  </div>
                </Col>
                : ""
              }
            </Row>
          ))}
          <div className="d-flex flex-column justify-content-center align-items-center pag_content f-info">
            <div className="d-flex mt-2 justify-content-center image-w">
              <img src={Feature1} alt="Feature" />
            </div>
          </div>
          {contents.slice(1, 4).map((content, index) => (
            <Row className="f-info" key={content.id}>
              {content.image && content.image_position == 1 ?
                <Col lg="5" className=" d-flex align-items-center justify-content-lg-start justify-content-center ">
                  <div className="d-flex justify-content-lg-start justify-content-center ">
                    <img className="Feature2" src={content.image} alt={content.name} />
                  </div>
                </Col>
                : ""
              }
              <Col lg="7" className=" d-flex align-items-center mx-auto">
                <div className="detail-ft">
                  <div className="d-flex justify-content-sm-start justify-content-center">
                    <h2>{content.name}</h2>
                  </div>
                  <div className="d-flex">
                    <p>
                      {content.description}
                    </p>
                  </div>
                  {index % 2 != 0 ?
                    <div className="d-flex justify-content-md-start justify-content-center">
                      <Link to="/signup">
                        <button type="button" className="growing-btn">
                          {index == 1 ? t("Try Now") : t("Go for it")}
                        </button>
                      </Link>
                    </div>
                    : ""}
                </div>
              </Col>
              {content.image && content.image_position == 2 ?
                <Col lg="5" className=" d-flex align-items-center justify-content-lg-end justify-content-center">
                  <div className="d-flex justify-content-lg-end justify-content-center ">
                    <img className="Feature2" src={content.image} alt={content.name} />
                  </div>
                </Col>
                : ""
              }
            </Row>
          ))}
          <div className="d-flex flex-column justify-content-center align-items-center pag_content f-info">
            <div className="d-flex mt-2 justify-content-center image-w">
              <img src={Feature4} alt="Feature" />
            </div>
          </div>
          {contents.slice(4, 7).map((content, index) => (
            <Row className="f-info" key={content.id}>
              {content.image && content.image_position == 1 ?
                <Col lg="5" className=" d-flex align-items-center justify-content-lg-end justify-content-center">
                  <div className="d-flex justify-content-lg-end justify-content-center ">
                    <img className="Feature2" src={content.image} alt={content.name} />
                  </div>
                </Col>
                : ""
              }
              <Col lg="7" className=" d-flex align-items-center mx-auto">
                <div className="detail-ft">
                  <div className="d-flex justify-content-sm-start justify-content-center">
                    <h2>{content.name}</h2>
                  </div>
                  <div className="d-flex">
                    <p>
                      {content.description}
                    </p>
                  </div>
                  {index % 2 == 0 ?
                    <div className="d-flex justify-content-md-start justify-content-center">
                      <Link to="/signin">
                        <button type="submit" className="growing-btn">
                          {index == 0 ? t("See it Yourself") : t("Get Started")}
                        </button>
                      </Link>
                    </div>
                    : ""}
                </div>
              </Col>
              {content.image && content.image_position == 2 ?
                <Col lg="5" className=" d-flex align-items-center justify-content-lg-end justify-content-center">
                  <div className="d-flex justify-content-lg-end justify-content-center ">
                    <img className="Feature2" src={content.image} alt={content.name} />
                  </div>
                </Col>
                : ""
              }
            </Row>
          ))}
          <div className="d-flex flex-column justify-content-center align-items-center pag_content f-info">
            <div className="d-flex mt-2 justify-content-center image-w">
              <img src={Feature6} alt="Feature" />
            </div>
          </div>
        </div>
      </section>
    </>
  );
}

export default withTranslation()(Features);
