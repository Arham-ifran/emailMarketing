import React from 'react';
import { Link } from 'react-router-dom';
import { Container, Form} from 'react-bootstrap';
import './CreateSplitTesting.css';
function CreateSplitTesting(props) {
	return (
		<React.Fragment>
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>Create Split Testing</h1>
					</div>
				</div>
				<Form className="create-form-holder rounded-box-shadow bg-white">
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
						<Form.Label for="sender-email">Sender Email</Form.Label>
						<div className="flex-fill input-holder">
							<input id="sender-email" className="form-control" type="email" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex w-auto">
						<Form.Label for="reply-to-address">Subject Test Parameters</Form.Label>
						<div className="flex-fill input-holder radio-btns-holder d-flex">
							<div className="checkbox-holder me-3 ms-4">
								<span className="custom-checkbox">
									<input type="checkbox" id="styled-checkbox-1" type="checkbox" value="value1" className="styled-checkbox" />
									<label class="custom-control-label" for="styled-checkbox-1">Subject Line</label>
								</span>
							</div>
							<div className="checkbox-holder me-3 ms-4">
								<span className="custom-checkbox">
									<input type="checkbox"  id="styled-checkbox-2" type="checkbox" value="value2" className="styled-checkbox" />
									<label class="custom-control-label" for="styled-checkbox-2">Email Content</label>
								</span>
							</div>
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="reply-to-address">Select Sizes of Test Group</Form.Label>
						<div className="flex-fill input-holder">
							<input id="reply-to-address" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="reply-to-address">Reply to Address</Form.Label>
						<div className="flex-fill input-holder">
							<input id="reply-to-address" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex w-auto">
						<Form.Label for="reply-to-address">Set up the Recursive Cycle</Form.Label>
						<div className="flex-fill input-holder radio-btns-holder d-flex">
							<div className="radio-holder me-3 ms-4">
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
					</Form.Group>
					<div className="btns-holder right-btns d-flex flex-row-reverse pt-5">
						<Link to="/my-mailing-list" className="btn btn-primary ms-3 mb-3"><span>Next</span></Link>
						<Link to="/split-testing" className="btn btn-secondary ms-3 mb-3"><span>Back</span></Link>
					</div>
				</Form>
			</Container>
		</React.Fragment>
	);
}

export default CreateSplitTesting;