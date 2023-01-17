import React, { useEffect, useState } from 'react';
import { Row, Col } from 'react-bootstrap';
import { Link, useHistory } from 'react-router-dom';
import { Form, Button } from 'react-bootstrap';
import SiteLogo from '../../assets/images/main-logo.svg';
import SignUpImg from '../../assets/images/signup.svg';
import GoogleIcon from '../../assets/images/google.png';
import Spinner from '../includes/spinner/Spinner';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faFacebookF, faTwitter, faGoogle } from '@fortawesome/free-brands-svg-icons'
import { faEye } from '@fortawesome/free-solid-svg-icons'
import { faEyeSlash } from '@fortawesome/free-solid-svg-icons'
import Swal from 'sweetalert2';
import * as Constants from "../../constants";
import GetAuthMetaData from '../helpers/GetAuthMetaData';
import './SignUp.css';
import Select from 'react-select';
import ReCAPTCHA from "react-google-recaptcha";
import { withTranslation } from 'react-i18next';

var countries = [];
var timezones = [];
function SignUp(props) {
	const { t } = props;
	const [name, setName] = useState('');
	const [email, setEmail] = useState('');
	const [password, setPassword] = useState('');
	const [passwordconfirmation, setPasswordconfirmation] = useState('');
	const [errors, setErrors] = useState([]);
	const [loading, setLoading] = useState('');
	const [disabled, setDisabled] = useState('');
	const [agreed, setAgreed] = useState(false);
	const [country, setCountry] = useState('');
	const [selectedCountry, setSelectedCountry] = useState('');
	const [timezone, setTimezone] = useState('');
	const [selectedTimezone, setSelectedTimezone] = useState('');
	const [showPassword, setShowPassword] = useState(false);
	const [showVerifyPassword, setShowVerifyPassword] = useState(false);
	const [captchaVal, setCaptchaVal] = useState(false);

	useEffect(() => {
		if (localStorage.jwt_token) {
			window.location.href = '/dashboard';
		}
		setLoading(true);
		axios.get('/api/get-countries?lang=' + localStorage.lang)
			.then(response => {
				let data = response.data.countries;
				data.map(country => (
					countries.push({ value: country.id, label: country.name })
				))
				// // console.log(countries);
				setLoading(false);

			})
			.catch(error => {
				setLoading(false);
			})

		// setLoading(true);
		// axios.get('/api/timezones?lang=' + localStorage.lang)
		// 	.then(response => {
		// 		let data = response.data.data;
		// 		data.map(timezone => (
		// 			timezones.push({ value: timezone.id, label: timezone.name + "  ( " + timezone.utc_offset + " )" })
		// 		))
		// 		setLoading(false);

		// 	})
		// 	.catch(error => {
		// 		setLoading(false);
		// 	})
	}, []);


	function onChange(value) {
		setCaptchaVal(value);
	}

	const handleCountryChange = (selectedOption) => {
		setSelectedCountry(selectedOption);
		setCountry(selectedOption.value);
	}

	const handleTimezoneChange = (selectedOption) => {
		setSelectedTimezone(selectedOption);
		setTimezone(selectedOption.value);
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

	const handleConnectAccount = (event, auth_id) => {

		event.preventDefault();
		//const { t } = this.props;
		let auth_slug = GetAuthMetaData(auth_id).slug;
		// console.log(auth_slug);
		setLoading(true);
		axios.get('/api/auth/' + auth_slug + '-auth-url?lang=' + localStorage.lang)
			.then(response => {
				// console.log(response);
				window.location.href = response.data.url;
			})
			.catch(error => {
				// console.log(error)
				setLoading(false);
			})
	}


	const handleSubmit = (event) => {

		event.preventDefault();
		setErrors([])
		setLoading(true);

		if (name == "" && email == "" && country == "" && password == "" && passwordconfirmation == "") {
			setErrors({
				name: [name == "" ? t('required') : ''],
				email: [email == "" ? t('required') : ''],
				country_id: [country == "" ? t('required') : ''],
				// timezone_id: [timezone == "" ? t('required') : ''],
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
					if (res.response) {
						if (res.response.data.errors) {
							setErrors(res.response.data.errors);
							window.grecaptcha.reset();
						}
					} else {
						// console.log(res);
						if (res.data.status) {
							setName(false);
							setEmail(false);
							setPassword(false);
							setPasswordconfirmation(false);

							// window.location.href = "/verify/" + email;
							window.location.href = "/signin?resend=" + email;


							// history.push({
							// 	pathname:  "/OnSubmit",
							// 	state: {
							// 	  response: messageFromServer 
							// 	} 
							//  });
							//localStorage.removeItem('on_hold_package_id');
							//history.push("/signin");
						}
						//else {
						//	swal(t('oops'), res.data.message, "error", {
						//		button: t('ok'),
						//	});
						//}
					}
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

		<React.Fragment>

			{loading ? <Spinner /> : null}
			<section className="form-wrap">
				<div className="form-sign signout-form">
					<Row>
						<Col lg="6" className='order-1 order-lg-0'>
							<div className="form-inner-wrapper">
								<div className="form-header d-none d-lg-block">
									<strong>
										<Link to="/" className="navbar-brand">
											<img src={SiteLogo} alt="Logo" className="img-fluid" />
										</Link>
									</strong>
								</div>
								<div className="form-account-wrap">
									<form action="#" className="form-container" onSubmit={handleSubmit}>
										<div className="form-conetnt">
											<h3 className="text-uppercase">{t('CREATE YOUR ACCOUNT!')}</h3>
											<div className="btn-social">
												<Link className="btn btn-facebook" to="#" onClick={(event) => handleConnectAccount(event, Constants.AUTH_TYPE.FACEBOOK)}>
													<FontAwesomeIcon icon={faFacebookF} />{t('Login with facebook')}
												</Link>
												<Link className="btn btn-twitter" to="#" onClick={(event) => handleConnectAccount(event, Constants.AUTH_TYPE.TWITTER)}>
													<FontAwesomeIcon icon={faTwitter} />{t('Login with twitter')}
												</Link>
												<Link className="btn btn-google" to="#" onClick={(event) => handleConnectAccount(event, Constants.AUTH_TYPE.GOOGLE)}>
													<img src={GoogleIcon} alt="Logo" className="img-fluid google-logo" />{t('Login with google')}
												</Link>
											</div>
											<div className="form-line"><span>{t('Or')}</span></div>
											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group">
												<Form.Label for="name" className="form-label text-capitalize mb-0">{t('Full Name')}</Form.Label>
												<input type="text" className="form-control" placeholder="e.g. Sam Smith" id="InputName" onChange={(e) => setName(e.target.value)} />
											</div>
											{renderErrorFor('name')}
											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group">
												<Form.Label for="email" className="form-label text-capitalize mb-0">{t('Email')}</Form.Label>
												<input type="email" className="form-control" placeholder="e.g. example@email.com" onChange={(e) => setEmail(e.target.value)} id="InputEmail" />
											</div>
											{renderErrorFor('email')}
											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group signup-select">
												<Form.Label for="country" className="form-label mb-0">{t('Country')}</Form.Label>
												<div className="form-control">
													<div className="subscriber-select w-100">
														<Select
															onChange={(e) => handleCountryChange(e)}
															options={countries}
															classNamePrefix="react-select"
															value={selectedCountry}
															placeholder={t("Select Country")}
														/>
													</div>
												</div>
											</div>
											{renderErrorFor('country_id')}
											{/* <div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group signup-select">
												<Form.Label for="country" className="form-label mb-0">{t('Timezone')}</Form.Label>
												<div className="form-control">
													<div className="subscriber-select w-100">
														<Select
															onChange={(e) => handleTimezoneChange(e)}
															options={timezones}
															classNamePrefix="react-select"
															value={selectedTimezone}
															placeholder="Select Timezone"
														/>
													</div>
												</div>
											</div>
											{renderErrorFor('timezone_id')} */}
											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group position-relative password-field-wrap">
												<Form.Label for="password" className="form-label text-capitalize mb-0">{t('Password')}</Form.Label>
												<span className="eye-view-icon">
													<FontAwesomeIcon icon={faEye} className={showPassword ? "no-view-icon" : "view-icon"} onClick={() => setShowPassword(true)} />
													<FontAwesomeIcon icon={faEyeSlash} className={showPassword ? "view-icon" : "no-view-icon"} onClick={() => setShowPassword(false)} />
												</span>
												<input type={showPassword ? "text" : "password"} className="form-control" placeholder={t("Enter Password")} id="InputPassword" onChange={(e) => setPassword(e.target.value)} />
											</div>
											{renderErrorFor('password')}
											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group position-relative password-field-wrap">
												<Form.Label for="password" className="form-label text-capitalize mb-0">{t('Verify Password')}</Form.Label>
												<span className="eye-view-icon">
													<FontAwesomeIcon icon={faEye} className={showVerifyPassword ? "no-view-icon" : "view-icon"} onClick={() => setShowVerifyPassword(true)} />
													<FontAwesomeIcon icon={faEyeSlash} className={showVerifyPassword ? "view-icon" : "no-view-icon"} onClick={() => setShowVerifyPassword(false)} />
												</span>
												<input type={showVerifyPassword ? "text" : "password"} className="form-control" placeholder={t('Verify Password')} id="InputVerifyPassword" onChange={(e) => setPasswordconfirmation(e.target.value)} />
											</div>
											{renderErrorFor('password_confirmation')}
											<div className="form-content form-check d-flex">
												<input
													type="checkbox"
													className="form-check-input"
													id="termsCheckbox"
													onClick={() => setAgreed(!agreed)}
												/>
												<label className="form-check-label" for="exampleCheck1">
													<p className='termsSignupPage p-0'>
														{t('I agree to the')} <Link class="green-two" to="/pages/terms-and-conditions"> {t('terms_and_conditions')} </Link> & <Link class="green-two" to="/pages/privacy-policy">{t('Privacy Policy')}</Link>
													</p>
												</label>
											</div>
											{renderErrorFor('agreed')}
											<div className="d-flex captcha-des form-group flex-xl-row flex-column">
												<ReCAPTCHA
													sitekey="6LfOUwoeAAAAAJnqPp7Wy0cXmplFTWkLlJvdPFn1"
													onChange={onChange}
												/>
											</div>
											<div className='d-flex w-100'>
												{renderErrorFor('captcha')}
											</div>
											<div className='account-btn-wrapper d-flex align-items-center justify-content-between mt-2'>
												<Button type="submit" className="btn btn-primary">
													<span className="text-capitalize">{t('Sign Up')}</span>
												</Button>
												<div className="account">
													<span>{t('Have an Account?')}
														<Link to="/signin" className="account-btn"> {t('Sign In')}</Link>
													</span>
												</div>
											</div>
										</div>
									</form>
									
								</div>
							</div>

						</Col>
						<Col lg="6" className='order-0 order-lg-1'>
							<div className="form-header d-lg-none d-block ps-4">
								<strong>
									<Link to="/" className="navbar-brand">
										<img src={SiteLogo} alt="Logo" className="img-fluid" />
									</Link>
								</strong>
							</div>
							<div className="img-wrapper">
								<img src={SignUpImg} alt="" className="img-fluid" />
							</div>
						</Col>
					</Row>
				</div>
			</section>
		</React.Fragment>
	);
}

export default withTranslation()(SignUp);

