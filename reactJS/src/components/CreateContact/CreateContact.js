import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import Select from 'react-select';
import { Container, Form} from 'react-bootstrap';
import './CreateContact.css';
function CreateContact(props) {
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
						<h1>Add Contact</h1>
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
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label for="mobile-no">Add Contact to Mailing List</Form.Label>
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
					<div className="btns-holder right-btns d-flex flex-row-reverse pt-5">
						<Link to="/my-mailing-list" className="btn btn-primary ms-3 mb-3"><span>Next</span></Link>
						<Link to="/split-testing" className="btn btn-secondary ms-3 mb-3"><span>Back</span></Link>
					</div>
				</Form>
			</Container>
		</React.Fragment>
	);
}

export default CreateContact;