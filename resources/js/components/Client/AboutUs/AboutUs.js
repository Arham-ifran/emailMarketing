import React, { useEffect, useState } from "react";
import about1 from "../../../assets/images/about-1.svg";
import about2 from "../../../assets/images/about-2.svg";
import email from "../../../assets/images/email.png";
import sms from "../../../assets/images/sms.png";
import analytics from "../../../assets/images/analytics.png";
import Slider from "react-slick";
import { Container, Row, Col } from "react-bootstrap";
import { withTranslation } from 'react-i18next';
import { Link } from "react-router-dom";

function AboutUs(props) {
  var settings = {
    dots: true,
    arrows: false,
    infinite: true,
    speed: 500,
    centerPadding: "10",
    slidesToShow: 2,
    slidesToScroll: 1,
    autoplay: true,
    responsive: [
      {
        breakpoint: 576,
        settings: {
          slidesToShow: 1,
          centerMode: false,
        },
      },
    ],
  };

  const [loading, setLoading] = useState(false);
  const [contents, setContents] = useState([]);
  const [quotes, setQuotes] = useState([]);
  const { t } = props;
  useEffect(() => {
    const getSections = () => {
      setLoading(true);
      axios
        .get("/api/get-about-us-sections?lang=" + localStorage.lang)
        .then((response) => {
          setLoading(false);
          console.log(response.data.data);
          setContents(response.data.data);
        })
        .catch((error) => {
          setLoading(false);
        });
    };
    getSections();

    const getQuotes = () => {
      setLoading(true);
      axios
        .get("/api/get-about-us-testimonials?lang=" + localStorage.lang)
        .then((response) => {
          setLoading(false);
          // console.log(response.data);
          setQuotes(response.data.data);
        })
        .catch((error) => {
          setLoading(false);
        });
    };
    getQuotes();
  }, []);

  return (
    <>
      {contents.slice(0, 2).map(content => (
        <section key={content.id} className={content.id == 11 ? "p-0" : "p-0 email-info"}>
          <div className="container-width">
            <div className="about-baner">
              <Row className="f-info" key={content.id}>
                {content.image && content.image_position == 1 ?
                  <Col
                    lg="5"
                    className=" d-flex align-items-center justify-content-lg-end justify-content-center "
                  >
                    <div className="d-flex justify-content-lg-end justify-content-center ">
                      <img className="Feature2" src={content.image} alt={content.name} />
                    </div>
                  </Col>
                  : ""
                }
                <Col lg="7" className=" d-flex align-items-center mx-auto">
                  <div className="detail-ft">
                    <div className="d-flex">
                      <h2>{content.name}</h2>
                    </div>
                    <div className="d-flex">
                      <p>
                        {content.description}
                      </p>
                    </div>
                  </div>
                </Col>
                {content.image && content.image_position == 2 ?
                  <Col
                    lg="5"
                    className=" d-flex align-items-center justify-content-lg-end justify-content-center "
                  >
                    <div className="d-flex justify-content-lg-end justify-content-center ">
                      <img className="Feature2" src={content.image} alt={content.name} />
                    </div>
                  </Col>
                  : ""
                }
              </Row>
            </div>
          </div>
        </section>
      ))}
      <section className="email-info">
        <div className="container-width">
          <Row className="justify-content-center mb-5">
            <Col lg="12">
              <h2 className="text-center mt-3 mb-5 pb-3">{t('With Email Marketing You Can')}</h2>
            </Col>
            {contents.slice(2, 5).map(content => (
              <Col lg="4" md-6 key={content.id}>
                <div className="compaigns">
                  <div className="circle">
                    {" "}
                    <img src={content.image} alt={content.name} />
                  </div>
                  <div className="bottom-content">
                    <h2>{content.name}</h2>
                    <p>
                      {content.description}
                    </p>
                  </div>
                </div>
              </Col>
            ))}
          </Row>
        </div>
      </section>

      <section className="whatPeoplesay">
        <div className="container-width">
          <div className="d-flex align-items-center justify-content-center">
            <h2>{t('What People Say About Us')}</h2>
          </div>
          <Row className="align-items-center">
            <Col lg="12">
              <Slider {...settings} className="about-carousel">
                {quotes.map(quote => (
                  <div key={quote.id}>
                    <div className="card-bg">
                      <p>
                        {quote.message}
                      </p>
                      <h4>{quote.name}</h4>
                    </div>
                  </div>
                ))}
              </Slider>
            </Col>
          </Row>
        </div>
      </section>

      <section className="get-started">
        <div className="container-width">
          <div className="get-started-content d-flex flex-md-row flex-column align-items-center justify-content-center">
            <h2 className="all-h2">{t('Ready To Get Started?')}</h2>
            <Link to="/signup">
              <button type="button" className="get-started-btn mt-md-2 mt-3">{t('Go for it')}</button>
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}

export default withTranslation()(AboutUs);
