import React from 'react';
import { Container, Row, Form} from 'react-bootstrap';
import './Campaigns.css';
function Campaigns(props) {
	return (
		<React.Fragment>
			<Container fluid>
				<h2 className="mb-3 mb-mb-sm-4 mb-md-5">Create a Campaign</h2>
				<Form className="create-form-holder create-campaign-holder">
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="campaign-name">Campaign Name</Form.Label>
						<div className="flex-fill input-holder">
							<input id="campaign-name" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="sender-name">Sender Name</Form.Label>
						<div className="flex-fill input-holder">
							<input id="sender-name" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="sender-no">Sender Mobile no.</Form.Label>
						<div className="flex-fill input-holder">
							<input id="sender-no" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="sms-text">SMS Text</Form.Label>
						<div className="flex-fill input-holder">
							<textarea id="sms-text" rows="5" cols="5" className="form-control" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="reply-to-address">Reply to Address</Form.Label>
						<div className="flex-fill input-holder">
							<input id="reply-to-address" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex flex-column w-auto">
						<Form.Label for="reply-to-address">Set the Recursion Cycle:</Form.Label>
						<div className="flex-fill input-holder radio-btns-holder d-flex">
							<div className="radio-holder me-5">
								<label className="custom-radio">Weekly
									<input type="radio" name="radio" />
									<span className="checkmark"></span>
								</label>
							</div>
							<div className="radio-holder me-5">
								<label className="custom-radio">Monthly
									<input type="radio" name="radio" />
									<span className="checkmark"></span>
								</label>
							</div>
							<div className="radio-holder me-5">
								<label className="custom-radio">Yearly
									<input type="radio" name="radio" />
									<span className="checkmark"></span>
								</label>
							</div>
						</div>
					</Form.Group>
					<div className="btns-holder right-btns d-flex flex-row-reverse">
						<button className="btn btn-primary ms-3 mb-3"><span>Next</span></button>
						<button className="btn btn-secondary ms-3 mb-3"><span>Back</span></button>
					</div>
				</Form>
			</Container>
		</React.Fragment>
	);
}

export default Campaigns;