import React from 'react';
import { Link } from 'react-router-dom';
import { Container, Row, Col, Form } from 'react-bootstrap';
import Select from 'react-select';
import CampaignPreview from '../CampaignPreview/CampaignPreview';
import './EditContact.css';
import DateTimePicker from 'react-datetime-picker';

function EditContact(props) {
	return (
		<React.Fragment>
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>View / Edit Contact</h1>
					</div>
				</div>
				<Form className="create-form-holder rounded-box-shadow bg-white">
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="title">Title</Form.Label>
						<div className="flex-fill input-holder">
							<input id="title" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="contact-name">Cotnact Name</Form.Label>
						<div className="flex-fill input-holder">
							<input id="contact-name" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="email">Email</Form.Label>
						<div className="flex-fill input-holder">
							<input id="email" className="form-control" type="email" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="country">Country</Form.Label>
						<div className="flex-fill input-holder">
							<input id="country" className="form-control" type="text" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="mobile-no">Mobile No.</Form.Label>
						<div className="flex-fill input-holder">
							<input id="mobile-no" className="form-control" type="tel" />
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label for="asso-lists">Associated Lists</Form.Label>
						<div className="flex-fill input-holder">
							<input id="asso-lists" className="form-control" type="text" />
						</div>
					</Form.Group>
					<div className="btns-holder right-btns d-flex flex-row-reverse pt-5">
						{/* Save button will be shonw in Edit Mode
						<Link to="/my-mailing-list" className="btn btn-primary ms-3 mb-3"><span>Save</span></Link> */}
						<Link to="/split-testing" className="btn btn-secondary ms-3 mb-3"><span>Back</span></Link>
					</div>
				</Form>
			</Container>
		</React.Fragment>
	);
}

export default EditContact;