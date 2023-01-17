import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { Container, Table, Modal } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons'
import { faTrashAlt } from '@fortawesome/free-regular-svg-icons'
import './EditMailList.css';
function EditMailList(props) {
	// delete modal
	const [show, setShow] = useState(false);
	const handleClose = () => setShow(false);
	const handleShow = () => setShow(true);
	return (
		<React.Fragment>
			<Container fluid>
				<div className="form-table-wrapper rounded-box-shadow bg-white">
					<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
						<div className="page-title">
							<h1>View Mailing Lists</h1>
						</div>
					</div>
					<div className="table-responsive">
						<Table className="table em-table align-middle">
							<thead>
								<tr>
									<th>Contact Name</th>
									<th>Email</th>
									<th>Mobile Number</th>
									<th>Date Created</th>
									<th>Last Modified</th>
									<th>actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Mr. John</td>
									<td>jhondoe@gmail.com</td>
									<td>+92300-000-000</td>
									<td>Feb 25, 2021</td>
									<td>Feb 26, 2021</td>
									<td>
										<ul className="action-icons list-unstyled">
											<li><Link to="/contacts/:contactId?" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
											<li><Link to="/contacts/:contactId?" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
											<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
										</ul>
									</td>
								</tr>
								<tr>
									<td>Mr. John</td>
									<td>jhondoe@gmail.com</td>
									<td>+92300-000-000</td>
									<td>Feb 25, 2021</td>
									<td>Feb 26, 2021</td>
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
			</Container>
		</React.Fragment>
	);
}

export default EditMailList;