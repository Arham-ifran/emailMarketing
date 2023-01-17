import React from 'react';
import { Link } from 'react-router-dom';
import { Container, Form} from 'react-bootstrap';
import './CreateMailingList.css';
function CreateMailingList(props) {
	return (
		<React.Fragment>
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>Create Mailing List</h1>
					</div>
				</div>
				<Form className="create-form-holder">
					<div className="rounded-box-shadow bg-white">
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" for="campaign-name">Name</Form.Label>
							<div className="flex-fill input-holder">
								<input id="campaign-name" className="form-control" type="text" />
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" for="sender-name">Tell me how do you know about them?</Form.Label>
							<div className="flex-fill input-holder">
								<textarea rows="5" cols="5" className="form-control"></textarea>
							</div>
						</Form.Group>
					</div>
					<div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
						<Link to="/add-import-contacts" className="btn btn-primary ms-3 mb-3"><span>Next</span></Link>
						<Link to="/mailing-lists" className="btn btn-secondary ms-3 mb-3"><span>Back</span></Link>
					</div>
				</Form>
			</Container>
		</React.Fragment>
	);
}

export default CreateMailingList;