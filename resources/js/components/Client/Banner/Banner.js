import React, { useEffect, useState, useRef } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faEye } from '@fortawesome/free-solid-svg-icons'
import { faEyeSlash } from '@fortawesome/free-solid-svg-icons'
import { faPlay } from "@fortawesome/free-solid-svg-icons";
import { Container, Row, Col } from "react-bootstrap";
import Spinner from '../../includes/spinner/Spinner';
import Swal from 'sweetalert2';
import Feature1 from "../../../assets/images/feature1.svg";
import { set } from "lodash";
import Select from 'react-select';
import ReCAPTCHA from "react-google-recaptcha";
import { withTranslation } from 'react-i18next';
import { Link } from "react-router-dom";

var countries = [];
function Banner(props) {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordconfirmation, setPasswordconfirmation] = useState('');
  const [errors, setErrors] = useState([]);
  const [loading, setLoading] = useState('');

  const [sectionText, setSectionText] = useState();
  const [bannerVideoLink, setBannerVideoLink] = useState();
  const [contents, setContents] = useState([]);
  const [agreed, setAgreed] = useState(false);
  const [country, setCountry] = useState('');
  const [selectedCountry, setSelectedCountry] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [showVerifyPassword, setShowVerifyPassword] = useState(false);
  const [captchaVal, setCaptchaVal] = useState(false);

  const { t } = props;
  useEffect(() => {
    if (props.contents.length) {
      setContents(props.contents);
      var allcontents = props.contents;
      setSectionText(allcontents.find(content => content.id == 11));
      setBannerVideoLink(allcontents.find(content => content.id == 1));
    }
    // setLoading(true);
    axios.get('/api/get-countries?lang=' + localStorage.lang)
      .then(response => {
        let data = response.data.countries;
        countries = [];
        data.map(country => (
          countries.push({ value: country.id, label: country.name })
        ))
        // setLoading(false);

      })
      .catch(error => {
        // setLoading(false);
      })
  }, [props]);

  const handleCountryChange = (selectedOption) => {
    setSelectedCountry(selectedOption);
    setCountry(selectedOption.value);
  }

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

    event.preventDefault();
    setLoading(true);
    setErrors([])

    if (name == "" && email == "" && country == "" && password == "" && passwordconfirmation == "") {
      setErrors({
        name: [name == "" ? t('required') : ''],
        email: [email == "" ? t('required') : ''],
        country_id: [country == "" ? t('required') : ''],
        password: [password == "" ? t('required') : ''],
        password_confirmation: [passwordconfirmation == "" ? t('required') : ''],
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
      password: password,
      password_confirmation: passwordconfirmation,
      agreed: agreed,
      country_id: country,
      timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
      captcha_token: captchaVal
    };
    axios.post('/api/auth/register?lang=' + localStorage.lang, data)
      .then(res => {
        setLoading(false);
        if (res.data.status == 0) {
          setErrors({
            captcha: [res.data.captcha],
          });
          window.grecaptcha.reset();
        } else {
          setName('');
          setEmail('');
          setPassword('');
          setPasswordconfirmation('');
          // window.location.href = "/verify/" + email;
          window.location.href = "/signin?resend=" + email;
          // Swal.fire({
          //   title: t('Success'),
          //   text: t('Profile Created! Please verify email and log in using the same credentials.'),
          //   icon: 'success',
          //   showCancelButton: false,
          //   confirmButtonText: 'OK',
          //   //cancelButtonText: 'No, keep it'
          // }).then(() => {
          //   window.location.href = "/signin";
          // })
        }
      })
      .catch(error => {
        setLoading(false);
        window.grecaptcha.reset();
        if (error.response.data.errors) {
          setErrors(error.response.data.errors);
        }
      })
  }

  return (
    <>
      {loading ? <Spinner /> : null}
      <section className="main">
        <div className="container-width">
          <Row>
            <Col lg="7" xs="12" className=" d-flex align-items-center">
              <div className="main-content">
                {sectionText ?
                  <div dangerouslySetInnerHTML={{ __html: sectionText.description }} />
                  : ""}

                {bannerVideoLink ?
                  <>
                    <a
                      className="main-btn mt-4"
                      href={bannerVideoLink.description}
                      target="_blank">
                      <FontAwesomeIcon className="me-4" icon={faPlay} />
                      {t('Play Demo')}
                    </a>
                  </>
                  : ""}

              </div>
            </Col>
            {!localStorage.jwt_token ?
              <Col lg="5">
                <form className="main-form" onSubmit={handleSubmit}>
                  <div className="form-heading">
                    <h1>
                      {t('get_started_now')}{" "}
                      <Link to="/signup" className="yellow">
                        {" "}
                        {t('It’s Free')}
                      </Link>
                    </h1>
                  </div>
                  <div className="form-content">
                    <input
                      placeholder={`${t('Full Name')}*`}
                      type="text"
                      className="form-control header-fields"
                      onChange={(e) => setName(e.target.value)}
                    />
                    {renderErrorFor('name')}
                  </div>

                  <div className="form-content">
                    <Select
                      onChange={(e) => handleCountryChange(e)}
                      options={countries}
                      classNamePrefix="react-select"
                      value={selectedCountry}
                      placeholder={`${t('Select Country')}*`}
                    />
                    {renderErrorFor('country_id')}
                  </div>

                  <div className="form-content">
                    <input
                      placeholder={`${t('Email')}*`}
                      type="email"
                      className="form-control header-fields"
                      onChange={(e) => setEmail(e.target.value)}
                    />
                    {renderErrorFor('email')}
                  </div>
                  <div className="form-content position-relative password-field-wrap">
                    <span className="eye-view-icon">
                      <FontAwesomeIcon icon={faEye} className={showPassword ? "no-view-icon" : "view-icon"} onClick={() => setShowPassword(true)} />
                      <FontAwesomeIcon icon={faEyeSlash} className={showPassword ? "view-icon" : "no-view-icon"} onClick={() => setShowPassword(false)} />
                    </span>
                    <input
                      placeholder={`${t('Password')}*`}
                      type={showPassword ? "text" : "password"}
                      className="form-control header-fields"
                      onChange={(e) => setPassword(e.target.value)}
                    />
                    {renderErrorFor('password')}
                  </div>
                  <div className="form-content position-relative password-field-wrap">
                    <span className="eye-view-icon">
                      <FontAwesomeIcon icon={faEye} className={showVerifyPassword ? "no-view-icon" : "view-icon"} onClick={() => setShowVerifyPassword(true)} />
                      <FontAwesomeIcon icon={faEyeSlash} className={showVerifyPassword ? "view-icon" : "no-view-icon"} onClick={() => setShowVerifyPassword(false)} />
                    </span>
                    <input
                      placeholder={`${t('Verify Password')}*`}
                      type={showVerifyPassword ? "text" : "password"}
                      className="form-control header-fields"
                      onChange={(e) => setPasswordconfirmation(e.target.value)}
                    />
                    {renderErrorFor('password_confirmation')}
                  </div>
                  <div className="home-form form-content form-check d-flex">
                    <div className="d-flex">
                      <input
                        type="checkbox"
                        className="form-check-input"
                        id="exampleCheck1"
                        onClick={() => setAgreed(!agreed)}
                      />
                    </div>
                    <div className="d-flex">
                      <label className="form-check-label" for="exampleCheck1">
                        <p>
                          {t('I agree to the')} <Link className="green-two" to="/pages/terms-and-conditions"> {t('terms_and_conditions')} </Link> {" & "}
                          <Link className="green-two" to="/pages/privacy-policy"> {t('Privacy Policy')}</Link>
                        </p>
                        {renderErrorFor('agreed')}
                      </label>
                    </div>
                  </div>
                  <div className="d-flex flex-column mt-3 captcha-des">
                    <ReCAPTCHA
                      sitekey="6LfOUwoeAAAAAJnqPp7Wy0cXmplFTWkLlJvdPFn1"
                      onChange={onChange}
                    />
                    <div className="d-flex">
                      {renderErrorFor('captcha')}
                    </div>
                  </div>
                  <button type="submit" className="form-btn">
                    {t('Sign Up For Free')}
                  </button>
                </form>
              </Col>
              :
              <Col md="5">

                <div className="d-flex flex-column justify-content-center align-items-center pag_content f-info">
                  <div className="d-flex justify-content-center image-w">
                    <img src={Feature1} alt="Feature" className="m-0" />
                  </div>
                </div>
              </Col>
            }
          </Row>
        </div>
      </section>
    </>
  );
}

export default withTranslation()(Banner);
