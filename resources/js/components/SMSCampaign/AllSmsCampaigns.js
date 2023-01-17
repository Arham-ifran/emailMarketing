import React, { Fragment, useState, useEffect } from 'react';
import { Container, Row, Col, Form, Table } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import { Badge, Modal, Button } from 'react-bootstrap';
import Select from 'react-select';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import { faListAlt } from '@fortawesome/free-solid-svg-icons';
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons'
import { faCircle } from '@fortawesome/free-solid-svg-icons'
import { faTrashAlt } from '@fortawesome/free-regular-svg-icons'
import { faPlus } from '@fortawesome/free-solid-svg-icons'
import DateTimePicker from 'react-datetime-picker';
import Moment from "react-moment";
import Spinner from "../includes/spinner/Spinner";
import Pagination from "react-js-pagination";
import Swal from 'sweetalert2';
import moment from 'moment-timezone';
import GetUserPackage from "../Auth/GetUserPackage.js";
import { withTranslation } from 'react-i18next';
import './css/AllSmsCampaigns.css';

function AllSmsCampaigns(props) {
	const { t } = props;
	const options2 = [
		{ value: '1', label: t('Draft') },
		{ value: '2', label: t('Sending') },
		{ value: '3', label: t('Sent') },
		{ value: '4', label: t('Disabled') },
		{ value: '5', label: t('Active') },
		{ value: '6', label: t('Stopped') },
	];
	const [selectedOption, setSelectedOption] = useState('')
	const [filterstatus, setfilterstatus] = useState('')
	const handleChange = (selectedOption) => {
		setSelectedOption(selectedOption);
		setfilterstatus(selectedOption.value);
	}

	// date range picker
	const [selectedDate, setSelectedDate] = useState()
	const handleCalenderChange = (date) => {
		if (!(moment(moment(date).format('YYYY-MM-DD'), 'YYYY-MM-DD', true).isValid())) {
			// setErrors({
			// 	invalid_format: [t("invalid_format")],
			// });
			setfilterCreated("");
			setSelectedDate(null);
		}
		else {
			setfilterCreated(moment(date).format('YYYY-MM-DD'));
			setSelectedDate(date);
		}
	}
	// const [selectedDate2, setSelectedDate2] = useState()
	// const handleCalenderChange2 = (date) => {
	// 	if (!(moment(moment(date).format('YYYY-MM-DD'), 'YYYY-MM-DD', true).isValid())) {
	// 		setErrors({
	// 			invalid_format_2: [t("invalid_format")],
	// 		});
	// 	}
	// 	setfilterUpdated(moment(date).format('YYYY-MM-DD'));
	// 	setSelectedDate2(date);
	// }

	// delete modal
	const [show, setShow] = useState(false);

	const [show2, setShow2] = useState(false);
	const [stopping, setStopping] = useState(0);

	const handleClose = () => {
		setShow(false);
		setDeleting("");
	};
	const handleShow = (e) => {
		setDeleting(e.target.closest("button").attributes.from.value);
		setShow(true);
	};

	const handleClose2 = () => {
		setShow2(false);
		setStopping(0);
	}

	const handleShow2 = (e, id) => {
		setShow2(true);
		setStopping(id);
	}

	// common
	const [loading, setLoading] = useState(false);
	const [pageNumber, setPageNumber] = useState(new URLSearchParams(location.search).get('page') ? parseInt(new URLSearchParams(location.search).get('page')) : 1);
	const [perPage, setperPage] = useState(0);
	const [totalItems, setTotalItems] = useState(0);
	const [pageRange, setPageRange] = useState(5);
	const [newCampaigns, setNewCampaigns] = useState(0);
	const [totalCampaigns, setTotalCampaigns] = useState(0);
	const [deletedCampaigns, setDeletedCampaigns] = useState(0);
	const [existingCampaigns, setExistingCampaigns] = useState(0);
	const [draftCampaigns, setDraftedCampaigns] = useState(0);
	const [scheduledCampaigns, setScheduledCampaigns] = useState(0);
	const [recursiveCampaigns, setRecursiveCampaigns] = useState(0);
	const [sentCampaigns, setSentCampaigns] = useState(0);
	// this page
	const [filterName, setfilterName] = useState("");
	const [filterCreated, setfilterCreated] = useState("");
	const [filterUpdated, setfilterUpdated] = useState("");
	const [filter, setFilter] = useState(0);
	const [campaigns, setCampaigns] = useState([]);
	const [deleting, setDeleting] = useState("");
	const [errors, setErrors] = useState([]);

	const [userPackage, setUserPackage] = useState({});
	const [canSchedule, setCanSchedule] = useState(0);
	const [canRecurr, setCanRecurr] = useState(0);
	const [canViewReport, setCanViewReport] = useState(0);

	// errors
	const hasErrorFor = (field) => {
		return !!errors[field]
	}
	const renderErrorFor = (field) => {
		if (hasErrorFor(field)) {
			return (
				<span className='invalid-feedback'>
					<strong>{errors[field][0]}</strong>
				</span>
			)
		}
	}

	useEffect(() => {
		const load = () => {
			if (userPackage != {}) {
				if (userPackage.features) {
					if (Object.keys(userPackage.features).findIndex(val => val === "10") >= 0) { // schedule allowed
						// console.log(Object.values(userPackage.features)[Object.keys(userPackage.features).findIndex(val => val === "12")])
						setCanSchedule(true)
					} else {
						setCanSchedule(false)
					}
					if (Object.keys(userPackage.features).findIndex(val => val === "11") >= 0) { // recursive allowed
						setCanRecurr(true)
					} else {
						setCanRecurr(false)
					}
					if (Object.keys(userPackage.features).findIndex(val => val === "9") >= 0) { // view report
						setCanViewReport(true)
					} else {
						setCanViewReport(false)
					}
				}
			}
		}
		load();
	}, [userPackage])

	const getCampaigns = () => {
		setErrors([])
		setLoading(true);
		const created = (filterCreated != '' ? moment.tz(filterCreated + " 12:00", localStorage.timezone).utc().format('YYYY-MM-DD') : '')
		const updated = (filterUpdated != '' ? moment.tz(filterUpdated + " 12:00", localStorage.timezone).utc().format('YYYY-MM-DD') : '')
		axios
			.get(
				"/api/get-sms-campaigns" +
				"?page=" +
				pageNumber +
				"&name=" +
				filterName +
				"&created=" +
				created +
				"&updated=" +
				updated +
				"&status=" +
				filterstatus +
				"&lang=" + localStorage.lang
			)
			.then((response) => {
				if (!$.trim(response.data.data) && pageNumber !== 1) {
					setPageNumber(pageNumber - 1);
					setFilter(!filter);
				}

				// // console.log(response.data);
				setCampaigns(response.data.data);
				setperPage(response.data.meta.per_page);
				setTotalItems(response.data.meta.total);
				setLoading(false);
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				setLoading(false);
			});

		setLoading(true);
		axios
			.post("/api/get-sms-campaigns-info?lang=" + localStorage.lang)
			.then((response) => {
				setNewCampaigns(response.data.new);
				setTotalCampaigns(response.data.total);
				setExistingCampaigns(response.data.existing);
				setDeletedCampaigns(response.data.deleted);
				setDraftedCampaigns(response.data.draft);
				setSentCampaigns(response.data.sent);
				setScheduledCampaigns(response.data.scheduled);
				setRecursiveCampaigns(response.data.recursive);
				setLoading(false);
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				setLoading(false);
			});
	};
	useEffect(() => {
		getCampaigns();
	}, [pageNumber, filter]);

	const clearFilter = async () => {
		if (filterName != "") setfilterName("");
		if (filterCreated != "") setfilterCreated("");
		if (filterUpdated != "") setfilterUpdated("");
		if (selectedOption != "") setSelectedOption("");
		setFilter(!filter);
		setfilterstatus("");
		setSelectedDate(null);
		setSelectedDate2(null);
	};

	const handleCampaignDelete = () => {
		setErrors([])
		const num = deleting;
		if (num) {
			setLoading(true);
			axios
				.post("/api/delete-sms-campaign/" + num + "?lang=" + localStorage.lang)
				.then((response) => {
					setLoading(false);
					getCampaigns();
					Swal.fire({
						title: t('Success'),
						text: t('Your Campaign has been deleted successfully!'),
						icon: 'success',
						showCancelButton: false,
						confirmButtonText: t('OK'),
						//cancelButtonText: 'No, keep it'
					})
				})
				.catch((error) => {
					setLoading(false);
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
				});
			handleClose();
		}
	};

	const handleStop = () => {
		// console.log(id);
		const id = stopping
		setErrors([])
		if (id) {
			setLoading(true);
			axios
				.post("/api/stop-sms-campaign/" + id + "?lang=" + localStorage.lang)
				.then((response) => {
					setLoading(false);
					getCampaigns();
					handleClose2();
					Swal.fire({
						title: t('Success'),
						text: t('Your Campaign has been Stoped successfully!'),
						icon: 'success',
						showCancelButton: false,
						confirmButtonText: t('OK'),
						//cancelButtonText: 'No, keep it'
					})
				})
				.catch((error) => {
					setLoading(false);
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
				});
		}
	};

	const getStatusColor = (status) => {
		if (status == 'Active') {
			return 'active';
		} else if (status == 'Draft') {
			return 'blue';
		} else if (status == 'Disabled') {
			return 'diabled';
		} else if (status == 'Sending') {
			return 'sending';
		} else if (status == 'Sent') {
			return 'sent';
		} else if (status == 'Stopped') {
			return 'stopped';
		} else if (status == 'Processing') {
			return 'processing';
		}
	}

	return (
		<Fragment>
			{loading ? <Spinner /> : null}
			<GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
			<section className="right-canvas email-campaign">
				<Container fluid>
					<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
						<div className="page-title">
							<h1>{t('SMS Campaigns')}</h1>
						</div>
						<div className="create-campaign">
							<Link to="/sms-campaign/create" className="btn btn-secondary">
								<span>{t('Create a Campaign')}<FontAwesomeIcon icon={faPlus} className="ml-1" /></span>
							</Link>
						</div>
					</div>
					<Row>
						<Col xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span className="title">{t('Total')}</span>
								<span className="value">{totalCampaigns}</span>
							</div>
						</Col>
						<Col xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span className="title">{t('sent')}</span>
								<span className="value">{sentCampaigns}</span>
							</div>
						</Col>
						{canSchedule ?
							<Col xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span className="title">{t('scheduled')}</span>
									<span className="value">{scheduledCampaigns}</span>
								</div>
							</Col>
							: ""
						}
						{canRecurr ?
							<Col xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span className="title">{t('recursive')}</span>
									<span className="value">{recursiveCampaigns}</span>
								</div>
							</Col>
							: ""
						}
						<Col xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span className="title">{t('draft')}</span>
								<span className="value">{draftCampaigns}</span>
							</div>
						</Col>
						<Col xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span className="title">{t('deleted')}</span>
								<span className="value">{deletedCampaigns}</span>
							</div>
						</Col>
					</Row>
					<div className="form-table-wrapper rounded-box-shadow bg-white">
						<Form className="em-form" method="GET">
							<div className="d-flex">
								<h5 className="filter-heading">
									{t('advance_filters')}
								</h5>
							</div>
							<Row>
								<Col xl="4" md="4" xs="12">
									<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
										<Form.Label>{t('Status')}</Form.Label>
										<Select
											onChange={(e) => handleChange(e)}
											options={options2}
											classNamePrefix="react-select"
											placeholder={t("Select Status")}
											value={selectedOption}
										/>
									</Form.Group>
								</Col>
								<Col xl="4" md="4" xs="12">
									<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
										<Form.Label>{t('Campaign Name')}</Form.Label>
										<input type="text" className="form-control" placeholder={t("eg Campaign name")} value={filterName} onChange={(e) => setfilterName(e.target.value)} />
									</Form.Group>
								</Col>
								<Col xl="4" md="4" xs="12">
									<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
										<Form.Label>{t('Date Created')}</Form.Label>
										<DateTimePicker
											format="y-MM-dd"
											className="em-calendar w-100"
											onChange={(e) => handleCalenderChange(e)} value={selectedDate}
										/>
										{renderErrorFor("invalid_format")}
									</Form.Group>
								</Col>
								<Col xl="12" lg="12" md="12" xs="12" className="d-flex justify-content-md-end justify-content-center">
									<Form.Group className="btn-wrapper filter-btns mb-3 mb-md-4 d-flex flex-column">
										<Form.Label>&nbsp;</Form.Label>
										<div className="d-flex justify-content-between">
											<button type="button" className="btn btn-primary" onClick={() => { if (!hasErrorFor('invalid_format')) { setFilter(!filter) } }}>
												<span>{t('Apply')}</span>
											</button>
											<button type="button" className="btn btn-secondary" onClick={clearFilter}>
												<span>{t('Reset')}</span>
											</button>
										</div>
									</Form.Group>
								</Col>
							</Row>
						</Form>
						<div className="status-table">
							<div>
								<div className="table-responsive">
									<Table className="align-middle em-table">
										<thead>
											<tr>

												<th>{t('Sr.')}</th>
												<th>{t('Campaign Name')}</th>
												<th>{t('Package')}</th>
												<th>{t('Date Created')}</th>
												<th>{t('Sending Type')}</th>
												<th>{t('Status')}</th>
												<th>{t('Actions')}</th>
											</tr>
										</thead>
										<tbody>
											{campaigns.length ? (
												campaigns.map((campaign, index) => (
													<tr key={campaign.hash_id}>
														<td>{(pageNumber - 1) * perPage + index + 1}</td>
														<td className="text-capitalize">{campaign.name ? campaign.name : "-"}</td>
														<td>{campaign.package_name ? campaign.package_name : "-"}</td>
														<td>
															{campaign.updated_at ?
																<Moment format="DD MMMM YYYY">
																	{moment.tz(moment(campaign.updated_at).utc(), localStorage.timezone)}
																</Moment>
																: t("Empty")}
														</td>
														<td>{campaign.type ? (campaign.type == 1 ? t("Immidiate") : (campaign.type == 2 ? t("Scheduled") : t("Recursive"))) : t("Not Selected")}</td>
														<td>
															<Badge className={"d-inline-block align-top badge bg-info status-" + getStatusColor(campaign.status)} > {t(campaign.status)} </Badge>
														</td>
														<td>
															<ul className="action-icons list-unstyled">
																<li><Link to={"/sms-campaign/view/" + campaign.hash_id + "?page=" + pageNumber} className="view-icon" title={t("View")}><FontAwesomeIcon icon={faEye} /></Link></li>
																{canViewReport && (campaign.status == "Sent" || campaign.status == "Stopped" || campaign.status == "Sending") ? <li><Link to={"/sms-campaign/" + campaign.hash_id + "/report"} className="view-icon" title={t("report")}><FontAwesomeIcon icon={faListAlt} /></Link></li> : ""}
																{campaign.status == "Draft" || campaign.status == "Active" ? <li><Link to={"/sms-campaign/" + campaign.hash_id + "/edit" + "?page=" + pageNumber} className="edit-icon" title={t("Edit")}><FontAwesomeIcon icon={faPencilAlt} /></Link></li> : ""}
																{campaign.status == "Sending" || campaign.status == "Active" ? <li><button onClick={(e) => handleShow2(e, campaign.hash_id)} className="stop-icon" title={t("stop")}><FontAwesomeIcon icon={faCircle} /></button></li> : ""}
																{campaign.status == "Draft" ? <li><button onClick={handleShow} className="dlt-icon" from={campaign.hash_id} title={t("Delete")} ariant="primary"><FontAwesomeIcon icon={faTrashAlt} /></button></li> : ""}
															</ul>
														</td>
													</tr>
												))
											) : (
												<tr>
													<td className="text-center" colSpan="7">
														{t('No SMS Campaigns Found')}
													</td>
												</tr>
											)}
										</tbody>
									</Table>
								</div>
								{/* pagination starts here */}
								<div className="mt-2">
									<Pagination
										activePage={pageNumber}
										itemsCountPerPage={perPage}
										totalItemsCount={totalItems}
										pageRangeDisplayed={pageRange}
										onChange={(e) => setPageNumber(e)}
									/>
								</div>
								{/* pagination ends here */}
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
					<p>{t('Are you sure you want to delete Campaign?')}</p>
				</Modal.Body>
				<Modal.Footer className="justify-content-center">
					<Button variant="primary" onClick={handleCampaignDelete}>
						<span>{t('Yes')}</span>
					</Button>
					<Button variant="secondary" onClick={handleClose}>
						<span>{t('Cancel')}</span>
					</Button>
				</Modal.Footer>
			</Modal>

			{/* Stop Modal */}
			<Modal show={show2} onHide={handleClose2} className="em-modal dlt-modal" centered>
				<Modal.Body className="d-flex align-items-center justify-content-center flex-column">
					<span className="dlt-icon">
						<FontAwesomeIcon icon={faCircle} />
					</span>
					<p>{t('are_you_sure_you_want_to_stop_this_campaign')}</p>
				</Modal.Body>
				<Modal.Footer className="justify-content-center">
					<Button variant="primary" onClick={() => handleStop()}>
						<span>{t('Yes')}</span>
					</Button>
					<Button variant="secondary" onClick={handleClose2}>
						<span>{t('Cancel')}</span>
					</Button>
				</Modal.Footer>
			</Modal>
		</Fragment >
	);
}

export default withTranslation()(AllSmsCampaigns);