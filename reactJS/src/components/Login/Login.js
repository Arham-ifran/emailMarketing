import React from 'react';
import { Link } from 'react-router-dom';
import { Form, Button } from 'react-bootstrap';
import SiteLogo from '../../assets/images/logo.svg';
import './Login.css';
function Login(props) {
	return (
		<React.Fragment>
			<div className="login-outer d-flex justify-content-center align-items-center">
				<div className="login-form-holder">
					<div className="logo-holder d-flex justify-content-center mb-4">
						<strong className="logo">
							<img src={SiteLogo} alt="Site Logo" />
						</strong>
					</div>
					<Form className="login-form" action="/dashboard">
						<Form.Group className="mb-2 mb-md-3 d-flex flex-column">
							<Form.Label className="mb-2" for="email">Email</Form.Label>
							<div className="flex-fill input-holder">
								<input id="email" className="form-control" type="email" />
							</div>
						</Form.Group>
						<Form.Group className="mb-1 d-flex flex-column">
							<Form.Label className="mb-2" for="password">Password</Form.Label>
							<div className="flex-fill input-holder">
								<input id="password" className="form-control" type="password" />
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-3 d-flex justify-content-end">
							<Link className="text-theme" to="/forgot-password">Forgot Password?</Link>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-3 d-flex justify-content-between">
							<Button type="submit" className="btn btn-primary">
								<span>Login</span>
							</Button>
						</Form.Group>
						<hr />
						<Form.Group className="d-flex justify-content-center">
							<span>Don't have account?&nbsp;<Link className="text-theme" to="/signup">Sign Up</Link></span>
						</Form.Group>
					</Form>
				</div>
			</div>
		</React.Fragment>
	);
}

export default Login;