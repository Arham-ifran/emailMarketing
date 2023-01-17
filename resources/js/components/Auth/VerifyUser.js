import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Form, Button } from 'react-bootstrap';
import SiteLogo from '../../assets/images/main-logo.svg';
import Spinner from '../includes/spinner/Spinner';
import { withTranslation } from 'react-i18next';
import Swal from 'sweetalert2';
//import './VerifyUser.css';
function VerifyUser(props) {
	const { t } = props;
	const [email, setEmail] = useState('');
	const [errors, setErrors] = useState([]);
	const [loading, setLoading] = useState(false);
	const [disabled, setDisabled] = useState(false);
	const [subscribed, setSubscribed] = useState(false);


	useEffect(() => {
		let parseUriSegment = window.location.pathname.split("/");
		if (parseUriSegment[1] == 'verified')
			setSubscribed(true)
	});

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
		</React.Fragment >
	);
}

export default withTranslation()(VerifyUser);