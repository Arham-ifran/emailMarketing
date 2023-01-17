import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Form, Button } from 'react-bootstrap';
import SiteLogo from '../../assets/images/main-logo.svg';
import Spinner from '../includes/spinner/Spinner';
import { withTranslation } from 'react-i18next';
import Swal from 'sweetalert2';
//import './VerifyUser.css';
function VerifyAccount(props) {
	const { t } = props;
	const [email, setEmail] = useState('');
	const [errors, setErrors] = useState([]);
	const [loading, setLoading] = useState(false);
	const [disabled, setDisabled] = useState(false);
	const [subscribed, setSubscribed] = useState(true);


	useEffect(() => {
		const verification = () => {
			let parseUriSegment = window.location.pathname.split("/");
			const verify = parseUriSegment[2];

			setLoading(true);

			const data = {
				id: verify
			}
			axios.post('/api/auth/verify-account?lang=' + localStorage.lang, data)
				.then(res => {
					setLoading(false);
					if (res.data.status) {
						// localStorage["country"] = res.data.data.country_id;
						axios.get("/api/get-country/" + res.data.data.country_id + "?lang=" + localStorage.lang)
							.then((response2) => {
								if (response2.data.country)
									localStorage["country"] = response2.data.country.code;
								localStorage["jwt_token"] = res.data.token_type + ' ' + res.data.access_token;
								localStorage["user_name"] = res.data.data.name;
								// localStorage["lang"] = res.data.data.language || 'en';
								localStorage["package_id"] = res.data.data.package_id;
								localStorage["timezone"] = Intl.DateTimeFormat().resolvedOptions().timeZone
								window.location.href = '/dashboard';
							})
							.catch((errors2) => {
								localStorage["jwt_token"] = res.data.token_type + ' ' + res.data.access_token;
								localStorage["user_name"] = res.data.data.name;
								// localStorage["lang"] = res.data.data.language || 'en';
								localStorage["package_id"] = res.data.data.package_id;
								localStorage["timezone"] = Intl.DateTimeFormat().resolvedOptions().timeZone
								window.location.href = '/dashboard';
							})
					}

				})
				.catch(error => {
					setLoading(false);
					setSubscribed(false)
				})
		}
		verification()
	}, []);

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
			<div className="login-outer d-flex justify-content-center align-items-center">
				<div className="login-form-holder">
					<div className="logo-holder d-flex justify-content-center mb-4">
						<strong className="logo">
							<Link to="/"><img src={SiteLogo} alt="Site Logo" /></Link>
						</strong>
					</div>
					{subscribed ?
						<Form className="login-form text-center">
							<Form.Group className="mb-2 mb-md-3 d-flex flex-column">
								<Form.Label className="mb-2" for="email">{t('You Email Address has been verified')}</Form.Label>
							</Form.Group>
							<Link to='/signin' className="btn btn-primary" disabled={disabled ? true : false}>
								<span>{t('Signin')}</span>
							</Link>
						</Form>
						:
						<Form className="login-form text-center">
							<Form.Group className="mb-2 mb-md-3 d-flex flex-column">
								<Form.Label className="mb-2" for="email">{t('There was a problem verifying your email address.')}</Form.Label>
								<Form.Label className="mb-2" for="email">{t('Please try again or contact our support team.')}</Form.Label>
							</Form.Group>
						</Form>
					}
				</div>
			</div>
		</React.Fragment>
	);
}

export default withTranslation()(VerifyAccount);