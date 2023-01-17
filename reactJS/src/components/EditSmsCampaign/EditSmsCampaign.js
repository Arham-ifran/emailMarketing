import React, { useState } from 'react';
import { Container, Row, Form} from 'react-bootstrap';
import Select from 'react-select';
import './EditSmsCampaign.css';
import DateTimePicker from 'react-datetime-picker';


function EditSmsCampaign(props) {
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
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>Edit SMS Campaign</h1>
					</div>
				</div>
				<Form className="create-form-holder">
					<div className="bg-white rounded-box-shadow">
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
							<Form.Label className="mb-2 mb-md-0" for="reply-to-address">Set the Recursion Cycle</Form.Label>
							<div className="flex-fill input-holder radio-btns-holder d-flex">
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
					</div>
					<div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
						<button className="btn btn-primary ms-3 mb-3"><span>Next</span></button>
						<button className="btn btn-secondary ms-3 mb-3"><span>Back</span></button>
					</div>
				</Form>
			</Container>
		</React.Fragment>
	);
}

export default EditSmsCampaign;