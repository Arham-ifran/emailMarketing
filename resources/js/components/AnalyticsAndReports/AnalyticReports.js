import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Container, Row, Col, Tabs, Tab, Table, Badge, Form } from 'react-bootstrap';
import Select from 'react-select';
import Spinner from "../includes/spinner/Spinner";
import './AnalyticReports.css';
import Moment from 'react-moment';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEye } from "@fortawesome/free-regular-svg-icons";
import { faListAlt } from "@fortawesome/free-regular-svg-icons";
import Pagination from "react-js-pagination";
import DateTimePicker from 'react-datetime-picker'
import moment from 'moment-timezone';
import { withTranslation } from 'react-i18next';
import GetUserPackage from "../Auth/GetUserPackage.js";

function AnalyticReports(props) {
	const { t } = props;
	const [loading, setLoading] = useState(false);
	const [refresh, setRefresh] = useState(0)
	const [subscribers, setSubscribers] = useState([])
	const [sms_campaigns, setSms_Campaigns] = useState([])
	const [email_campaigns, setEmail_Campaigns] = useState([])
	const [split_campaigns, setSplit_Campaigns] = useState([])
	const [smsCampaignData, setSmsCampaignData] = useState({
		total_campaigns_sent: 0,
		sent_successfilly: 0,
		sending_fails: 0,
	});
	const [emailCampaignData, setEmailCampaignData] = useState({
		total_campaigns_sent: 0,
		total_click_rate: 0,
		total_open_rate: 0,
	});
	const [splitCampaignData, setSplitCampaignData] = useState({
		total_campaigns_sent: 0,
		total_click_rate: 0,
		total_open_rate: 0,
	});

	// for filtering
	const [selectedOption, setSelectedOption] = useState('')
	const [filterstatus, setfilterstatus] = useState('')
	const [selectedDate, setSelectedDate] = useState()
	const [filterCreated, setfilterCreated] = useState("");
	const [campaignName, setCampaignName] = useState('');
	const options = [
		{ value: '2', label: t('Sending') },
		{ value: '3', label: t('Sent') },
		{ value: '6', label: t('Stopped') },
	];
	const options2 = [
		{ value: '4', label: t('Sending') },
		{ value: '5', label: t('Sent') },
		{ value: '6', label: t('Stopped') },
	];

	// for pagination
	const [pageRange, setPageRange] = useState(5);
	const [perPage, setperPage] = useState(5);
	const [pageNumber, setPageNumber] = useState(1);
	const [module, setModule] = useState(0);
	// // email
	const [pageNumberEmail, setPageNumberEmail] = useState(1);
	const [totalItemsEmail, setTotalItemsEmail] = useState(0);
	// // sms
	const [pageNumberSms, setPageNumberSms] = useState(1);
	const [totalItemsSms, setTotalItemsSms] = useState(0);
	// // split
	const [pageNumberSplit, setPageNumberSplit] = useState(1);
	const [totalItemsSplit, setTotalItemsSplit] = useState(0);

	const [userPackage, setUserPackage] = useState({});
	const [canSplitTest, setCanSplitTest] = useState(0);

	// errors
	const [errors, setErrors] = useState([]);
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
					if (Object.keys(userPackage.features).findIndex(val => val === "12") >= 0) { // split allowed
						setCanSplitTest(true)
					} else {
						setCanSplitTest(false)
					}
				}
			}
		}
		load();
	}, [userPackage])

	useEffect(() => {
		const get_analytics = () => {
			setLoading(true);
			// module => 1=email, 2=sms, 3=split
			axios
				.get("/api/get-analytics?page=" + pageNumber + "&module=" + module + '&filt_status=' + selectedOption + '&filt_camp_name=' + campaignName + '&filter_date=' + (filterCreated != '' ? moment.tz(filterCreated + " 12:00", localStorage.timezone).utc().format('YYYY-MM-DD') : '') + '&lang=' + localStorage.lang)
				.then((response) => {
					setLoading(false);
					const data_received = response.data;
					const sms_camp = data_received.sms_campaigns;
					const subs = data_received.subscribers;
					// sms campaign data
					var total_campaigns = data_received.total_sms_campaigns;
					var sms_sent = data_received.sms_sent;
					var sent_successfully = data_received.sms_sent_successfilly;
					var sending_fails = data_received.sms_sending_fails;
					setSmsCampaignData({
						total_campaigns_sent: sms_sent,
						sent_successfilly: sent_successfully,
						sending_fails: sending_fails,
					})
					setSubscribers(subs);
					if (module == 0 || module == 2) {
						setSms_Campaigns(sms_camp);
					}
					setTotalItemsSms(sms_sent);

					// email campaign data
					const email_camp = data_received.email_campaigns;
					var total_s_campaigns_sent = data_received.total_email_campaigns_sent;
					var total_click_rate = data_received.email_avg_click_rate;
					var total_open_rate = data_received.email_avg_open_rate;
					setEmailCampaignData({
						total_campaigns_sent: total_s_campaigns_sent,
						total_click_rate: total_click_rate,
						total_open_rate: total_open_rate,
					})
					if (module == 0 || module == 1) {
						setEmail_Campaigns(email_camp);
					}
					setTotalItemsEmail(total_s_campaigns_sent);
					// split testing data
					const split_camp = data_received.split_campaigns;
					var total_split_campaigns_sent = data_received.total_split_campaigns_sent;
					var total_split_click_rate = data_received.split_avg_click_rate;
					var total_split_open_rate = data_received.split_avg_open_rate;
					setSplitCampaignData({
						total_campaigns_sent: total_split_campaigns_sent,
						total_click_rate: total_split_click_rate,
						total_open_rate: total_split_open_rate,
					})
					if (module == 0 || module == 3) {
						setSplit_Campaigns(split_camp);
					}
					setTotalItemsSplit(total_split_campaigns_sent);
				})
				.catch((error) => {
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
					setLoading(false);
				});
		}
		get_analytics();
	}, [refresh])

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

	const clearFilter = async () => {
		if (selectedOption != "") setSelectedOption("");
		if (filterCreated != "") setfilterCreated("");
		if (campaignName != "") setCampaignName("");
		setSelectedDate(null);
		setfilterstatus("");
		setRefresh(!refresh);
	};

	return (
		<React.Fragment>
			<GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
			{loading ? <Spinner /> : null}
			<Container fluid>
				<Tabs defaultActiveKey="email-campaigns" id="main-tabs" className="em-tabs mb-3" onClick={clearFilter}>
					{/* ===================== */}
					{/* EMAIL ANALYTICS START */}
					{/* ===================== */}
					<Tab eventKey="email-campaigns" title={t('Email Campaigns')}>
						<Row>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span className="title">{t('Total Campaigns Sent')}</span>
									<span className="value">{emailCampaignData.total_campaigns_sent}</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span className="title">{t('Average Open Rate')}</span>
									<span className="value">{emailCampaignData.total_open_rate}</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span className="title">{t('Average Click Rate')}</span>
									<span className="value">{emailCampaignData.total_click_rate}</span>
								</div>
							</Col>
						</Row>
						<Row>
							<div className="page-title ml-3">
								<h5>{t('Recent Email campaigns')}</h5>
							</div>
							<div className="form-table-wrapper rounded-box-shadow bg-white w-100">
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
													placeholder={t('Select Status')}
													value={filterstatus}
												/>
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
										<Col xl="4" lg="6" md="12" xs="12">
											<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
												<Form.Label>{t('Campaign Name')}</Form.Label>
												<div className="input-holder">
													<input type="text" className="form-control" onChange={(e) => setCampaignName(e.target.value)} value={campaignName} placeholder={t('eg Campaign name')} />
												</div>
											</Form.Group>
										</Col>

										<Col md="12" xs="12" className="d-flex justify-content-md-end">
											<Form.Group className="btn-wrapper filter-btns mt-lg-0 mt-md-0 mt-2 mb-3 mb-md-4 d-flex flex-column">
												<Form.Label className="mbl-label">&nbsp;</Form.Label>
												<div className="d-flex justify-content-between">
													<button onClick={() => { if (!hasErrorFor('invalid_format')) { setModule(1); setRefresh(!refresh); } }} type="button" className="btn btn-primary">
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
													<th>{t('Package')}</th>
													<th>{t('Opens')}</th>
													<th>{t('Clicks')}</th>
													<th>{t('Date Created')}</th>
													<th>{t('Last Modified')}</th>
													<th>{t('Sending Type')}</th>
													<th>{t('Status')}</th>
													<th>{t('Action')}</th>
												</tr>
											</thead>
											<tbody>
												{email_campaigns.length ? (
													email_campaigns.map((campaign, index) => (
														<tr key={campaign.hash_id}>
															<td>{(pageNumberEmail - 1) * perPage + index + 1}</td>
															<td className="text-capitalize">{campaign.name}</td>
															<td>{campaign.package_name ? campaign.package_name : "-"}</td>
															<td>{campaign.track_opens}</td>
															<td>{campaign.track_clicks}</td>
															<td>
																<Moment format="DD MMMM YYYY">
																	{moment.tz(moment(campaign.created_at).utc(), localStorage.timezone)}
																</Moment>
															</td>
															<td>
																{campaign.updated_at ?
																	<Moment format="DD MMMM YYYY">
																		{moment.tz(moment(campaign.updated_at).utc(), localStorage.timezone)}
																	</Moment>
																	: t("Empty")}
															</td>
															<td>{campaign.campaign_type ? (campaign.campaign_type == 1 ? t("Immidiate") : (campaign.campaign_type == 2 ? t("Scheduled") : t("Recursive"))) : t("Not Selected")}</td>
															<td>
																<Badge className={"d-inline-block align-top badge bg-" + (campaign.status == 'Sent' || campaign.status == 'Stopped' ? "success" : "info")} > {t(campaign.status)} </Badge>
															</td>
															<td>
																<ul className="action-icons list-unstyled">
																	<li><Link to={"/email-campaign/view/" + campaign.hash_id} className="view-icon" title={t("View")}><FontAwesomeIcon icon={faEye} /></Link></li>
																	<li><Link to={"/email-campaign/" + campaign.hash_id + "/report"} className="view-icon" title={t("report")}><FontAwesomeIcon icon={faListAlt} /></Link></li>
																</ul>
															</td>
														</tr>
													))
												) : (
													<tr>
														<td className="text-center" colSpan="10">
															{t('No Campaigns Found')}
														</td>
													</tr>
												)}
											</tbody>
										</Table>
									</div>
									{email_campaigns.length ?
										<>
											{/* pagination starts here */}
											<div className="mt-2">
												<Pagination
													activePage={pageNumberEmail}
													itemsCountPerPage={perPage}
													totalItemsCount={totalItemsEmail}
													pageRangeDisplayed={pageRange}
													onChange={(e) => { setPageNumber(e); setPageNumberEmail(e); setModule(1); setRefresh(!refresh) }}
												/>
											</div>
											{/* pagination ends here */}
										</>
										: ""}
								</div>
							</div>
						</Row>
					</Tab>
					{/* =================== */}
					{/* EMAIL ANALYTICS END */}
					{/* =================== */}

					{/* ===================== */}
					{/* SPLIT ANALYTICS START */}
					{/* ===================== */}
					{canSplitTest ?
						<Tab eventKey="split-testing" title={t('Split Testing')}>
							<Row>
								<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
									<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
										<span className="title">{t('Total Campaigns Sent')}</span>
										<span className="value">{splitCampaignData.total_campaigns_sent}</span>
									</div>
								</Col>
								<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
									<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
										<span className="title">{t('Average Open Rate')}</span>
										<span className="value">{splitCampaignData.total_open_rate}</span>
									</div>
								</Col>
								<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
									<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
										<span className="title">{t('Average Clicks Rate')}</span>
										<span className="value">{splitCampaignData.total_click_rate}</span>
									</div>
								</Col>
							</Row>
							<Row>
								<div className="page-title ml-3">
									<h5>{t('Recent Email campaigns')}</h5>
								</div>
								<div className="form-table-wrapper rounded-box-shadow bg-white w-100">
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
														placeholder={t('Select Status')}
														value={filterstatus}
													/>
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
											<Col xl="4" lg="6" md="12" xs="12">
												<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
													<Form.Label>{t('Campaign Name')}</Form.Label>
													<div className="input-holder">
														<input type="text" className="form-control" onChange={(e) => setCampaignName(e.target.value)} placeholder={t('eg Campaign name')} />
													</div>
												</Form.Group>
											</Col>

											<Col md="12" xs="12" className="d-flex justify-content-md-end">
												<Form.Group className="btn-wrapper filter-btns mt-lg-0 mt-md-0 mt-2 mb-3 mb-md-4 d-flex flex-column">
													<Form.Label className="mbl-label">&nbsp;</Form.Label>
													<div className="d-flex justify-content-between">
														<button onClick={() => { if (hasErrorFor('invalid_format')) { setModule(3); setRefresh(!refresh) } }} type="button" className="btn btn-primary">
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
														<th>{t('Split By')}</th>
														<th>{t('Package')}</th>
														<th>{t('Opens')}</th>
														<th>{t('Clicks')}</th>
														<th>{t('Date Created')}</th>
														<th>{t('Last Modified')}</th>
														<th>{t('Sending Type')}</th>
														<th>{t('Status')}</th>
														<th>{t('Action')}</th>
													</tr>
												</thead>
												<tbody>
													{split_campaigns.length ? (
														split_campaigns.map((campaign, index) => (
															<tr key={campaign.hash_id}>
																<td>{(pageNumberSplit - 1) * perPage + index + 1}</td>
																<td className="text-capitalize">{campaign.name}</td>
																<td>{campaign.split_test_param == 1 ? t("Subject") : t("Mail Content")}</td>
																<td>{campaign.package_name ? campaign.package_name : "-"}</td>
																<td>{campaign.track_opens}</td>
																<td>{campaign.track_clicks}</td>
																<td>
																	<Moment format="DD MMMM YYYY">
																		{moment.tz(moment(campaign.created_at).utc(), localStorage.timezone)}
																	</Moment>
																</td>
																<td>
																	{campaign.updated_at ?
																		<Moment format="DD MMMM YYYY">
																			{moment.tz(moment(campaign.updated_at).utc(), localStorage.timezone)}
																		</Moment>
																		: t("Empty")}
																</td>
																<td>{campaign.campaign_type ? (campaign.campaign_type == 1 ? t("Immidiate") : (campaign.campaign_type == 2 ? t("Scheduled") : t("Recursive"))) : t("Not Selected")}</td>
																<td>
																	<Badge className={"d-inline-block align-top badge bg-" + (campaign.status == 'Sent' || campaign.status == 'Stopped' ? "success" : "info")} > {t(campaign.status)} </Badge>
																</td>
																<td>
																	<ul className="action-icons list-unstyled">
																		<li><Link to={"/split-testing/view/" + campaign.hash_id} className="view-icon" title={t("View")}><FontAwesomeIcon icon={faEye} /></Link></li>
																		<li><Link to={"/split-testing/" + campaign.hash_id + "/report"} className="view-icon" title={t("report")}><FontAwesomeIcon icon={faListAlt} /></Link></li>
																	</ul>
																</td>
															</tr>
														))
													) : (
														<tr>
															<td className="text-center" colSpan="11">
																{t('No Campaigns Found')}
															</td>
														</tr>
													)}
												</tbody>
											</Table>
										</div>
										{split_campaigns.length ?
											<>
												{/* pagination starts here */}
												<div className="mt-2">
													<Pagination
														activePage={pageNumberSplit}
														itemsCountPerPage={perPage}
														totalItemsCount={totalItemsSplit}
														pageRangeDisplayed={pageRange}
														onChange={(e) => { setPageNumber(e); setPageNumberSplit(e); setModule(3); setRefresh(!refresh) }}
													/>
												</div>
												{/* pagination ends here */}
											</>
											: ""}
									</div>
								</div>
							</Row>
						</Tab>
						: ""}
					{/* =================== */}
					{/* SPLIT ANALYTICS END */}
					{/* =================== */}

					{/* =================== */}
					{/* SMS ANALYTICS START */}
					{/* =================== */}
					<Tab eventKey="sms-campaigns" title={t('SMS Campaigns')}>
						<Row>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span className="title">{t('Total Campaigns Sent')}</span>
									<span className="value">{smsCampaignData.total_campaigns_sent}</span>
								</div>
							</Col>
						</Row>
						<Row>
							<div className="page-title ml-3">
								<h5>{t('Recent SMS Campaigns')}</h5>
							</div>
							<div className="form-table-wrapper rounded-box-shadow bg-white w-100">
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
													options={options}
													classNamePrefix="react-select"
													placeholder={t('Select Status')}
													value={filterstatus}
												/>
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
										<Col xl="4" lg="6" md="12" xs="12">
											<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
												<Form.Label>{t('Campaign Name')}</Form.Label>
												<div className="input-holder">
													<input type="text" className="form-control" onChange={(e) => setCampaignName(e.target.value)} placeholder={t("eg Campaign name")} />
												</div>
											</Form.Group>
										</Col>

										<Col md="12" xs="12" className="d-flex justify-content-md-end">
											<Form.Group className="btn-wrapper filter-btns mt-lg-0 mt-md-0 mt-2 mb-3 mb-md-4 d-flex flex-column">
												<Form.Label className="mbl-label">&nbsp;</Form.Label>
												<div className="d-flex justify-content-between">
													<button onClick={() => { if (hasErrorFor('invalid_format')) { setModule(2); setRefresh(!refresh) } }} type="button" className="btn btn-primary">
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
													<th>{t('Package')}</th>
													<th>{t('Date Created')}</th>
													<th>{t('Last Modified')}</th>
													<th>{t('Sending Type')}</th>
													<th>{t('Status')}</th>
													<th>{t('Action')}</th>
												</tr>
											</thead>
											<tbody>
												{sms_campaigns.length ? (
													sms_campaigns.map((campaign, index) => (
														<tr key={campaign.hash_id}>
															<td>{(pageNumberSms - 1) * perPage + index + 1}</td>
															<td className="text-capitalize">{campaign.name}</td>
															<td>{campaign.package_name ? campaign.package_name : "-"}</td>
															<td>
																<Moment format="DD MMMM YYYY">
																	{moment.tz(moment(campaign.created_at).utc(), localStorage.timezone)}
																</Moment>
															</td>
															<td>
																{campaign.updated_at ?
																	<Moment format="DD MMMM YYYY">
																		{moment.tz(moment(campaign.updated_at).utc(), localStorage.timezone)}
																	</Moment>
																	: t("Empty")}
															</td>
															<td>{campaign.type ? (campaign.type == 1 ? t("Immidiate") : (campaign.type == 2 ? t("Scheduled") : t("Recursive"))) : t("Not Selected")}</td>
															<td>
																<Badge className={"d-inline-block align-top badge bg-" + (campaign.status == 'Sent' || campaign.status == 'Stopped' ? "success" : "info")} > {t(campaign.status)} </Badge>
															</td>
															<td>
																<ul className="action-icons list-unstyled">
																	<li><Link to={"/sms-campaign/view/" + campaign.hash_id} className="view-icon" title={t("View")}><FontAwesomeIcon icon={faEye} /></Link></li>
																	<li><Link to={"/sms-campaign/" + campaign.hash_id + "/report"} className="view-icon" title={t("report")}><FontAwesomeIcon icon={faListAlt} /></Link></li>
																</ul>
															</td>
														</tr>
													))
												) : (
													<tr>
														<td className="text-center" colSpan="8">
															{t('No SMS Campaigns Found')}
														</td>
													</tr>
												)}
											</tbody>
										</Table>
									</div>
									{sms_campaigns.length ?
										<>
											{/* pagination starts here */}
											<div className="mt-2">
												<Pagination
													activePage={pageNumberSms}
													itemsCountPerPage={perPage}
													totalItemsCount={totalItemsSms}
													pageRangeDisplayed={pageRange}
													onChange={(e) => { setPageNumber(e); setPageNumberSms(e); setModule(2); setRefresh(!refresh) }}
												/>
											</div>
											{/* pagination ends here */}
										</>
										: ""}
								</div>
							</div>
						</Row>
					</Tab>
					{/* ================= */}
					{/* SMS ANALYTICS END */}
					{/* ================= */}

					{/* ====================== */}
					{/* SUBSCRIBERS LIST START */}
					{/* ====================== */}
					<Tab eventKey="subscribers-lists" title={t('Subscribers List')}>
						<h5 className="ml-3">{t('Subscribers List')}</h5>
						<div className="form-table-wrapper rounded-box-shadow bg-white w-100 m-3">
							<div className="table-responsive">
								<Table className="em-table align-middle">
									<thead>
										<tr>
											<th>{t('Contacts Name')}</th>
											<th>{t('Email')}</th>
											<th>{t('Phone Number')}</th>
											<th>{t('Date Created')}</th>
											<th>{t('Last Modified')}</th>
											<th>{t('Mailing List')}</th>
										</tr>
									</thead>
									<tbody>
										{subscribers.length ? (
											subscribers.map((contact) => (
												<tr key={contact.hash_id}>
													<td className="text-capitalize">
														{contact.first_name}{" "}
														{contact.last_name}
													</td>
													<td>
														{" "}
														{contact.email}{" "}
													</td>
													<td>
														{
															contact.country_Code
														}{" "}
														{contact.number}
													</td>
													<td>
														<Moment format="DD MMMM YYYY">
															{moment.tz(moment(contact.created_at).utc(), localStorage.timezone)}
														</Moment>
													</td>
													<td>
														<Moment format="DD MMMM YYYY">
															{moment.tz(moment(contact.updated_at).utc(), localStorage.timezone)}
														</Moment>
													</td>
													<td>
														{contact.groups.map(
															(row) =>
																<span key={row.hash_id} className="badge badge-secondary">{row.name}</span>
														)}
													</td>
												</tr>
											))
										) : (
											<tr>
												<td className="text-center" colSpan="6">
													{t('No Contacts Found')}
												</td>
											</tr>
										)}
									</tbody>
								</Table>
							</div>
						</div>
					</Tab>
					{/* ===================== */}
					{/* SUBSCRIBERS LIST ENDS */}
					{/* ===================== */}

				</Tabs>
			</Container>
		</React.Fragment>
	);
}

export default withTranslation()(AnalyticReports);