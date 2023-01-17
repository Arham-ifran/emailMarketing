import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Container, Row, Col, Modal, Button, Dropdown } from 'react-bootstrap';
// import templatePreview from '../../assets/images/template-preview.jpg';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import { faFileImport } from '@fortawesome/free-solid-svg-icons'
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons'
import { faTrashAlt } from '@fortawesome/free-regular-svg-icons'
import { faPlus } from '@fortawesome/free-solid-svg-icons'
import Pagination from "react-js-pagination";
import Spinner from '../includes/spinner/Spinner';
import GetUserPackage from '../Auth/GetUserPackage';
import { withTranslation } from 'react-i18next';
import Swal from 'sweetalert2';
import './MyTemplates.css';

var loading_count = 0;
function MyTemplates(props) {
	const { t } = props;
	const [loading, setLoading] = useState('');
	const [pageNumber, setPageNumber] = useState(new URLSearchParams(location.search).get('page1') ? new URLSearchParams(location.search).get('page1') : 1);
	const [perPage, setperPage] = useState(10);
	const [totalItems, setTotalItems] = useState(0);
	const [pageNumber2, setPageNumber2] = useState(1);
	const [perPage2, setperPage2] = useState(10);
	const [totalItems2, setTotalItems2] = useState(0);
	const [pageNumber3, setPageNumber3] = useState(new URLSearchParams(location.search).get('page2') ? new URLSearchParams(location.search).get('page2') : 1);
	const [perPage3, setperPage3] = useState(10);
	const [totalItems3, setTotalItems3] = useState(0);
	const [pageRange, setPageRange] = useState(5);
	const [report, setReport] = useState([]);
	const [templates, setTemplates] = useState([]);
	const [smsTemplates, setSmsTemplates] = useState([]);
	const [deleting, setDeleting] = useState([]);
	const [templatePreview, setTemplatePreview] = useState(false);
	const [templatePreviewText, setTemplatePreviewText] = useState(false);
	const [previewImage, setPreviewImage] = useState('');
	const [previewText, setPreviewText] = useState('');
	const [previewName, setPreviewName] = useState('');

	const [show, setShow] = useState(false);
	const [showTemplates, setShowTemplates] = useState(false);
	const [showSms, setShowSms] = useState(false);
	const handleClose = () => setShow(false);
	const handleShow = (id) => { setShow(true); setDeleting(id) }

	const templateListing = () => {
		setLoading(true);
		axios.get('/api/campaign-template/index/?page=' + pageNumber + '&lang=' + localStorage.lang)
			.then(response => {
				if (response.data.status) {

					loading_count++;
					if (loading_count >= 3)
						setLoading(false);
					setReport(response.data.data);
					setperPage(response.data.meta.per_page);
					setTotalItems(response.data.meta.total);
				}
			})
			.catch(error => {
				loading_count++;
				if (loading_count >= 3)
					setLoading(false);
			})
	}

	const PublicTemplateListing = () => {
		setLoading(true);
		axios.get('/api/public-campaign-template/index/?page=' + pageNumber2 + '&lang=' + localStorage.lang)
			.then(response => {
				if (response.data.status) {

					loading_count++;
					if (loading_count >= 3)
						setLoading(false);
					setTemplates(response.data.data);
					setperPage2(response.data.meta.per_page);
					setTotalItems2(response.data.meta.total);
				}
			})
			.catch(error => {
				loading_count++;
				if (loading_count >= 3)
					setLoading(false);
			})
	}

	const SmsTemplateListing = () => {
		setLoading(true);
		axios.get('/api/get-sms-templates?page=' + pageNumber3 + '&lang=' + localStorage.lang)
			.then(response => {
				if (response.data.status) {
					loading_count++;
					if (loading_count >= 3)
						setLoading(false);
					var received_data = response.data.data;
					console.log(received_data);
					setSmsTemplates(received_data.data);
					setperPage3(received_data.per_page);
					setTotalItems3(received_data.total);
				}
			})
			.catch(error => {
				loading_count++;
				if (loading_count >= 3)
					setLoading(false);
			})
	}

	useEffect(() => {
		templateListing();
	}, [pageNumber]);

	useEffect(() => {
		PublicTemplateListing();
	}, [pageNumber2]);

	useEffect(() => {
		SmsTemplateListing();
	}, [pageNumber3]);

	const SmsTemplateDelete = () => {
		setLoading(true);
		const id = deleting;
		setLoading(true);
		axios.post('/api/delete-sms-template/' + id + '?lang=' + localStorage.lang)
			.then(response => {
				loading_count++;
				if (loading_count >= 3)
					setLoading(false);
				if (response.data.status) {
					SmsTemplateListing();
					handleCloseSms();
				}
			})
			.catch(error => {
				loading_count++;
				if (loading_count >= 3)
					setLoading(false);
			})
	}

	const templateDelete = () => {
		setLoading(true);
		const id = deleting;
		axios.delete('/api/campaign-template/' + id + '?lang=' + localStorage.lang)
			.then(response => {
				loading_count++;
				if (loading_count >= 3)
					setLoading(false);
				if (response.data.status) {
					templateListing();
					handleClose();
				}
				else {
					handleClose();
					Swal.fire({
						title: t("oops!"),
						text: t("template_will_not_be_deleted_if_it_is_included_in_an_active_campaign"),
						icon: "warning",
						showCancelButton: false,
						confirmButtonText: t('OK')
					});
				}
			})
			.catch(error => {
				loading_count++;
				if (loading_count >= 3)
					setLoading(false);
			})
	}

	const handleCloseSms = () => {
		setShowSms(false);
	};
	const handleShowSms = (id) => {
		setShowSms(true);
		setDeleting(id)
	};

	const handleCloseTemplates = () => {
		setShowTemplates(false);
	};
	const handleShowTemplates = () => {
		setShowTemplates(true);
	};

	const HandleTemplateImport = (id) => {
		setLoading(true);
		axios.get('/api/public-campaign-template/import/' + id + '?lang=' + localStorage.lang)
			.then(response => {
				loading_count++;
				if (loading_count >= 3)
					setLoading(false);
				if (response.data.status) {
					templateListing();
					handleCloseTemplates();
				}
			})
			.catch(error => {
				loading_count++;
				if (loading_count >= 3)
					setLoading(false);
			})
	}

	const [userPackage, setUserPackage] = useState({});
	const [canDesign, setCanDesign] = useState(0);
	const [canImportBasic, setCanImportBasic] = useState(0);
	const [canImportHTML, setCanImportHTML] = useState(0);
	const [canAddSMS, setCanAddSMS] = useState(0);

	useEffect(() => {
		const load = () => {
			if (userPackage != {}) {
				if (userPackage.features) {
					if (Object.keys(userPackage.features).findIndex(val => val === "5") >= 0) { // import basic
						setCanImportBasic(true)
					} else {
						setCanImportBasic(false)
					}
					if (Object.keys(userPackage.features).findIndex(val => val === "6") >= 0) { // design
						setCanDesign(true)
					} else {
						setCanDesign(false)
					}
					if (Object.keys(userPackage.features).findIndex(val => val === "7") >= 0) { // import html
						setCanImportHTML(true)
					} else {
						setCanImportHTML(false)
					}
					if (Object.keys(userPackage.features).findIndex(val => val === "8") >= 0) { // add sms
						setCanAddSMS(true)
					} else {
						setCanAddSMS(false)
					}
				}
			}
		}
		load();
	}, [userPackage])

	const reportCampaignList = report.map((template, index) => {

		return (
			<Col xl="4" sm="6" key={index}>
				<div className="template-holder">
					<div className="image-holder img-holder-height">
						<img className="img-fluid" src={template.image} alt="template Image" />
						<div className="overlay">&nbsp;</div>
						<ul className="action-list list-unstyled d-flex">
							<li><button className="view-icon" title={t("View")} onClick={() => { setTemplatePreview(true); setPreviewImage(template.image); setPreviewName(template.name) }}><FontAwesomeIcon icon={faEye} /></button></li>
							{canDesign || template.type != 1 ? <li>
								<a href={`/email-template/` + template.hash_id + (template.type == 1 ? '/edit' : '/edithtml')} className="edit-icon" title={t("Edit")}>
									<FontAwesomeIcon icon={faPencilAlt} />
								</a>
							</li> : ""}
							<li><button className="dlt-icon" title={t("Delete")} ariant="primary" onClick={() => handleShow(template.hash_id)}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
						</ul>
					</div>
					<strong className="template-name d-block text-center">{template.name}</strong>
				</div>
			</Col>
		);
	});

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
			<section className="templates-holder">
				<Container fluid>
					<div className="page-title">
						<h1>{t('My Templates')}</h1>
					</div>
					{canDesign || canImportBasic || canImportHTML ?
						<>
							<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
								<h4>{t('Email Templates')}</h4>
								<div className="add-contact-dropdown">
									<Dropdown>
										<Dropdown.Toggle variant="success" id="dropdown-basic">
											<span className="btn btn-secondary">
												<span>{t('Add New Template')}{" "}<FontAwesomeIcon icon={faPlus} className="ml-1" /></span>
											</span>
										</Dropdown.Toggle>
										<Dropdown.Menu>
											<ul className="list-unstyled sub-menu">
												{canDesign ? <li><Link to={"/email-template/create" + "?page=" + pageNumber}>{t('Design Template')}</Link></li> : ""}
												{canImportHTML ? <li><Link to={"/email-template/import" + "?page=" + pageNumber}>{t('Import HTML Template')}</Link></li> : ""}
												{canImportBasic ? <li><Link onClick={handleShowTemplates}>{t('Import Existing Template')}</Link></li> : ""}
											</ul>
										</Dropdown.Menu>
									</Dropdown>
								</div>
							</div>
							<Row className="row d-flex align-items-center _height">

								{
									report.length ?
										<>
											{reportCampaignList}
											{/* pagination starts here */}
											<div className="">
												<Pagination
													activePage={pageNumber}
													itemsCountPerPage={perPage}
													totalItemsCount={totalItems}
													pageRangeDisplayed={pageRange}
													onChange={(e) => setPageNumber(e)}
												/>
											</div>
											{/* pagination ends here */}
										</>
										:
										<div className="d-flex justify-content-center ">
											<h5>{t('You have No templates Yet')}</h5>
										</div>
								}
							</Row>
						</>
						: ""
					}

					{canAddSMS ?
						<>
							<div className="mt-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
								<div className="page-title">
									<h4>{t('SMS Templates')}</h4>
								</div>
								<div className="add-contact">
									<Link to="/sms-template/create" className="btn btn-secondary">
										<span>
											{t('Add New Template')}{" "} <FontAwesomeIcon icon={faPlus} className="ms-2" />
										</span>
									</Link>
								</div>
							</div>
							<Row className="row d-flex align-items-center _height">

								{
									smsTemplates.length ?
										<>
											{smsTemplates.map((template, index) =>
												<Col xl="4" sm="6" key={index}>
													<div className="template-holder  sms-template-holder">
														<div className="overlay">&nbsp;</div>
														<ul className="action-list list-unstyled d-flex">
															<li><button className="view-icon" title={t("View")} onClick={() => { setTemplatePreviewText(true); setPreviewText(template.message); setPreviewName(template.name) }}><FontAwesomeIcon icon={faEye} /></button></li>
															<li>
																<Link to={`/sms-template/` + template.hash_id + '/edit' + "?page=" + pageNumber} className="edit-icon" title={t("Edit")}>
																	<FontAwesomeIcon icon={faPencilAlt} />
																</Link>
															</li>
															<li><button className="dlt-icon" title={t("Delete")} ariant="primary" onClick={() => handleShowSms(template.hash_id)}><FontAwesomeIcon icon={faTrashAlt} /></button></li>
														</ul>

														<strong className="template-name d-block text-center">{template.name}</strong>
													</div>
												</Col>
											)}

											{/* pagination starts here */}
											<div className="mt-2">
												<Pagination
													activePage={pageNumber3}
													itemsCountPerPage={perPage3}
													totalItemsCount={totalItems3}
													pageRangeDisplayed={pageRange}
													onChange={(e) => setPageNumber3(e)}
												/>
											</div>
											{/* pagination ends here */}
										</>
										:
										<div className="d-flex justify-content-center ">
											<h5>{t('You have No templates Yet')}</h5>
										</div>
								}
							</Row>
						</>
						: ""
					}
				</Container>
			</section>
			{/* Delete Email Modal */}
			<Modal show={show} onHide={handleClose} className="em-modal dlt-modal" centered>
				<Modal.Body className="d-flex align-items-center justify-content-center flex-column">
					<span className="dlt-icon">
						<FontAwesomeIcon icon={faTrashAlt} />
					</span>
					<p>{t('Are you sure you want to delete Email Template?')}</p>
				</Modal.Body>
				<Modal.Footer className="justify-content-center">
					<Button variant="primary" onClick={templateDelete}>
						<span>{t('Yes')}</span>
					</Button>
					<Button variant="secondary" onClick={handleClose}>
						<span>{t('Cancel')}</span>
					</Button>
				</Modal.Footer>
			</Modal>

			{/* Delete SMS Modal */}
			<Modal show={showSms} onHide={handleCloseSms} className="em-modal dlt-modal" centered>
				<Modal.Body className="d-flex align-items-center justify-content-center flex-column">
					<span className="dlt-icon">
						<FontAwesomeIcon icon={faTrashAlt} />
					</span>
					<p>{t('Are you sure you want to delete SMS Template?')}</p>
				</Modal.Body>
				<Modal.Footer className="justify-content-center">
					<Button variant="primary" onClick={SmsTemplateDelete}>
						<span>{t('Yes')}</span>
					</Button>
					<Button variant="secondary" onClick={handleCloseSms}>
						<span>{t("Cancel")}</span>
					</Button>
				</Modal.Footer>
			</Modal>

			{/* show public templates Modal */}
			<Modal show={showTemplates} onHide={handleCloseTemplates} size="xl" className="existing-temp em-modal dlt-modal" centered>
				<Modal.Body className="d-flex align-items-center justify-content-center flex-column">
					<h3>{t('Import Existing Template')} </h3>
					<p>{t('choose_the_template_that_you_would_like_to_import_and_edit')}</p>
					<Row className="row d-flex align-items-center _height">
						{
							templates.length ?
								<>
									{templates.map((template, index) =>
										<Col xl="4" sm="6" key={index}>
											<div className="template-holder mb-xl-0 mb-2">
												<div className="model-image image-holder img-holder-height">
													<img className=" img-fluid" src={template.image} alt="template Image" />
													<ul className="action-list list-unstyled d-flex">
														<li><a className="view-icon" title={t("View")} onClick={() => { setTemplatePreview(true); setPreviewImage(template.image); setPreviewName(template.name) }}><FontAwesomeIcon icon={faEye} /></a></li>
														<li><a onClick={() => HandleTemplateImport(template.hash_id)} className="view-icon" title={t("import")}><FontAwesomeIcon icon={faFileImport} /></a></li>
														<li>
															<strong className="template-name d-block text-center">{template.name}</strong>
														</li>
													</ul>
												</div>
											</div>
										</Col>
									)}
									{/* pagination starts here */}
									<div className="mt-5">
										<Pagination
											activePage={pageNumber2}
											itemsCountPerPage={perPage2}
											totalItemsCount={totalItems2}
											pageRangeDisplayed={pageRange}
											onChange={(e) => setPageNumber2(e)}
										/>
									</div>
									{/* pagination ends here */}
								</>
								:
								<div className="d-flex justify-content-center ">
									<h5>{t('You have No templates Yet')}</h5>
								</div>
						}
					</Row>
				</Modal.Body>
				<Modal.Footer className="justify-content-center">
					<Button variant="secondary" onClick={handleCloseTemplates}>
						<span>{t('Cancel')}</span>
					</Button>
				</Modal.Footer>
			</Modal>

			{/* show template Image Modal */}
			<Modal show={templatePreview} onHide={() => setTemplatePreview(false)} size="xl" className="existing-temp em-modal dlt-modal" centered>
				<Modal.Header closeButton className="add-contact-modal em-table"> </Modal.Header>
				<Modal.Body className="d-flex align-items-center justify-content-center flex-column">
					{/* <h3>{t('view_email_template')} </h3> */}
					<h3> {previewName} </h3>
					<Row className="row d-flex align-items-center _height mt-3">
						<img className=" img-fluid" src={previewImage} alt="template Image" />
					</Row>
				</Modal.Body>
				<Modal.Footer className="justify-content-center">
					<Button variant="secondary" onClick={() => setTemplatePreview(false)}>
						<span>{t('Close')}</span>
					</Button>
				</Modal.Footer>
			</Modal>

			{/* show template Text Modal */}
			<Modal show={templatePreviewText} onHide={() => setTemplatePreviewText(false)} size="l" className="existing-temp em-modal dlt-modal" centered>
				<Modal.Header closeButton className="add-contact-modal em-table"> </Modal.Header>
				<Modal.Body className="d-flex align-items-center justify-content-center flex-column">
					{/* <h3>{t('view_email_template')} </h3> */}
					<h3> {previewName} </h3>
					<Row className="row d-flex align-items-center _height mt-3">
						<p>{previewText}</p>
					</Row>
				</Modal.Body>
				<Modal.Footer className="justify-content-center">
					<Button variant="secondary" onClick={() => setTemplatePreviewText(false)}>
						<span>{t('Close')}</span>
					</Button>
				</Modal.Footer>
			</Modal>
		</React.Fragment>
	);
}

export default withTranslation()(MyTemplates);