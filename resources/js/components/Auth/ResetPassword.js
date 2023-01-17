import React, { Component, useState, useEffect } from 'react'
import { Link } from 'react-router-dom';
//import GuestHeader from "../sections/GuestHeader"
import { Row, Col, Form, Button } from 'react-bootstrap';
import SiteLogo from '../../assets/images/main-logo.svg';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEye } from '@fortawesome/free-solid-svg-icons'
import { faEyeSlash } from '@fortawesome/free-solid-svg-icons'
import SignUpImg from '../../assets/images/signup.svg';
import Spinner from '../includes/spinner/Spinner';
import Swal from 'sweetalert2';
import { useParams } from "react-router-dom";
import { withTranslation } from 'react-i18next';

function ResetPassword(props) {
	const { t } = props;
	const { token } = useParams();
	const [email, setEmail] = useState('');
	const [password, setPassword] = useState('');
	const [passwordconfirmation, setPasswordconfirmation] = useState('');
	const [errors, setErrors] = useState([]);
	const [loading, setLoading] = useState('');
	const [disabled, setDisabled] = useState('');
	const [showPassword, setShowPassword] = useState(false);
	const [showVerifyPassword, setShowVerifyPassword] = useState(false);

	useEffect(() => {
		if (localStorage.jwt_token) {
			window.location.href = '/dashboard';
		}
		const get_email = () => {
			axios.get('/api/auth/password/validate-reset-token/' + token + '?lang=' + localStorage.lang)
				.then(res => {
					setLoading(false);
					setErrors([]);
					if (res.data.status) {
						setEmail(res.data.data.email);
					}
					else {
						setErrors({
							token: [res.data.message],
						});
					}
				})
				.catch(error => {
					setLoading(false);
					if (error.response.data.errors) {
						setErrors(error.response.data.errors)
					}
				})
		}
		get_email();
	}, []);


	const handleFormSubmit = (event) => {
		event.preventDefault()

		setLoading(true);
		setDisabled(true);
		setErrors([])

		if (token == "") {
			setErrors({ token: [t('Reset Password Token not found.')] });
			setLoading(false);
			setDisabled(false);
			return;
		}

		if (email == "" && password == "" && passwordconfirmation == "") {
			setErrors({
				email: [email == "" ? t('required') : ''],
				password: [password == "" ? t('required') : ''],
				password_confirmation: [passwordconfirmation == "" ? t('required') : ''],
			});
			setLoading(false);
			setDisabled(false);
			return;
		}

		const data = {
			token: token,
			email: email,
			password: password,
			password_confirmation: passwordconfirmation
		}

		axios.post('/api/auth/password/reset?lang=' + localStorage.lang, data)
			.then(res => {

				setLoading(false);
				setDisabled(false);
				setErrors([]);

				if (res.response) {
					if (res.response.data.errors) {
						setErrors(res.response.data.errors)
					}
				} else {
					if (res.data.status) {


						Swal.fire({
							//title: 'Are you sure?',
							text: res.data.message,
							icon: 'success',
							showCancelButton: false,
							confirmButtonText: t('OK'),
							//cancelButtonText: 'No, keep it'
						}).then((result) => {
							window.location.href = "/signin";
						})
					}
					else {
						console.log();
						setErrors({
							email: [t('Email not found or Token Expired.')],
						});
					}
				}
			})
			.catch(error => {
				setLoading(false);
				setDisabled(false);


				if (error.response.data.errors) {
					setErrors(error.response.data.errors)
				}
			})
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



	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<section className="form-wrap">
				<div className="form-sign forget-form">
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
									<form className="form-container" onSubmit={handleFormSubmit}>
										<div className="form-conetnt">
											<h3 className="text-uppercase">{t('Reset Password')}</h3>
											{renderErrorFor('token')}
											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group">
												<Form.Label for="email" className="form-label text-capitalize mb-0">{t('Email')}</Form.Label>
												<input type="email" disabled id="email" value={email} className="form-control" placeholder="e.g. example@email.com" />
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
											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group position-relative password-field-wrap">
												<Form.Label for="password" className="form-label text-capitalize mb-0">{t('Verify Password')}</Form.Label>
												<span className="eye-view-icon">
													<FontAwesomeIcon icon={faEye} className={showVerifyPassword ? "no-view-icon" : "view-icon"} onClick={() => setShowVerifyPassword(true)} />
													<FontAwesomeIcon icon={faEyeSlash} className={showVerifyPassword ? "view-icon" : "no-view-icon"} onClick={() => setShowVerifyPassword(false)} />
												</span>
												<input type={showVerifyPassword ? "text" : "password"} className="form-control" placeholder={t("Verify Password")} id="password_confirmation" onChange={(e) => setPasswordconfirmation(e.target.value)} />
											</div>
											{renderErrorFor('password_confirmation')}
											<Button type="submit" className="btn btn-primary" disabled={disabled}>
												<span className="text-capitalize">{t('Submit')}</span>
											</Button>
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

export default withTranslation()(ResetPassword);