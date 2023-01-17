import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { Container, Row, Col, Modal, Button, Dropdown } from 'react-bootstrap';
import templatePreview from '../../assets/images/template-preview.jpg';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons'
import { faTrashAlt } from '@fortawesome/free-regular-svg-icons'
import { faPlus } from '@fortawesome/free-solid-svg-icons'

import './MyTemplates.css';
function MyTemplates(props) {
	// delete modal
	const [show, setShow] = useState(false);
	const handleClose = () => setShow(false);
	const handleShow = () => setShow(true);

	return (
		<React.Fragment>
			<section className="templates-holder">
				<Container fluid>
					<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
						<div className="page-title">
							<h1>My Templates</h1>
						</div>
						<div className="add-contact-dropdown">
							<Dropdown>
								<Dropdown.Toggle variant="success" id="dropdown-basic">
									<span className="btn btn-secondary">
										<span>Add New Template<FontAwesomeIcon icon={faPlus} className="ms-2"/></span>
									</span>
								</Dropdown.Toggle>
								<Dropdown.Menu>
									<ul className="list-unstyled sub-menu">
										<li><Link to="/create-template">HTML Code Editor</Link></li>
										<li><Link to="/import-template">import / Upload a File</Link></li>
									</ul>
								</Dropdown.Menu>
							</Dropdown>
						</div>
					</div>
					<Row>
						<Col xl="4" sm="6">
							<div className="template-holder">
								<div className="image-holder">
									<img className="img-fluid" src={templatePreview} alt="template Image" />
									<div className="overlay">&nbsp;</div>
									<ul className="action-list list-unstyled d-flex">
										<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
										<li><Link to="/templates/:templateId?" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
										<li><Link to="/templates/:templateId?" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
									</ul>
								</div>
								<strong className="template-name d-block text-center">E-Commerce Template</strong>
							</div>
						</Col>
						<Col xl="4" sm="6">
							<div className="template-holder">
								<div className="image-holder">
									<img className="img-fluid" src={templatePreview} alt="template Image" />
									<div className="overlay">&nbsp;</div>
									<ul className="action-list list-unstyled d-flex">
										<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
										<li><Link to="/templates/:templateId?" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
										<li><Link to="/templates/:templateId?" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
									</ul>
								</div>
								<strong className="template-name d-block text-center">E-Commerce Template</strong>
							</div>
						</Col>
						<Col xl="4" sm="6">
							<div className="template-holder">
								<div className="image-holder">
									<img className="img-fluid" src={templatePreview} alt="template Image" />
									<div className="overlay">&nbsp;</div>
									<ul className="action-list list-unstyled d-flex">
										<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
										<li><Link to="/templates/:templateId?" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
										<li><Link to="/templates/:templateId?" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
									</ul>
								</div>
								<strong className="template-name d-block text-center">E-Commerce Template</strong>
							</div>
						</Col>
						<Col xl="4" sm="6">
							<div className="template-holder">
								<div className="image-holder">
									<img className="img-fluid" src={templatePreview} alt="template Image" />
									<div className="overlay">&nbsp;</div>
									<ul className="action-list list-unstyled d-flex">
										<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
										<li><Link to="/templates/:templateId?" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
										<li><Link to="/templates/:templateId?" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
									</ul>
								</div>
								<strong className="template-name d-block text-center">E-Commerce Template</strong>
							</div>
						</Col>
						<Col xl="4" sm="6">
							<div className="template-holder">
								<div className="image-holder">
									<img className="img-fluid" src={templatePreview} alt="template Image" />
									<div className="overlay">&nbsp;</div>
									<ul className="action-list list-unstyled d-flex">
										<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
										<li><Link to="/templates/:templateId?" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
										<li><Link to="/templates/:templateId?" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
									</ul>
								</div>
								<strong className="template-name d-block text-center">E-Commerce Template</strong>
							</div>
						</Col>
						<Col xl="4" sm="6">
							<div className="template-holder">
								<div className="image-holder">
									<img className="img-fluid" src={templatePreview} alt="template Image" />
									<div className="overlay">&nbsp;</div>
									<ul className="action-list list-unstyled d-flex">
										<li><button className="dlt-icon" title="Delete" ariant="primary" onClick={handleShow}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
										<li><Link to="/templates/:templateId?" className="view-icon" title="View"><FontAwesomeIcon icon={faEye} /></Link></li>
										<li><Link to="/templates/:templateId?" className="edit-icon" title="Edit"><FontAwesomeIcon icon={faPencilAlt } /></Link></li>
									</ul>
								</div>
								<strong className="template-name d-block text-center">E-Commerce Template</strong>
							</div>
						</Col>
					</Row>
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
		</React.Fragment>
	);
}

export default MyTemplates;