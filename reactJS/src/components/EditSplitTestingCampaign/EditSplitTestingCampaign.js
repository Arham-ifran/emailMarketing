import React, { useState } from 'react';
import { Container, Row, Col, Form } from 'react-bootstrap';
import Select from 'react-select';
import campaignPreviewImg from '../../assets/images/img-01.jpg';
import CampaignPreview from '../CampaignPreview/CampaignPreview';
import './EditSplitTestingCampaign.css';
import DateTimePicker from 'react-datetime-picker';

function EditSplitTestingCampaign(props) {
	const options = [
		{ value: 'subscriberone', label: 'Subscriber One' },
		{ value: 'subscribertwo', label: 'Subscriber Two' },
		{ value: 'subscriberthree', label: 'Subscriber Three' },
	];
	const [selectedOption, setSelectedOption] = useState('')
	const [selectedDate, setSelectedDate] = useState(new Date())

	const handleChange = (selectedOption) => {
		setSelectedOption( selectedOption.value )
	}

	const handleCalenderChange = (date) => {
		console.log(`date ======= ${JSON.stringify(date)}`)
		setSelectedDate(date)
	}

	return (
		<React.Fragment>
			<Container fluid>
				<Row>
					<Col lg="8" md="6" xs="12">
						<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
							<div className="page-title">
								<h1>Edit Split Testing</h1>
							</div>
						</div>
						<Form className="edit-form-holder">
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
							<Form.Group className="mb-3 mb-md-4 d-flex">
								<Form.Label for="subject-line">Subject Line</Form.Label>
								<div className="flex-fill input-holder">
									<input id="subject-line" className="form-control" type="text" />
								</div>
							</Form.Group>
							<Form.Group className="mb-3 mb-md-4 d-flex">
								<Form.Label for="reply-to-address">Reply to Address</Form.Label>
								<div className="flex-fill input-holder">
									<input id="reply-to-address" className="form-control" type="text" />
								</div>
							</Form.Group>
							<Form.Group className="mb-3 mb-md-4 d-flex w-auto">
								<Form.Label for="reply-to-address">Subject Test Parameters</Form.Label>
								<div className="flex-fill input-holder radio-btns-holder d-flex">
									<div className="checkbox-holder me-3 ms-4">
										<label class="custom-checkbox mb-0">Subject Line
											<input type="checkbox" id="styled-checkbox-1" type="checkbox" value="value1" />
											<span class="checkmark"></span>
										</label>
									</div>
									<div className="checkbox-holder me-3 ms-4">
										<label class="custom-checkbox mb-0">Email Content
											<input type="checkbox" id="styled-checkbox-2" type="checkbox" value="value1" />
											<span class="checkmark"></span>
										</label>
									</div>
								</div>
							</Form.Group>
							<Form.Group className="mb-3 mb-md-4 d-flex">
								<Form.Label for="reply-to-address">Set the Recursion Cycle</Form.Label>
								<div className="flex-fill input-holder radio-btns-holder d-flex">
									<div className="radio-holder me-3">
										<label className="custom-radio mb-0">Weekly
											<input type="radio" name="radio" />
											<span className="checkmark"></span>
										</label>
									</div>
									<div className="radio-holder me-3">
										<label className="custom-radio mb-0">Monthly
											<input type="radio" name="radio" />
											<span className="checkmark"></span>
										</label>
									</div>
									<div className="radio-holder me-3">
										<label className="custom-radio mb-0">Yearly
											<input type="radio" name="radio" />
											<span className="checkmark"></span>
										</label>
									</div>
								</div>
							</Form.Group>
							<Form.Group className="mb-3 mb-md-4 d-flex">
								<Form.Label>Contact Details</Form.Label>
								<div className="flex-fill input-holder">
									<div className="subscriber-select">
										<Select
											onChange={(e) => handleChange(e)}
											options={options}
											classNamePrefix="react-select"
										/>
									</div>
								</div>
							</Form.Group>
							<Form.Group className="mb-3 mb-md-4 d-flex">
								<Form.Label for="reply-to-address">Send Campaign</Form.Label>
								<div className="flex-fill input-holder radio-btns-holder d-flex pt-0">
									<div className="radio-holder me-3">
										<label className="custom-radio mb-0">Immediately
											<input type="radio" name="radio" />
											<span className="checkmark"></span>
										</label>
									</div>
									<div className="radio-holder me-3">
										<label className="custom-radio mb-0">Send at:
											<input type="radio" name="radio" />
											<span className="checkmark"></span>
										</label>
									</div>
									<div className="calendar-holder me-3">
										<DateTimePicker
											className="em-calendar"
											onChange={(e) => handleCalenderChange(e) } value={selectedDate}
										/>
									</div>
								</div>
							</Form.Group>
						</Form>
					</Col>
					<Col xxl="4" xs="12">
						<CampaignPreview />
					</Col>
				</Row>
				<Row>
					<Col xs="12">
						<div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
							{/* Save button will be shonw in Edit Mode
							<button className="btn btn-primary ms-3 mb-3"><span>Save</span></button> */}
							<button className="btn btn-secondary ms-3 mb-3"><span>Back</span></button>
						</div>
					</Col>
				</Row>
			</Container>
		</React.Fragment>
	);
}

export default EditSplitTestingCampaign;