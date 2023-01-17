import React, { useState, useEffect } from 'react';
import { Row, Col, Form, Button } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import SiteLogo from '../../assets/images/main-logo.svg';
import SignInImg from '../../assets/images/signin.svg';
import GoogleIcon from '../../assets/images/google.png';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faFacebookF } from '@fortawesome/free-brands-svg-icons'
import { faTwitter } from '@fortawesome/free-brands-svg-icons'
import { faGoogle } from '@fortawesome/free-brands-svg-icons'
import { faEye } from '@fortawesome/free-solid-svg-icons'
import { faEyeSlash } from '@fortawesome/free-solid-svg-icons'
import Spinner from '../includes/spinner/Spinner';
import './Login.css';
import * as Constants from "../../constants";
import GetAuthMetaData from '../helpers/GetAuthMetaData';
import ReCAPTCHA from "react-google-recaptcha";
import { withTranslation } from 'react-i18next';
import Swal from 'sweetalert2';
function Login(props) {
	const { t } = props;
	const [email, setEmail] = useState('');
	const [password, setPassword] = useState('');
	const [errors, setErrors] = useState([]);
	const [loading, setLoading] = useState(false);
	const [disabled, setDisabled] = useState(false);
	const [accesstoken, setAccesstoken] = useState('');
	const [username, setUsername] = useState('');
	const [invalidError, setInvalidError] = useState('');
	const [showPassword, setShowPassword] = useState(false);
	const [captchaVal, setCaptchaVal] = useState(false);
	const [resend, setResend] = useState('');

	const hasErrorFor = (field) => {
		return !!errors[field]
	}

	const renderErrorFor = (field) => {
		if (hasErrorFor(field)) {
			return (
				<span className='invalid-feedback d-flex '>
					<strong className='text-red'>{errors[field][0]}</strong>
				</span>
			)
		}
	}

	useEffect(() => {
		let params = new URLSearchParams(window.location.search);
		if (params.get('resend')) {
			setResend(params.get('resend'))
		}
		window.history.pushState({}, document.title, window.location.pathname);

		if (localStorage.jwt_token) {
			window.location.href = '/dashboard';
		}
	});

	function onChange(value) {
		setCaptchaVal(value);
	}

	const handleSubmit = (event) => {

		event.preventDefault();
		setLoading(true);
		// setInvalidError('');
		setErrors([]);

		if (email == "" && password == "") {
			setErrors({
				email: [email == "" ? t('required') : ''],
				password: [password == "" ? t('required') : ''],
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
			email: email,
			password: password,
			captcha_token: captchaVal
		};
		axios.post('/api/auth/login?lang=' + localStorage.lang, data)
			.then(res => {

				if (res.response) {
					if (res.response.data.errors) {
						setErrors(res.response.data.errors);
						window.grecaptcha.reset();
					}
				} else {
					if (res.data.status) {
						axios.get("/admin/logout").then((errors) => { });

						axios.get("/api/get-country/" + res.data.data.country_id + "?lang=" + localStorage.lang)
							.then((response2) => {
								if (response2.data.country)
									localStorage["country"] = response2.data.country.code;
								localStorage["jwt_token"] = res.data.token_type + ' ' + res.data.access_token;
								localStorage["user_name"] = res.data.data.name;
								if (!localStorage.after_login)
									localStorage["package_id"] = res.data.data.package_id;
								localStorage["timezone"] = res.data.data.timezone;
								window.location.href = '/dashboard';
							})
							.catch((errors2) => {
								localStorage["jwt_token"] = res.data.token_type + ' ' + res.data.access_token;
								localStorage["user_name"] = res.data.data.name;
								if (!localStorage.after_login)
									localStorage["package_id"] = res.data.data.package_id;
								localStorage["timezone"] = res.data.data.timezone;
								window.location.href = '/dashboard';
							})

					} else {
						// setInvalidError(res.data.message);
						console.log(res.data.message);
						Swal.fire({
							text: res.data.message,
							icon: 'info',
							showCancelButton: false,
							confirmButtonText: t('OK'),
						})
						setErrors(res.response.data.errors);
						window.grecaptcha.reset();
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

	const handleConnectAccount = (event, auth_id) => {

		event.preventDefault();
		let auth_slug = GetAuthMetaData(auth_id).slug;
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

	const ResendEmail = (event) => {
		setLoading(true);
		setDisabled(true);
		setErrors([])

		const data = {
			email: resend,
		};

		axios.post('/api/auth/resend-verification-email?lang=' + localStorage.lang, data)
			.then(res => {
				setLoading(false);
				setDisabled(false);

				if (res.response) {
					if (res.response.data.errors) {
						setErrors(res.response.data.errors)
					}
				} else {
					if (res.data.status) {
						Swal.fire({
							//title: 'Are you sure?',
							text: t('verification_link_has_been_sent_to_your_account'),
							icon: 'success',
							showCancelButton: false,
							confirmButtonText: t('OK'),
							//cancelButtonText: 'No, keep it'
						}).then((result) => {
						})
					}
					else {
						setErrors({ 'email': [res.data.message] })
					}
				}
			})
			.catch(error => {
				setErrors(error)
				setLoading(false);
				setDisabled(false);
			})
	}

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<section className="form-wrap">
				<div className="form-sign login-form">
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
									<form className="form-container" onSubmit={handleSubmit}>
										<div className="form-conetnt">
											<h3 className="text-uppercase">{t('LOGIN TO YOUR ACCOUNT!')}</h3>
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



											{resend ?
												<div className='w-100 error-msg-box bg-primary mb-2 p-3'>
													<div className='d-flex flex-column align-items-center text-center'>
														<p>{t('Profile Created Please verify email and log in using the same credentials')} </p>
														<h3 className="text-uppercase">{t('resend_verification_link')}</h3>
														<p className='text-center'>A verification link has been sent to this Email Address:<b> {resend} </b> </p>
														<Button type="submit" className="btn btn-primary" disabled={disabled ? true : false} onClick={() => ResendEmail()}>
															<span className="text-capitalize">{t('Submit')}</span>
														</Button>
													</div>
												</div>
												: null
											}



											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group">
												<Form.Label for="email" className="form-label text-capitalize mb-0">{t('Email')}</Form.Label>
												<input type="email" className="form-control" placeholder="e.g. example@email.com" id="email" onChange={(e) => setEmail(e.target.value)} />
											</div>
											{renderErrorFor('email')}
											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group position-relative password-field-wrap">
												<Form.Label for="password" className="form-label text-capitalize mb-0">{t('Password')}</Form.Label>
												<span className="eye-view-icon">
													<FontAwesomeIcon icon={faEye} className={showPassword ? "no-view-icon" : "view-icon"} onClick={() => setShowPassword(true)} />
													<FontAwesomeIcon icon={faEyeSlash} className={showPassword ? "view-icon" : "no-view-icon"} onClick={() => setShowPassword(false)} />
												</span>
												<input type={showPassword ? "text" : "password"} className="form-control" placeholder={t('Enter Password')} id="password" onChange={(e) => setPassword(e.target.value)} />
											</div>
											{renderErrorFor('password')}
											<Form.Group className="mb-2 mb-md-3 d-flex justify-content-end">
												<Link className="text-theme" to="/forgot-password">{t('Forgot Password?')}</Link>
											</Form.Group>
											<span className='mb-2 mb-md-3 invalid-feedback'>
												<strong>{invalidError}</strong>
											</span>
											<div className="form-group d-flex captcha-des align-items-center mb-lg-2 mb-md-2 mb-1">
												<ReCAPTCHA
													sitekey="6LfOUwoeAAAAAJnqPp7Wy0cXmplFTWkLlJvdPFn1"
													onChange={onChange}
												/>
											</div>
											{renderErrorFor('captcha')}

											<div className='account-btn-wrapper d-flex align-items-center justify-content-between mt-2'>
												<Button type="submit" className="btn btn-primary">
													<span className="text-capitalize">{t('sign in')}</span>
												</Button>
												<div className="account">
													<span>{t('New User?')}
														<Link to="/signup" className="account-btn"> {t('Sign Up')}</Link>
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
								<img src={SignInImg} alt="" className="img-fluid" />
							</div>
						</Col>
					</Row>
				</div>
			</section>
		</React.Fragment>
	);
}

export default withTranslation()(Login);