import React, { useState } from 'react';
import { Container, Row, Col, Form } from 'react-bootstrap';
import Select from 'react-select';
import './AddMultipleContact.css';
function AddMultipleContact(props) {
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
			<section className="right-canvas email-campaign">
				<Container fluid>
					<Row>
						<Col xs="12">
							<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
								<div className="page-title">
									<h1>Add Multiple Contacts</h1>
								</div>
							</div>
							<div className="bg-white rounded-box-shadow mb-3 mb-md-4">
								<div className="multiple-contact">
									<Row className="multiple-contact-row">
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="title" className="form-control" placeholder="Title" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="name" className="form-control" placeholder="Name" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="email" className="form-control" placeholder="Email" type="email" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="country" className="form-control" placeholder="County" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="phone" className="form-control" placeholder="Phone" type="tel" />
												</div>
											</Form.Group>
										</Col>
										<Col sm="12">
											<hr />
										</Col>
									</Row>
									<Row className="multiple-contact-row">
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="title" className="form-control" placeholder="Title" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="name" className="form-control" placeholder="Name" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="email" className="form-control" placeholder="Email" type="email" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="country" className="form-control" placeholder="County" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="phone" className="form-control" placeholder="Phone" type="tel" />
												</div>
											</Form.Group>
										</Col>
										<Col sm="12">
											<hr />
										</Col>
									</Row>
									<Row className="multiple-contact-row">
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="title" className="form-control" placeholder="Title" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="name" className="form-control" placeholder="Name" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="email" className="form-control" placeholder="Email" type="email" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="country" className="form-control" placeholder="County" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="phone" className="form-control" placeholder="Phone" type="tel" />
												</div>
											</Form.Group>
										</Col>
										<Col sm="12">
											<hr />
										</Col>
									</Row>
									<Row className="multiple-contact-row">
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="title" className="form-control" placeholder="Title" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="name" className="form-control" placeholder="Name" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="email" className="form-control" placeholder="Email" type="email" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="country" className="form-control" placeholder="County" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="phone" className="form-control" placeholder="Phone" type="tel" />
												</div>
											</Form.Group>
										</Col>
										<Col sm="12">
											<hr />
										</Col>
									</Row>
									<Row className="multiple-contact-row">
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="title" className="form-control" placeholder="Title" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="name" className="form-control" placeholder="Name" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="email" className="form-control" placeholder="Email" type="email" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="country" className="form-control" placeholder="County" type="text" />
												</div>
											</Form.Group>
										</Col>
										<Col xxl="2" xl="3" md="4" sm="6">
											<Form.Group className="mb-3 mb-md-4 d-flex">
												<div className="input-holder w-100">
													<input id="phone" className="form-control" placeholder="Phone" type="tel" />
												</div>
											</Form.Group>
										</Col>
										<Col sm="12">
											<hr />
										</Col>
									</Row>
									<Row>
										<Col xs="12">
											<span className="btn btn-secondary"><span>Add Another Contact</span></span>
										</Col>
									</Row>
								</div>
							</div>
							<div className="mutli-contct-list-holder">
								<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
								<Form.Label className="mb-2 mb-md-0" for="title">Add Conctact to Mailing List</Form.Label>
									<div className="flex-fill input-holder">
										<div className="mutli-contct-list-select">
											<Select
												onChange={(e) => handleChange(e)}
												options={options}
												classNamePrefix="react-select"
											/>
										</div>
									</div>
								</Form.Group>
							</div>
							<div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
								<a className="btn btn-primary ms-3 mb-3" href="/my-mailing-list"><span>Next</span></a>
								<a className="btn btn-secondary ms-3 mb-3" href="/email-campaigns"><span>Back</span></a>
							</div>
						</Col>
					</Row>
				</Container>
			</section>
		</React.Fragment>
	);
}

export default AddMultipleContact;