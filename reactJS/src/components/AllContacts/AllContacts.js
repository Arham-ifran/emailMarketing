import React, { Fragment, useState } from 'react';
import { Container, Row, Col, Form, Table } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import { Modal, Button, Dropdown } from 'react-bootstrap';
import Select from 'react-select';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons'
import { faTrashAlt } from '@fortawesome/free-regular-svg-icons'
import { faPlus } from '@fortawesome/free-solid-svg-icons'
import DateTimePicker from 'react-datetime-picker';

import './AllContacts.css';
import { useCallback } from 'react';
function AllContacts(props) {
	
	const options = [
		{ value: 'bulkactionone', label: 'View' },
		{ value: 'bulkactiontwo', label: 'Edit' },
		{ value: 'bulkactionthree', label: 'Delete' },
	];
	const options2 = [
		{ value: 'nameone', label: 'Contact Name 1' },
		{ value: 'nametwo', label: 'Contact Name 2' },
	];
	const [selectedOption, setSelectedOption] = useState('')
	const handleChange = (selectedOption) => {
		setSelectedOption( selectedOption.value )
	}
	const handleChange2 = (selectedOption) => {
		setSelectedOption( selectedOption.value )
	}

	// delete modal
	const [show, setShow] = useState(false);
	const handleClose = () => setShow(false);
	const handleShow = () => setShow(true);

	// date range picker
	const [selectedDate, setSelectedDate] = useState(new Date())		
	const handleCalenderChange = (date) => {
		console.log(`date ======= ${JSON.stringify(date)}`)
		setSelectedDate(date)
	}

	return (
		<Fragment>
			<section className="right-canvas email-campaign">
				<Container fluid>
					<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
						<div className="page-title">
							<h1>All Contacts</h1>
						</div>
						<div className="add-contact-dropdown">
							<Dropdown>
								<Dropdown.Toggle variant="success" id="dropdown-basic">
									<span className="btn btn-secondary">
										<span>Add New <FontAwesomeIcon icon={faPlus} className="ms-2"/></span>
									</span>
								</Dropdown.Toggle>
								<Dropdown.Menu>
									<ul className="list-unstyled sub-menu">
										<li><Link to="/contacts/create">Single Contact</Link></li>
										<li><Link to="/contacts/add-multiple">Multiple Contacts</Link></li>
									</ul>
								</Dropdown.Menu>
							</Dropdown>
						</div>
					</div>
					<Row>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">Total Contacts</span>
								<span class="value">900</span>
							</div>
						</Col>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">new contacts</span>
								<span class="value">700</span>
							</div>
						</Col>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">existing contacts</span>
								<span class="value">400</span>
							</div>
						</Col>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">drafts</span>
								<span class="value">50</span>
							</div>
						</Col>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">deleted</span>
								<span class="value">150</span>
							</div>
						</Col>
					</Row>
					<div className="form-table-wrapper rounded-box-shadow bg-white">
						<Form className="em-form" method="GET">
							<Row>
								<Col lg="3" md="3" xs="12">
									<Form.Group className="mb-3 mb-md-4 d-flex flex-column">
										<Form.Label for="contact-name">Contact name</Form.Label>
										<Select
											onChange={(e) => handleChange(e)}
											options={options2}
											classNamePrefix="react-select"
											placeholder="Select Name"
										/>
									</Form.Group>
								</Col>
								<Col lg="3" md="3" xs="12">
									<Form.Group className="mb-3 mb-md-4 d-flex flex-column">
										<Form.Label for="date-created">date created</Form.Label>
										<DateTimePicker
											className="em-calendar w-100"
											onChange={(e) => handleCalenderChange(e) } value={selectedDate}
										/>
									</Form.Group>
								</Col>
								<Col lg="3" md="3" xs="12">
									<Form.Group className="mb-3 mb-md-4 d-flex flex-column">
										<Form.Label for="email">Email</Form.Label>
										<div  className="input-holder">
											<input type="email" className="form-control" />
										</div>
									</Form.Group>
								</Col>
								<Col lg="3" md="3" xs="12">
									<Form.Group className="mb-3 mb-md-4 d-flex flex-column">
										<Form.Label for="maobile-no">mobile no.</Form.Label>
										<div  className="input-holder">
											<input type="text" className="form-control" />
										</div>
									</Form.Group>
								</Col>
								<Col lg="3" md="3" xs="12">
									<Form.Group className="btn-wrapper filter-btns mb-3 mb-md-4 d-flex flex-column">
										<div className="d-flex justify-content-between">
											<button type="submit" className="btn btn-primary">
												<span>Apply</span>
											</button>
											<button type="button" className="btn btn-secondary">
												<span>Reset</span>
											</button>
										</div>
									</Form.Group>
								</Col>
							</Row>
						</Form>
						<div className="status-table">
							<Row>
								<Col lg="2" md="6" xs="12">
									<Form.Group className="mb-3 mb-md-4">
										<Select
											onChange={(e) => handleChange2(e)}
											options={options}
											classNamePrefix="react-select"
											placeholder="Bulk Action"
										/>
									</Form.Group>
								</Col>
							</Row>
							<div className="table-responsive">
								<Table className="em-table align-middle">
									<thead>
										<tr>
											<th>
												<label class="custom-checkbox no-text me-0">
													<input type="checkbox" />
													<span class="checkmark"></span>
												</label>
											</th>
											<th>Contacts Name</th>
											<th>Email</th>
											<th>Mobile no.</th>
											<th>Date Created</th>
											<th>Last Modified</th>
											<th>Mailing List</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<label class="custom-checkbox no-text me-0">
													<input type="checkbox" />
													<span class="checkmark"></span>
												</label>
											</td>
											<td className="text-capitalize">John Doe</td>
											<td>johndoe@gmail.com</td>
											<td>+92300-000-000</td>
											<td>Feb 25, 2021</td>
											<td>Feb 26, 2021</td>
											<td>Mailing List Name</td>
											<td>
												<ul className="action-icons list-unstyled">
													<li><Link to="/contacts/:contactId?" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
													<li><Link to="/contacts/:contactId?" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
													<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
												</ul>
											</td>
										</tr>
										<tr>
											<td>
												<label class="custom-checkbox no-text me-0">
													<input type="checkbox" />
													<span class="checkmark"></span>
												</label>
											</td>
											<td className="text-capitalize">John Doe</td>
											<td>johndoe@gmail.com</td>
											<td>+92300-000-000</td>
											<td>Feb 25, 2021</td>
											<td>Feb 26, 2021</td>
											<td>Mailing List Name</td>
											<td>
												<ul className="action-icons list-unstyled">
													<li><Link to="/contacts/:contactId?" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
													<li><Link to="/contacts/:contactId?" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
													<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
												</ul>
											</td>
										</tr>
									</tbody>
								</Table>
								</div>
						</div>
					</div>
				</Container>
			</section>
			{/* Delete Modal */}
			<Modal show={show} onHide={handleClose} className="em-modal dlt-modal" centered>
				<Modal.Body className="d-flex align-items-center justify-content-center flex-column">
					<span className="dlt-icon">
						<FontAwesomeIcon icon={faTrashAlt} />
					</span>
					<p>Are you sure you want to delete Campaign?</p>
				</Modal.Body>
				<Modal.Footer className="justify-content-center">
					<Button variant="primary" onClick={handleClose}>
						<span>Ok</span>
					</Button>
					<Button variant="secondary" onClick={handleClose}>
						<span>Cancel</span>
					</Button>
				</Modal.Footer>
			</Modal>
		</Fragment>
	);
}

export default AllContacts;