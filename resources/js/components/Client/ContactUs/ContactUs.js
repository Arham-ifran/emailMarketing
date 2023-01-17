import React, { useEffect, useState } from "react";
import { Link, useHistory } from "react-router-dom";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faInfoCircle } from "@fortawesome/free-solid-svg-icons"
import { Row, Col, Form, Button, Container } from "react-bootstrap";
import Spinner from '../../includes/spinner/Spinner';
import Swal from 'sweetalert2';
import ReCAPTCHA from "react-google-recaptcha";
import { withTranslation } from 'react-i18next';
import {
  faMapMarker,
  faPhone,
  faEnvelope,
  faGlobeAsia,
} from "@fortawesome/free-solid-svg-icons";

function ContactUs(props) {
  const { t } = props;
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [subject, setSubject] = useState('');
  const [message, setMessage] = useState('');
  const [errors, setErrors] = useState([]);
  const [loading, setLoading] = useState('');

  const [address, setAddress] = useState('');
  const [number, setNumber] = useState('');
  const [contactEmail, setContactEmail] = useState('');
  const [website, setWebsite] = useState('');
  const [captchaVal, setCaptchaVal] = useState(false);

  const hasErrorFor = (field) => {
    return !!errors[field]
  }

  const renderErrorFor = (field) => {
    if (hasErrorFor(field)) {
      return (
        <span className='invalid-feedback'>
          <strong>{errors[field][0]}</strong>
        </span>
      )
    }
  }

  function onChange(value) {
    setCaptchaVal(value);
  }

  const handleSubmit = (event) => {

    setErrors([]);
    event.preventDefault();
    setLoading(true);

    if (email == "" && name == "" && subject == "" && message == "") {
      setErrors({
        name: [name == "" ? t('required') : ''],
        email: [email == "" ? t('required') : ''],
        subject: [subject == "" ? t('required') : ''],
        message: [message == "" ? t('required') : ''],
      });
      setLoading(false);
      return;
    }

    if (captchaVal == "") {
      setErrors({
        captcha: [captchaVal == "" ? t('check_to_proceed') : ''],
      });
      setLoading(false);
      return;
    }

    const data = {
      name: name,
      email: email,
      subject: subject,
      message: message,
      captcha_token: captchaVal
    };

    axios
      .post("/api/contact-us?lang=" + localStorage.lang, data)
      .then((res) => {
        if (res.data.status == 0) {
          window.grecaptcha.reset();
          setErrors({
            captcha: [res.data.captcha],
          });
        } else {
          setName('');
          setEmail('');
          setSubject('');
          setMessage('');
          setLoading(false);
          Swal.fire({
            title: t('Success'),
            text: 'Your Message is received!',
            icon: 'success',
            showCancelButton: false,
            confirmButtonText: 'OK',
          }).then((result) => {
          });
        }
      })
      .catch((error) => {
        window.grecaptcha.reset();
        if (error.response.data.errors) {
          setErrors(error.response.data.errors);
        }
        setLoading(false);
      });
  }

  const getDetails = () => {
    axios
      .get("/api/get-contact-us-details?lang=" + localStorage.lang)
      .then((res) => {
        setLoading(false);
        // console.log(res.data);
        var data = res.data;
        setAddress(data.office_address);
        setNumber(data.contact_number);
        setContactEmail(data.contact_email);
        setWebsite(data.website);
      })
      .catch((error) => {
        setLoading(false);
      });
  }
  useEffect(() => {
    getDetails();
  }, [])

  return (
    <>
      {loading ? <Spinner /> : null}
      <section className="ftco-section">
        <Container>
          <Row className="justify-content-center">
            <Col md="6" className="text-center mb-5">
              <h2 className="heading-section">{t('Get In Touch')}</h2>
            </Col>
          </Row>
          <Row className="justify-content-center">
            <Col md="12">
              <div className="wrapper">
                <Row className="no-gutters mb-5">
                  <Col md="7">
                    <div className="contact-wrap w-100 p-md-5 p-4">
                      <h3 className="mb-4">{t('Contact Us')}</h3>
                      <div id="form-message-warning" className="mb-4"></div>
                      {/* <div id="form-message-success" className="mb-4">
                        Your message was sent, thank you!
                      </div> */}
                      <Form
                        id="contactForm"
                        name="contactForm"
                        className="contactForm"
                        onSubmit={handleSubmit}
                      >
                        <Row>
                          <Col md="6">
                            <div className="form-group">
                              <label className="label" for="name">
                                {t('Full Name')} <b className="req-sign">*</b>
                              </label>
                              <input
                                type="text"
                                className="form-control"
                                name="name"
                                id="name"
                                value={name}
                                placeholder={`${t('Name')}*`}
                                onChange={(e) => setName(e.target.value)}
                              />
                              {renderErrorFor('name')}
                            </div>
                          </Col>
                          <Col md="6">
                            <div className="form-group">
                              <label className="label" for="email">
                                {t('Email Address')} <b className="req-sign">*</b>
                              </label>
                              <input
                                type="text"
                                className="form-control"
                                name="email"
                                id="email"
                                value={email}
                                placeholder={`${t('Email')}*`}
                                onChange={(e) => setEmail(e.target.value)}
                              />
                              {renderErrorFor('email')}
                            </div>
                          </Col>
                          <Col md="12">
                            <div className="form-group">
                              <label className="label" for="subject">
                                {t('Subject')} <b className="req-sign">*</b>
                              </label>
                              <input
                                type="text"
                                className="form-control"
                                name="subject"
                                id="subject"
                                value={subject}
                                placeholder={`${t('Subject')}*`}
                                onChange={(e) => setSubject(e.target.value)}
                              />
                              {renderErrorFor('subject')}
                            </div>
                          </Col>
                          <Col md="12">
                            <div className="form-group">
                              <label className="label" for="#">
                                {t('Message')} <b className="req-sign">*</b>
                              </label>
                              <textarea
                                rows="5"
                                maxLength='250'
                                className="form-control "
                                name="message"
                                value={message}
                                placeholder={`${t('Message')}*`}
                                onChange={(e) => setMessage(e.target.value)}
                              ></textarea>
                              {renderErrorFor('message')}
                            </div>
                            <small> {250 - message.length} {t('characters_remaining')} </small>
                            <p>
                              <FontAwesomeIcon icon={faInfoCircle}></FontAwesomeIcon>
                              {" "}
                              {t('limit_is_250_characters_including_spaces')}
                            </p>
                          </Col>
                          <Col md="12">
                            <div className="d-flex captcha-des form-group">
                              <ReCAPTCHA
                                sitekey="6LfOUwoeAAAAAJnqPp7Wy0cXmplFTWkLlJvdPFn1"
                                onChange={onChange}
                              />
                              {renderErrorFor('captcha')}
                            </div>
                          </Col>
                          <Col md="12">
                            <div className="form-group">
                              <input
                                type="submit"
                                value={t('Send Message')}
                                className="btn btn-primary"
                              />
                              <div className="submitting"></div>
                            </div>
                          </Col>
                        </Row>
                      </Form>
                    </div>
                  </Col>
                  <Col md="5" className="d-flex">
                    <div id="map">
                      <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2442.4277044023374!2d10.466925315797518!3d52.25377787976468!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47aff633f1712015%3A0xf0272e235929f1af!2sErftstra%C3%9Fe%2015%2C%2038120%20Braunschweig!5e0!3m2!1sen!2sde!4v1603353933229!5m2!1sen!2sde"
                        width="100%"
                        height="100%"
                        allowfullscreen=""
                        loading="lazy"
                      ></iframe>
                    </div>
                  </Col>
                </Row>
                <Row>
                  <Col md="3">
                    <div className="dbox w-100 text-center">
                      <div className="icon d-flex align-items-center justify-content-center">
                        <span className="fa fa-map-marker">
                          <FontAwesomeIcon icon={faMapMarker} />
                        </span>
                      </div>
                      <div className="text">
                        <p>
                          <span>{t('Address')}:</span>
                          {address}
                        </p>
                      </div>
                    </div>
                  </Col>
                  <Col md="3">
                    <div className="dbox w-100 text-center">
                      <div className="icon d-flex align-items-center justify-content-center">
                        <span className="fa fa-phone">
                          <FontAwesomeIcon icon={faPhone} />
                        </span>
                      </div>
                      <div className="text">
                        <p>
                          <span>{t('Phone')}:</span>
                          {number}
                        </p>
                      </div>
                    </div>
                  </Col>
                  <Col md="3">
                    <div className="dbox w-100 text-center">
                      <div className="icon d-flex align-items-center justify-content-center">
                        <span className="fa fa-paper-plane">
                          <FontAwesomeIcon icon={faEnvelope} />
                        </span>
                      </div>
                      <div className="text">
                        <p>
                          <span>{t('Email')}:</span>
                          {contactEmail}
                        </p>
                      </div>
                    </div>
                  </Col>
                  <Col md="3">
                    <div className="dbox w-100 text-center">
                      <div className="icon d-flex align-items-center justify-content-center">
                        <span className="fa fa-globe">
                          <FontAwesomeIcon icon={faGlobeAsia} />
                        </span>
                      </div>
                      <div className="text">
                        <p>
                          <span>{t('Website')}</span> <a href={website}>{website}</a>
                        </p>
                      </div>
                    </div>
                  </Col>
                </Row>
              </div>
            </Col>
          </Row>
        </Container>
      </section>
    </>
  );
}

export default withTranslation()(ContactUs);
