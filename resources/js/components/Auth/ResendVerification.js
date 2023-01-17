import React, { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { Row, Col, Form, Button } from 'react-bootstrap';
import SiteLogo from '../../assets/images/main-logo.svg';
import SignInImg from '../../assets/images/signin.svg';
import Spinner from '../includes/spinner/Spinner';
import Swal from 'sweetalert2';
import { withTranslation } from 'react-i18next';
//import './ForgotPassword.css';
function ResendVerification(props) {
	const { t } = props;
	const { email } = useParams();
	const [loading, setLoading] = useState(false);
	const [disabled, setDisabled] = useState(false);
	const [errors, setErrors] = useState([]);

	useEffect(() => {
		if (localStorage.jwt_token) {
			window.location.href = '/dashboard';
		}
	});


	const handleFormSubmit = (event) => {
		event.preventDefault()
		setLoading(true);
		setDisabled(true);
		setErrors([])

		const data = {
			email: email,
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
						<Col lg="6" className='order-1 order-lg-0 mb-lg-0 mb-5'>
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
											<h3 className="text-uppercase">{t('resend_verification_link')}</h3>
											<div> {t('Profile Created Please verify email and log in using the same credentials')} </div>
											<div className="form-group mb-lg-2 mb-md-2 mb-1 login-form-group">
												<Form.Label for="email" className="form-label text-capitalize mb-0">{t('Email')}</Form.Label>
												<input type="email" disabled className="form-control" placeholder="e.g. example@email.com" id="email" value={email} />
											</div>
											{renderErrorFor('email')}
											<Button type="submit" className="btn btn-primary" disabled={disabled ? true : false}>
												<span className="text-capitalize">{t('Submit')}</span>
											</Button>
										</div>
									</form>
								</div>
							</div>

						</Col>
						<Col lg="6" className='order-0 order-lg-1'>
							<div className="form-header d-lg-none d-block ps-4 pb-4">
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

export default withTranslation()(ResendVerification);