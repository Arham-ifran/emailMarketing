import React, { Fragment, useState } from 'react';
import { Container, Row, Col, Form, Table } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import { Badge, Modal, Button } from 'react-bootstrap';
import { ModalHeader, ModalBody, ModalFooter } from 'react-bootstrap';
import Select from 'react-select';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons'
import { faTrashAlt } from '@fortawesome/free-regular-svg-icons'
import { faPlus } from '@fortawesome/free-solid-svg-icons'
import { faSignal } from '@fortawesome/free-solid-svg-icons'
import DateTimePicker from 'react-datetime-picker';

import './SplitTesting.css';
import { useCallback } from 'react';
function SplitTesting(props) {
	
	const options = [
		{ value: 'bulkactionone', label: 'View' },
		{ value: 'bulkactiontwo', label: 'Edit' },
		{ value: 'bulkactionthree', label: 'Delete' },
	];
	const options2 = [
		{ value: 'statusone', label: 'Pending' },
		{ value: 'statustwo', label: 'Completed' },
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
							<h1>Split Testing</h1>
						</div>
						<div className="create-campaign">
							<Link to="/create-split-testing" className="btn btn-secondary">
								<span>Create a Campaign<FontAwesomeIcon icon={faPlus } className="ms-2"/></span>
							</Link>
						</div>
					</div>
					<Row>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">Total</span>
								<span class="value">900</span>
							</div>
						</Col>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">sent</span>
								<span class="value">800</span>
							</div>
						</Col>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">scheduled</span>
								<span class="value">500</span>
							</div>
						</Col>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">recursive</span>
								<span class="value">600</span>
							</div>
						</Col>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">draft</span>
								<span class="value">50</span>
							</div>
						</Col>
						<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span class="title">deleted</span>
								<span class="value">50</span>
							</div>
						</Col>
					</Row>
					<div className="form-table-wrapper rounded-box-shadow bg-white">
						<Form className="em-form" method="GET">
							<Row>
								<Col lg="3" md="3" xs="12">
									<Form.Group className="mb-3 mb-md-4 d-flex flex-column">
										<Form.Label>Status</Form.Label>
										<Select
											onChange={(e) => handleChange(e)}
											options={options2}
											classNamePrefix="react-select"
											placeholder="Select Status"
										/>
									</Form.Group>
								</Col>
								<Col lg="3" md="3" xs="12">
									<Form.Group className="mb-3 mb-md-4 d-flex flex-column">
										<Form.Label>date created</Form.Label>
										<DateTimePicker
											className="em-calendar w-100"
											onChange={(e) => handleCalenderChange(e) } value={selectedDate}
										/>
									</Form.Group>
								</Col>
								<Col lg="3" md="3" xs="12">
									<Form.Group className="mb-3 mb-md-4 d-flex flex-column">
										<Form.Label>campaign name</Form.Label>
										<input type="text" className="form-control" />
									</Form.Group>
								</Col>
								<Col lg="3" md="3" xs="12">
									<Form.Group className="btn-wrapper filter-btns mb-3 mb-md-4 d-flex flex-column">
										<Form.Label>&nbsp;</Form.Label>
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
											<th>campaign name</th>
											<th>subject</th>
											<th>click rate</th>
											<th>open rate</th>
											<th>unsubscribers</th>
											<th>date created</th>
											<th>last modified</th>
											<th>status</th>
											<th>actions</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td className="text-capitalize">summer promo</td>
											<td>-</td>
											<td>70%</td>
											<td>50%</td>
											<td>500</td>
											<td>feb 25, 2021</td>
											<td>feb 26, 2021</td>
											<td>
												<Badge className="d-inline-block align-top" bg="success">Sent</Badge>
											</td>
											<td>
												<ul className="action-icons list-unstyled">
													<li><Link to="/split-testing/:campaignId" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
													<li><Link to="/split-testing/:campaignId" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
													<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
													<li><Link to="" className="graph-icon" title="Graph"><FontAwesomeIcon icon={faSignal} /></Link></li>
												</ul>
											</td>
										</tr>
										<tr>
											<td className="text-capitalize">summer promo</td>
											<td>-</td>
											<td>70%</td>
											<td>50%</td>
											<td>500</td>
											<td>feb 25, 2021</td>
											<td>feb 26, 2021</td>
											<td>
												<Badge className="d-inline-block align-top" bg="success">Sent</Badge>
											</td>
											<td>
												<ul className="action-icons list-unstyled">
													<li><Link to="/split-testing/:campaignId" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
													<li><Link to="/split-testing/:campaignId" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
													<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
													<li><Link to="" className="graph-icon" title="Graph"><FontAwesomeIcon icon={faSignal} /></Link></li>
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
				<Button variant="secondary" onClick={handleClose}>
					<span>Cancel</span>
				</Button>
				<Button variant="primary" onClick={handleClose}>
					<span>Ok</span>
				</Button>
				</Modal.Footer>
			</Modal>
		</Fragment>
	);
}

export default SplitTesting;