import React from 'react';
import { Link } from 'react-router-dom';
import { Container, Row, Form} from 'react-bootstrap';
import './CreateSmsCampaign.css';
function CreateSmsCampaign(props) {
	return (
		<React.Fragment>
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>Create SMS Campaign</h1>
					</div>
				</div>
				<Form className="create-form-holder rounded-box-shadow bg-white">
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label className="mb-2 mb-md-0" for="campaign-name">Campaign Name</Form.Label>
						<div className="flex-fill input-holder">
							<input id="campaign-name" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label className="mb-2 mb-md-0" for="sender-name">Sender Name</Form.Label>
						<div className="flex-fill input-holder">
							<input id="sender-name" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label className="mb-2 mb-md-0" for="sender-no">Sender Mobile no.</Form.Label>
						<div className="flex-fill input-holder">
							<input id="sender-no" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label className="mb-2 mb-md-0" for="sms-text">SMS Text</Form.Label>
						<div className="flex-fill input-holder">
							<textarea id="sms-text" rows="5" cols="5" className="form-control" />
						</div>
					</Form.Group>
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label className="mb-2 mb-md-0" for="reply-to-address">Reply to Address</Form.Label>
						<div className="flex-fill input-holder">
							<input id="reply-to-address" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label className="mb-2 mb-md-0" for="reply-to-address">Do you want to set Recursive Cycle?</Form.Label>
						<div className="flex-fill input-holder radio-btns-holder d-flex">
							<div className="radio-holder me-3">
								<label className="custom-radio">Yes
									<input type="radio" name="radio" />
									<span className="checkmark"></span>
								</label>
							</div>
							<div className="radio-holder me-3">
								<label className="custom-radio">No
									<input type="radio" name="radio" />
									<span className="checkmark"></span>
								</label>
							</div>
						</div>
						{/* The following div will be populated when YES is selected */}
						{/* <div className="flex-fill input-holder radio-btns-holder d-flex">
							<div className="radio-holder me-3">
								<label className="custom-radio">Weekly
									<input type="radio" name="radio" />
									<span className="checkmark"></span>
								</label>
							</div>
							<div className="radio-holder me-3">
								<label className="custom-radio">Monthly
									<input type="radio" name="radio" />
									<span className="checkmark"></span>
								</label>
							</div>
							<div className="radio-holder me-3">
								<label className="custom-radio">Yearly
									<input type="radio" name="radio" />
									<span className="checkmark"></span>
								</label>
							</div>
						</div> */}
					</Form.Group>
					<div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
						<Link to="/my-mailing-list" className="btn btn-primary ms-3 mb-3"><span>Next</span></Link>
						<Link to="/sms-campaigns" className="btn btn-secondary ms-3 mb-3"><span>Back</span></Link>
					</div>
				</Form>
			</Container>
		</React.Fragment>
	);
}

export default CreateSmsCampaign;