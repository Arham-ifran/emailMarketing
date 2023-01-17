import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Form, Table, Badge, Modal, Button } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import Select from 'react-select';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faListAlt } from '@fortawesome/free-regular-svg-icons'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons'
import { faTrashAlt } from '@fortawesome/free-regular-svg-icons'
import { faPlus } from '@fortawesome/free-solid-svg-icons'
import DateTimePicker from 'react-datetime-picker'
import Pagination from "react-js-pagination";;
import Spinner from '../includes/spinner/Spinner';
import { faSpinner } from '@fortawesome/free-solid-svg-icons'
import Moment from "react-moment";
import moment from 'moment-timezone';
import { faCircle } from '@fortawesome/free-solid-svg-icons'
import GetUserPackage from "../Auth/GetUserPackage.js";
import { withTranslation } from 'react-i18next';

function EmailCampaignsList(props) {
	const { t } = props;
	const options2 = [
		{ value: '1', label: t('Active') },
		{ value: '2', label: t('Draft') },
		{ value: '3', label: t('Disabled') },
		{ value: '4', label: t('Sending') },
		{ value: '5', label: t('Sent') },
		{ value: '6', label: t('Stopped') },
	];

	// delete modal
	const [show, setShow] = useState(false);
	const [show2, setShow2] = useState(false);
	const [stopping, setStopping] = useState(0);
	const [report, setReport] = useState([]);
	const [loading, setLoading] = useState('');
	const [pageNumber, setPageNumber] = useState(new URLSearchParams(location.search).get('page') ? parseInt(new URLSearchParams(location.search).get('page')) : 1);
	const [perPage, setperPage] = useState(0);
	const [totalItems, setTotalItems] = useState(0);
	const [pageRange, setPageRange] = useState(5);
	const [campaignId, setCampaignId] = useState(0);
	const [counter, setCounter] = useState([]);
	const [selectedOption, setSelectedOption] = useState('')
	const [selectedDate, setSelectedDate] = useState()
	const [filterCreated, setfilterCreated] = useState("");
	const [campaignName, setCampaignName] = useState('');
	const [errors, setErrors] = useState([]);
	const [filter, setFilter] = useState(0);
	const [filterstatus, setfilterstatus] = useState('')

	const [userPackage, setUserPackage] = useState({});
	const [canSchedule, setCanSchedule] = useState(0);
	const [canRecurr, setCanRecurr] = useState(0);
	const [canViewReport, setCanViewReport] = useState(0);

	// useEffect(() => {
	// 	let params = new URLSearchParams(location.search);
	// 	if (params.get('page')) {
	// 		setPageNumber(params.get('page'));
	// 	}
	// }, [])

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

	//campaign filter statuses
	const handleChange = (selectedOption) => {
		setSelectedOption(selectedOption.value)
		setfilterstatus(selectedOption);
	}

	// date range picker
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

	const handleClose = () => setShow(false);

	const handleShow = (e, id) => {
		setShow(true);
		setCampaignId(id);
	}

	const handleClose2 = () => {
		setShow2(false);
		setStopping(0);
	}

	const handleShow2 = (e, id) => {
		setShow2(true);
		setStopping(id);
	}

	const campaignListing = () => {
		setLoading(true);
		axios.get('/api/campaign/report/?page=' + pageNumber + '&filt_status=' + selectedOption + '&filt_camp_name=' + campaignName + '&filter_date=' + (filterCreated != '' ? moment.tz(filterCreated + " 12:00", localStorage.timezone).utc().format('YYYY-MM-DD') : '') + '&lang=' + localStorage.lang)
			.then(response => {
				// console.log(response);
				if (response.data.status) {
					setLoading(false);
					setReport(response.data.data);
					setperPage(response.data.meta.per_page);
					setTotalItems(response.data.meta.total);
					setErrors([])

					//request to retrieve the stats of split testing
					axios.post('/api/campaign/stats-show?lang=' + localStorage.lang)
						.then(res => {
							if (res.data.status) {
								setCounter(res.data.data);
							}
						})
						.catch(error => {
							// console.log(error)
						})

				}
			})
			.catch(error => {
				// console.log(error)
			})

	}
	useEffect(() => {
		campaignListing();
	}, [pageNumber, filter]);

	const clearFilter = async () => {
		if (selectedOption != "") setSelectedOption("");
		if (filterCreated != "") setfilterCreated("");
		if (campaignName != "") setCampaignName("");
		setSelectedDate(null);
		setfilterstatus("");
		setFilter(!filter);
	};

	const deleteCampaign = () => {

		setLoading(true);
		axios
			.delete("/api/campaign/" + campaignId + "?lang=" + localStorage.lang)
			.then((response) => {
				setLoading(false);
				campaignListing();
				Swal.fire({
					title: t('Success'),
					text: t('Your Campaign has been deleted successfully!'),
					icon: 'warning',
					showCancelButton: false,
					confirmButtonText: t('OK'),
				})
			})
			.catch((error) => {
				setLoading(false);
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
			});
		handleClose();

	};

	const handleStop = () => {
		// console.log(id);
		const id = stopping
		setErrors([])
		if (id) {
			setLoading(true);
			axios
				.post("/api/campaign/stop/" + id + "?lang=" + localStorage.lang)
				.then((response) => {
					setLoading(false);
					campaignListing();
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

	const reportCampaignList = report.map((campaign, index) => {

		return (
			<tr key={index}>
				<td>{(pageNumber - 1) * perPage + index + 1}</td>
				<td>{campaign.name ? campaign.name : "-"}</td>
				<td>{campaign.subject ? campaign.subject : "-"}</td>
				<td>{campaign.package_name ? campaign.package_name : "-"}</td>
				<td>{campaign.track_clicks}</td>
				<td>{campaign.track_opens}</td>
				<td>
					<Moment format="DD MMMM YYYY">
						{moment.tz(moment(campaign.created_at).utc(), localStorage.timezone)}
					</Moment>
				</td>
				<td>{campaign.campaign_type ? (campaign.campaign_type == 1 ? t("Immidiate") : (campaign.campaign_type == 2 ? t("Scheduled") : t("Recursive"))) : t("Not Selected")}</td>
				<td>
					<Badge className={"d-inline-block align-top badge bg-info status-" + getStatusColor(campaign.status)} > {t(campaign.status)} </Badge>
				</td>
				<td>
					<ul className="action-icons list-unstyled">
						<li>
							<Link to={`/email-campaign/view/` + campaign.hash_id + "?page=" + pageNumber} className="view-icon" title={t("View")}><FontAwesomeIcon icon={faEye} /></Link>
						</li>
						{canViewReport && (campaign.status == 'Sending' || campaign.status == 'Sent' || campaign.status == "Stopped") ?
							<li>
								<Link to={`/email-campaign/` + campaign.hash_id + `/report`} className="view-icon" title={t("report")}><FontAwesomeIcon icon={faListAlt} /></Link>
							</li>
							: ""}
						{campaign.status == "Draft" || campaign.status == "Active" ?
							<React.Fragment>
								<li>
									<Link to={`/email-campaign/` + campaign.hash_id + `/edit` + "?page=" + pageNumber} className="edit-icon" title={t("Edit")}><FontAwesomeIcon icon={faPencilAlt} /></Link>
								</li>
							</React.Fragment>
							: ""}
						{campaign.status == "Sending" || campaign.status == "Active" ?
							<React.Fragment>
								<li>
									<button onClick={(e) => handleShow2(e, campaign.hash_id)} className="stop-icon" title={t("stop")}><FontAwesomeIcon icon={faCircle} /></button>
								</li>
							</React.Fragment>
							: ""}
						{campaign.status == "Draft" ?
							<React.Fragment>
								<li>
									<button className="dlt-icon" title={t("Delete")} ariant="primary" onClick={(e) => handleShow(e, campaign.hash_id)}><FontAwesomeIcon icon={faTrashAlt} /></button>
								</li>
							</React.Fragment>
							: ""
						}
					</ul>
				</td>
			</tr>
		);
	});

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
			<section className="right-canvas email-campaign">
				<Container fluid>
					<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
						<div className="page-title">
							<h1>{t('Email campaigns')}</h1>
						</div>
						<div className="create-campaign">
							<Link to="/email-campaign/create" className="btn btn-secondary">
								<span>{t('Create a Campaign')}<FontAwesomeIcon icon={faPlus} className="ml-1" /></span>
							</Link>
						</div>
					</div>
					<Row>
						<Col xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span className="title">{t('Total')}</span>
								<span className="value">
									{
										counter.total >= 0 ? counter.total :
											<FontAwesomeIcon icon={faSpinner} spin />
									}
								</span>
							</div>
						</Col>
						<Col xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span className="title">{t('sent')}</span>
								<span className="value">
									{
										counter.sent >= 0 ? counter.sent :
											<FontAwesomeIcon icon={faSpinner} spin />
									}
								</span>
							</div>
						</Col>
						{canSchedule ?
							<Col xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span className="title">{t('scheduled')}</span>
									<span className="value">
										{
											counter.scheduled >= 0 ? counter.scheduled :
												<FontAwesomeIcon icon={faSpinner} spin />
										}
									</span>
								</div>
							</Col>
							: ""
						}
						{canRecurr ?
							<Col xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span className="title">{t('recursive')}</span>
									<span className="value">
										{
											counter.recursive >= 0 ? counter.recursive :
												<FontAwesomeIcon icon={faSpinner} spin />
										}
									</span>
								</div>
							</Col>
							: ""
						}
						<Col xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span className="title">{t('draft')}</span>
								<span className="value">
									{
										counter.draft >= 0 ? counter.draft :
											<FontAwesomeIcon icon={faSpinner} spin />
									}
								</span>
							</div>
						</Col>
						<Col xl="4" lg="6" md="4" sm="6" xs="12">
							<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
								<span className="title">{t('deleted')}</span>
								<span className="value">
									{
										counter.deleted >= 0 ? counter.deleted :
											<FontAwesomeIcon icon={faSpinner} spin />
									}
								</span>
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
								<Col xl="4" lg="6" md="12" xs="12">
									<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
										<Form.Label>{t('Status')}</Form.Label>
										<Select
											onChange={(e) => handleChange(e)}
											options={options2}
											classNamePrefix="react-select"
											placeholder={t("Select Status")}
											value={filterstatus}
										/>
									</Form.Group>
								</Col>
								<Col xl="4" lg="6" md="12" xs="12">
									<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
										<Form.Label>{t('Campaign Name')}</Form.Label>
										<div className="input-holder">
											<input type="text" className="form-control" onChange={(e) => setCampaignName(e.target.value)} value={campaignName} placeholder={t("eg Campaign name")} />
										</div>
									</Form.Group>
								</Col>
								<Col xl="4" lg="6" md="12" xs="12">
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

								<Col md="12" xs="12" className="d-flex justify-content-md-end">
									<Form.Group className="btn-wrapper filter-btns mt-4 mb-3 mb-md-4 d-flex flex-column">
										<Form.Label className="mbl-label">&nbsp;</Form.Label>
										<div className="d-flex justify-content-between">
											<button onClick={() => { if (!hasErrorFor('invalid_format')) { campaignListing() } }} type="button" className="btn btn-primary">
												<span>{t('Apply')}</span>
											</button>
											<button type="button" onClick={clearFilter} className="btn btn-secondary">
												<span>{t('Reset')}</span>
											</button>
										</div>
									</Form.Group>
								</Col>
							</Row>
						</Form>
						<div className="status-table">

							<div className="table-responsive">
								<Table className="align-middle em-table">
									<thead>
										<tr>
											<th>{t('Sr.')}</th>
											<th>{t('Campaign Name')}</th>
											<th>{t('Subject')}</th>
											<th>{t('Package')}</th>
											<th>{t('Link Clicks')}</th>
											<th>{t('Open Rate')}</th>
											<th>{t('Date Created')}</th>
											<th>{t('Sending Type')}</th>
											<th>{t('Status')}</th>
											<th>{t('Actions')}</th>
										</tr>
									</thead>
									<tbody>
										{report.length ?
											reportCampaignList
											:
											<tr>
												<td className="text-center" colSpan="10">
													{t('No Campaigns Found')}
												</td>
											</tr>
										}
									</tbody>
								</Table>
							</div>
							<div className="mt-2">
								<Pagination
									activePage={pageNumber}
									itemsCountPerPage={perPage}
									totalItemsCount={totalItems}
									pageRangeDisplayed={pageRange}
									onChange={(e) => setPageNumber(e)}
								/>
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
					<Button variant="primary" onClick={deleteCampaign}>
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
		</React.Fragment>
	);
}

export default withTranslation()(EmailCampaignsList);