import React, { useState } from 'react';
import { Container, Row, Col, Form } from 'react-bootstrap';
import Select from 'react-select';
import CampaignPreview from '../CampaignPreview/CampaignPreview';
import './EditEmailCampaign.css';
import DateTimePicker from 'react-datetime-picker';

function EditEmailCampaign(props) {
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
					<Col xxl="8" xs="12">
						<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
							<div className="page-title">
								<h1>Edit Email campaigns</h1>
							</div>
						</div>
						<div className="bg-white rounded-box-shadow mb-4 mb-xxl-0">
							<Form className="edit-form-holder">
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
									<Form.Label className="mb-2 mb-md-0" for="sender-email">Sender Email</Form.Label>
									<div className="flex-fill input-holder">
										<input id="sender-email" className="form-control" type="email" />
									</div>
								</Form.Group>
								<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
									<Form.Label className="mb-2 mb-md-0" for="subject-line">Subject Line</Form.Label>
									<div className="flex-fill input-holder">
										<input id="subject-line" className="form-control" type="text" />
									</div>
								</Form.Group>
								<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
									<Form.Label className="mb-2 mb-md-0" for="reply-to-address">Reply to Address</Form.Label>
									<div className="flex-fill input-holder">
										<input id="reply-to-address" className="form-control" type="text" />
									</div>
								</Form.Group>
								<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
									<Form.Label className="mb-2 mb-md-0">Contact Details</Form.Label>
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
								<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
									<Form.Label className="mb-2 mb-md-0" for="reply-to-address">Set the Recursion Cycle</Form.Label>
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
								<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
									<Form.Label className="mb-2 mb-md-0" for="reply-to-address">Send Campaign</Form.Label>
									<div className="flex-fill input-holder radio-btns-holder d-flex flex-column pt-0">
										<div className="d-flex align-items-center">
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
										</div>
										<div className="calendar-holder pt-3 me-3">
											<DateTimePicker
												className="em-calendar"
												onChange={(e) => handleCalenderChange(e) } value={selectedDate}
											/>
										</div>
									</div>
								</Form.Group>
							</Form>
						</div>
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

export default EditEmailCampaign;