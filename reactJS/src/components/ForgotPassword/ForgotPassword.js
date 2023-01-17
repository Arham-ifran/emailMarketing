import React from 'react';
import { Link } from 'react-router-dom';
import { Form, Button } from 'react-bootstrap';
import SiteLogo from '../../assets/images/logo.svg';
import './ForgotPassword.css';
function ForgotPassword(props) {
	return (
		<React.Fragment>
			<div className="login-outer d-flex justify-content-center align-items-center">
				<div className="login-form-holder">
					<div className="logo-holder d-flex justify-content-center mb-4">
						<strong className="logo">
							<img src={SiteLogo} alt="Site Logo" />
						</strong>
					</div>
					<Form className="login-form" action="/signin">
						<Form.Group className="mb-2 mb-md-3 d-flex flex-column">
							<Form.Label className="mb-2" for="email">Email</Form.Label>
							<div className="flex-fill input-holder">
								<input id="email" className="form-control" type="email" />
							</div>
						</Form.Group>
						<Button type="submit" className="btn btn-primary">
							<span>Submit</span>
						</Button>
					</Form>
				</div>
			</div>
		</React.Fragment>
	);
}

export default ForgotPassword;