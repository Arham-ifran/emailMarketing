import React, { useState, useEffect } from 'react';
import { Container, Table, Form, Row, Col, Modal, Button } from 'react-bootstrap';
import Select from 'react-select';
import './css/EditSmsCampaign.css';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faInfoCircle } from "@fortawesome/free-solid-svg-icons"
import DateTimePicker from 'react-datetime-picker';
import Spinner from "../includes/spinner/Spinner";
import { daysOfWeek, daysOfMonth, MonthsOfYear } from '../../constants';
import PhoneInput from 'react-phone-number-input'
import Multiselect from 'multiselect-react-dropdown';
import Pagination from "react-js-pagination";
import { withTranslation } from 'react-i18next';
import { Link } from 'react-router-dom';

var addedContacts = [];
var excludedContacts = [];
var loading_count = 0;
function ViewCampaign(props) {
	const { t } = props;
	const [loading, setLoading] = useState('');
	const [recursiveCampaignType, setRecursiveCampaignType] = useState('');
	const [dayOfWeek, setDayOfWeek] = useState('');
	const [dayOfMonth, setDayOfMonth] = useState('');
	const [monthOfYear, setMonthOfYear] = useState('');
	const [sendCampaign, setSendCampaign] = useState(1);
	const [selectedDate, setSelectedDate] = useState('')
	const [options, setOptions] = useState([])
	const [name, setName] = useState("");
	const [contactsNum, setContactsNum] = useState(0);
	const [excludesNum, setExcludesNum] = useState(0);
	const [message, setMessage] = useState("");
	const [sender_name, setSendername] = useState("");
	const [sender_number, setSendernum] = useState("");
	const [times, setTimes] = useState("");
	const [groups, setGroups] = useState([]);
	const [contacts, setContacts] = useState([]);
	const [uniqueContacts, setUniqueContacts] = useState([]);
	const [selectedGroups, setSelectedGroups] = useState([])

	// modal
	const [show, setShow] = useState(false);
	const handleClose = () => {
		setShow(false);
	};
	const handleShow = (e) => {
		e.preventDefault();
		setShow(true);
	};

	// Get Mailing Lists
	const getGroups = () => {
		setLoading(true);
		axios
			.get("/api/get-all-groups?lang=" + localStorage.lang)
			.then((response) => {
				loading_count++;
				if (loading_count >= 5)
					setLoading(false);
				const data = [];
				const moredata = [
					response.data.data.map((row, index) => ({
						value: row.hash_id,
						index: index,
						label: row.name,
					})),
				];
				const opt = data.concat(moredata[0]);
				setOptions(opt);
				setGroups(response.data.data);
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				loading_count++;
				if (loading_count >= 5)
					setLoading(false);
			});
	}

	// Get Mailing Lists
	const getSmsTemplates = () => {
		setLoading(true);
		axios
			.get("/api/get-sms-templates?lang=" + localStorage.lang)
			.then((response) => {
				loading_count++;
				if (loading_count >= 5)
					setLoading(false);
				const data = response.data.data.data.map(row => ({
					value: row.message,
					label: row.name,
				}));
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				loading_count++;
				if (loading_count >= 5)
					setLoading(false);
			});
	}

	const getCampaign = (id) => {
		setLoading(true);

		axios
			.get("/api/get-sms-campaign/" + id + "?lang=" + localStorage.lang)
			.then((response) => {
				loading_count++;
				if (loading_count >= 5)
					setLoading(false);
				const received_data = response.data.data[0]
				setName(received_data.name);
				setMessage(received_data.message);
				setSendernum(received_data.sender_number);
				setSendername(received_data.sender_name);
				setSendCampaign(received_data.type);

				setContactsNum(received_data.contacts.length);
				var c = received_data.contacts.map(cont => cont.hash_id);
				addedContacts = c;
				setExcludesNum(received_data.excludes.length);
				var c2 = received_data.excludes.map(cont => cont.hash_id);
				excludedContacts = c2;

				if (received_data.type == 3) {
					setRecursiveCampaignType(received_data.recursive_campaign_type);
					setDayOfWeek(received_data.day_of_week);
					setDayOfMonth(received_data.day_of_month);
					setMonthOfYear(received_data.month_of_year);
					setTimes(received_data.no_of_time);
				}
				if (received_data.type == 2) {
					var date = received_data.schedule_date;
					// console.log(date);
					setSelectedDate(new Date(date))
				}

			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				loading_count++;
				if (loading_count >= 5)
					setLoading(false);
			});
	};

	const goBack = () => {
		let params = new URLSearchParams(location.search);
		if (params.get('page')) {
			window.location.href = "/sms-campaign?page=" + params.get('page');
		}
		else {
			window.location.href = "/sms-campaign";
		}
	}

	const getCampaign2 = (id) => {
		setLoading(true);
		axios
			.get("/api/get-sms-campaign/" + id + "?lang=" + localStorage.lang)
			.then((response) => {
				loading_count++;
				if (loading_count >= 5)
					setLoading(false);
				const received_data = response.data.data[0]
				var selected_groups = []
				received_data.group_ids.map(val => selected_groups.push(options.find(o => o.value == val)))
				setSelectedGroups(selected_groups)
				var tempcontacts = [];
				received_data.group_ids.map(id => {
					var selectedOpt = options.find(o => o.value == id)
					if (selectedOpt.value != "")
						var arr1 = [...tempcontacts];
					var arr2 = groups[selectedOpt.index].contacts;
					tempcontacts = arr1.concat(arr2);
				})
				setContacts([...tempcontacts].filter(contact => contact.for_sms == 1))
				setUniqueContacts([...tempcontacts].filter(contact => contact.for_sms == 1))
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				loading_count++;
				if (loading_count >= 5)
					setLoading(false);
			});
	};



	// add contacts modal
	const [contactsAdd, setContactsAdd] = useState([]);
	const [pageNumber, setPageNumber] = useState(1);
	const [perPage, setperPage] = useState(0);
	const [totalItems, setTotalItems] = useState(0);
	const [pageRange, setPageRange] = useState(5);
	const [filterName, setfilterName] = useState("");
	const [showAdd, setShowAdd] = useState(false);
	const handleCloseAdd = () => {
		setShowAdd(false);
	};
	const handleShowAdd = (e) => {
		e.preventDefault();
		setShowAdd(true);
		if (filterName != "") setfilterName("");
	};

	// add contacts modal

	const getContacts = () => {
		setLoading(true);
		axios
			.get(
				"/api/get-contacts" +
				"?page=" +
				pageNumber +
				"&name=" +
				filterName +
				"&lang=" + localStorage.lang +
				"&type=" + 1
			)
			.then((response) => {
				if (!$.trim(response.data.data) && pageNumber !== 1) {
					setPageNumber(pageNumber - 1);
				}
				setContactsAdd(response.data.data);
				setperPage(response.data.meta.per_page);
				setTotalItems(response.data.meta.total);
				loading_count++;
				if (loading_count >= 5)
					setLoading(false);
				// // console.log(contacts);
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
					loading_count++;
					if (loading_count >= 5)
						setLoading(false);
				}
			});
	};

	useEffect(() => {
		loading_count = 0;
		getGroups();
		getSmsTemplates()
	}, []);
	useEffect(() => {
		let parseUriSegment = window.location.pathname.split("/");
		if (parseUriSegment.indexOf('sms-campaign') && parseUriSegment.indexOf('view') != -1) {
			getCampaign(parseUriSegment[3]);
		}
	}, []);
	useEffect(() => {
		let parseUriSegment = window.location.pathname.split("/");
		if (parseUriSegment.indexOf('sms-campaign') && parseUriSegment.indexOf('view') != -1) {
			if (groups.length)
				getCampaign2(parseUriSegment[3]);
		}
	}, [groups]);
	// for contacts
	useEffect(() => {
		getContacts()
	}, [pageNumber, filterName]);

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>{t('view_sms_campaign')}</h1>
					</div>
				</div>
				<Form className="create-form-holder">
					<div className="bg-white rounded-box-shadow">
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="campaign-name">{t('Campaign Name')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<input id="campaign-name" disabled className="form-control" value={name} type="text" placeholder={t("eg Campaign name")} />
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="sender-name">{t('Sender Name')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<input id="sender-name" disabled className="form-control" value={sender_name} type="text" placeholder="e.g. Sam Smith" />
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="sender-no">{t('reply_to_number')}</Form.Label>
							<div className="flex-fill input-holder PhoneInput d-flex  flex-column">
								<PhoneInput
									className="form-control d-flex"
									placeholder={t("Enter phone number")}
									value={sender_number}
									disabled
									placeholder="e.g. +49 1579230198"
								/>
							</div>
						</Form.Group>


						<Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0"></Form.Label>
							<div className="flex-fill input-holder">
								<div className="alert-info p-2" role="alert">
									<p>{t("On sending campaign, following keywords with double curly brackets")} {t("e.g")} <strong> {"{{" + t('keyword') + "}}"} </strong> {t('will be replaced by their values:')}</p>
									<li><strong>{t('Name')}</strong> : {t("Contacts full name")}  </li>
								</div>
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="sms-text">{t('SMS Text')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<textarea id="sms-text" rows="5" cols="5" maxLength='250' disabled value={message} className="form-control" placeholder={t("eg Message here")} />
								<small> {250 - message.length} {t('characters_remaining')} </small>
								<p>
									<FontAwesomeIcon icon={faInfoCircle}></FontAwesomeIcon>
									{" "}
									{t('limit_is_250_characters_including_spaces')}
								</p>
							</div>
						</Form.Group>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0">{t('Contact Details')} <b className="req-sign">*</b></Form.Label>
							<div className="mt-lg-0 mt-0  d-flex flex-column input-holder flex-sm-row align-items-center justify-content-between mbl-flex-wrap">
								<p>
									<FontAwesomeIcon icon={faInfoCircle}></FontAwesomeIcon>
									{" "}
									{t('you_can_either_add_individual_contacts_contact_lists_or_both_to_the_campaign')}
								</p>
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row form-group">
							<Form.Label className="mb-2 mb-md-0">{t('Add Contacts')} </Form.Label>
							<div className="mt-lg-0 mt-3 d-flex input-holder flex-column flex-sm-row align-items-center justify-content-between mbl-flex-wrap">
								{/* <h3>OR</h3> */}
								<button type="submit" onClick={handleShowAdd} className="btn btn-secondary ml-lg-3 ml-0 mt-0 mr-3 mb-0">
									<span>{t('Contacts')}</span>
								</button>
								<p className="mb-0 ms-2">{contactsNum} {t('Contact(s) Added')} </p>
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row form-group">
							<Form.Label className="mb-2 mb-md-0">{t('Contact Lists')}</Form.Label>
							<div className="d-flex flex-xl-row flex-column flex-lg-nowrap flex-wrap  align-items-start justify-content-between input-holder">
								<div className="flex-fill input-holder edit-sms-select">
									<div className="subscriber-select w-100">
										{selectedGroups.length ?
											selectedGroups.map(group =>
												<span className="chip" key={group.hash_id}> {group.label} </span>
											)
											:
											t('no_groups_selected')
										}
									</div>
								</div>
								<div className="mt-lg-0 mt-1 d-flex flex-column flex-sm-row align-items-center justify-content-between">
									{selectedGroups.length ?
										<React.Fragment>
											<button type="submit" onClick={handleShow} className="exclude-btn btn btn-secondary ml-lg-3 ml-0 mt-0 mr-3 mb-0">
												<span>{t('Contacts')}</span>
											</button>
											<p className="mb-0 ms-lg-2">{excludesNum} {t('contacts_excluded')} </p>
										</React.Fragment>
										: ""}
								</div>
							</div>
						</Form.Group>

						{/* Email campaign schedule options  */}
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="reply-to-address">{t('Send Campaign')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder radio-btns-holder d-flex flex-column pt-0">
								<div className="d-flex flex-column flex-lg-row flex-md-row align-items-start align-items-lg-center align-items-md-center flex-gap mbl-flex-wrap">
									<div className="radio-holder mr-2">
										<label className="custom-radio mb-0">{t('Immediately')}
											<input type="radio" name="sendcampaign" disabled value="1" checked={sendCampaign == 1 ? true : false} />
											<span className="checkmark"></span>
										</label>
									</div>
									<div className="radio-holder mr-2">
										<label className="custom-radio mb-0">{t('Schedule Once')}
											<input type="radio" name="sendcampaign" disabled value="2" checked={sendCampaign == 2 ? true : false} />
											<span className="checkmark"></span>
										</label>
									</div>
									<div className="radio-holder mr-2">
										<label className="custom-radio mb-0">{t('Recursively')}
											<input type="radio" name="sendcampaign" disabled value="3" checked={sendCampaign == 3 ? true : false} />
											<span className="checkmark"></span>
										</label>
									</div>
								</div>

								{/* schedule campaign for speicific day. campaign will send only single time  */}
								{
									sendCampaign == 2 ?
										<div className="calendar-holder pt-3 me-3">
											<DateTimePicker
												format="y-MM-dd"
												className="em-calendar"
												disabled
												value={selectedDate}
												minDate={new Date()}
											/>
										</div>
										: null
								}

								{/* Show to create campaign with recursive option */}
								{
									sendCampaign == 3 ?
										<Form.Group className="align-items-center mb-2 mb-md-4 d-flex flex-column flex-md-row form-group mbl-flex-wrap">
											<Form.Label className="mb-2 mb-md-0" htmlFor="reply-to-address">{t('Set the Recursion Cycle')}</Form.Label>
											<div className="flex-fill input-holder radio-btns-holder d-flex mbl-flex-wrap">
												<div className="radio-holder mr-2">
													<label className="custom-radio">
														{t('Weekly')}
														<input type="radio" name="campaignrecursivetype" disabled value="1" checked={recursiveCampaignType == 1 ? true : false} />
														<span className="checkmark"></span>
													</label>
												</div>
												<div className="radio-holder mr-2">
													<label className="custom-radio">
														{t('Monthly')}
														<input type="radio" name="campaignrecursivetype" disabled value="2" checked={recursiveCampaignType == 2 ? true : false} />
														<span className="checkmark"></span>
													</label>
												</div>
												<div className="radio-holder mr-2">
													<label className="custom-radio">
														{t('Yearly')}
														<input type="radio" name="campaignrecursivetype" disabled value="3" checked={recursiveCampaignType == 3 ? true : false} />
														<span className="checkmark"></span>
													</label>
												</div>
											</div>
										</Form.Group>
										: null
								}

								{/* Recursive Yearly- show list of Months */}
								{
									recursiveCampaignType == 3 ?
										<Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
											<Form.Label className="mb-2 mb-md-0">{t('Select Month')}</Form.Label>
											<div className="flex-fill input-holder">
												<div className="subscriber-select w-100">
													<Select
														disabled
														options={MonthsOfYear}
														value={MonthsOfYear.find(o => o.value == monthOfYear)}
														classNamePrefix="react-select"
														placeholder={t("Select Month")}
													/>
												</div>
											</div>
										</Form.Group>
										: null
								}

								{/* Recursive Monthly- show days no of Month */}
								{
									(recursiveCampaignType == 2 || recursiveCampaignType == 3) ?
										<Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
											<Form.Label className="mb-2 mb-md-0">{('Select Day of Month')}</Form.Label>
											<div className="flex-fill input-holder">
												<div className="subscriber-select w-100">
													<Select
														disabled
														options={daysOfMonth}
														value={daysOfMonth.find(o => o.value == dayOfMonth)}
														classNamePrefix="react-select"
														placeholder={t("Select Day")}
													/>
												</div>
											</div>
										</Form.Group>
										: null
								}

								{/* Recursive Weekly- show days of Week */}
								{
									(recursiveCampaignType == 1) ?
										<Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
											<Form.Label className="mb-2 mb-md-0">{t('Select Day of Week')}</Form.Label>
											<div className="flex-fill input-holder">
												<div className="subscriber-select w-100">
													<Select
														disabled
														options={daysOfWeek}
														value={daysOfWeek.find(o => o.value == dayOfWeek)}
														classNamePrefix="react-select"
														placeholder={t("Select Day")}
													/>
												</div>
											</div>
										</Form.Group>
										: null
								}

								{/* Recursive weekly / Monthly / Yearly - No of times to send */}
								{
									(recursiveCampaignType == 1 || recursiveCampaignType == 2 || recursiveCampaignType == 3) ?
										<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
											<Form.Label className="mb-2 mb-md-0">{t('No of Times')}</Form.Label>
											<div className="flex-fill input-holder">
												<input
													id="no-of-time"
													className="form-control"
													type="text"
													disabled
													value={times}
												/>
											</div>
										</Form.Group>
										: null
								}
							</div>
						</Form.Group>
						<div className="btns-holder right-btns d-flex flex-sm-row flex-column pt-5 buttons-setting-mobile justify-content-sm-end">
							<Link onClick={() => goBack()} className="btn btn-secondary ms-sm-3 mb-3"><span>{t('Back')}</span></Link>
						</div>
					</div>
				</Form>
			</Container>

			{/* // Contacts Modal */}
			<Modal show={show} onHide={handleClose} className="em-modal contact-modal smallmodal" centered>
				<div>
					<Modal.Body className="d-flex align-items-center justify-content-center flex-column em-table">
						<div className="mb-3 group-select-title w-100 text-center">
							<span className="static-title">{t('selected lists')} :</span>
							<div className="selected-groups mt-3">
								{selectedGroups.map(group =>
									<span className="chip" key={group.hash_id}> {group.label} </span>
								)}
							</div>
						</div>
						<div className="table-responsive w-100">
							<Table className="align-middle">
								<thead>
									<tr>
										<th>
											<label className="custom-checkbox no-text me-0 d-none">
												<input
													className="form-checkbox"
													type="checkbox"
													className="addContact"
												/>
												<span className="checkmark"></span>
											</label>
										</th>
										<th>{t('Contacts Name')}</th>
										<th>{t('Email')}</th>
										<th>{t('Mobile no.')}</th>
									</tr>
								</thead>
								<tbody>
									{contacts.length ?
										[...new Map(uniqueContacts.map(item => [item['hash_id'], item])).values()].map((contact, index) => (
											<tr key={contact.hash_id}>
												<td>
													<label className="custom-checkbox no-text me-0">
														<input
															className="form-checkbox"
															type="checkbox"
															disabled
															defaultChecked={excludedContacts.includes(contact.hash_id) ? true : false}
														/>
														<span className="checkmark"></span>
													</label>
												</td>
												<td className="text-capitalize">{contact.first_name}{" "}{contact.last_name}</td>
												<td>{contact.email}</td>
												<td>{contact.country_Code}{" "}{contact.number}</td>
											</tr>
										)) :
										<tr>
											<td className="text-center" colSpan="4">
												{t('No Contacts Found')}
											</td>
										</tr>
									}
								</tbody>
							</Table>
						</div>
					</Modal.Body>
					<Modal.Footer className="update-cancl-btn justify-content-center">
						<Button variant="secondary" onClick={handleClose}>
							<span>{t('Cancel')}</span>
						</Button>
					</Modal.Footer>
				</div>
			</Modal>
			{/* // Add Contacts Modal */}
			<Modal show={showAdd} onHide={handleCloseAdd} className="em-modal contact-modal smallmodal  new-contact-modal" centered>
				<div>
					<Modal.Body className="d-flex align-items-center justify-content-center flex-column em-table">
						<div className="mb-3 group-select-title w-100 text-center">
							<span className="static-title">{t('Contact(s) Added')} </span>
						</div>
						<div className="mb-3 group-select-title w-100 text-center">
							<Form className="em-form campaign-new-contact mb-3 mb-md-2" method="GET">
								<Row>
									<Col lg="12">
										<Form.Group className="d-flex flex-row align-items-center mbl-campaign-search">
											<Form.Label className="campaign-search-label">
												{t('Search Contact Name')}
											</Form.Label>
											<input
												type="text"
												className="form-control"
												value={filterName}
												onChange={(e) =>
													setfilterName(
														e.target.value
													)
												}
											/>
										</Form.Group>
									</Col>
								</Row>
							</Form>
						</div>
						<div className="table-responsive w-100">
							<Table className="align-middle">
								<thead>
									<tr>
										<th>
											<label className="custom-checkbox no-text me-0 d-none">
												<input
													className="form-checkbox"
													type="checkbox"
													className="addContact"
												/>
												<span className="checkmark"></span>
											</label>
										</th>
										{/* <th>Sr.</th> */}
										<th>{t('Contacts Name')}</th>
										<th>{t('Email')}</th>
										<th>{t('Mobile no.')}</th>
									</tr>
								</thead>
								<tbody>
									{contactsAdd.length ?
										contactsAdd.map((contact, index) => (
											<tr key={contact.hash_id}>
												<td>
													<label className="custom-checkbox no-text me-0">
														<input
															className="form-checkbox"
															type="checkbox"
															disabled
															defaultChecked={addedContacts.includes(contact.hash_id) ? true : false}
															id={"n" + contact.hash_id}
														/>
														<span className="checkmark"></span>
													</label>
												</td>
												{/* <td>{(pageNumber - 1) * perPage + index + 1}</td> */}
												<td className="text-capitalize">{contact.first_name}{" "}{contact.last_name}</td>
												<td>{contact.email}</td>
												<td>{contact.country_Code}{" "}{contact.number}</td>
											</tr>
										)) :
										<tr>
											<td className="text-center" colSpan="4">
												{t('No Contacts Found')}
											</td>
										</tr>
									}
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

					</Modal.Body>
					<Modal.Footer className="justify-content-center">
						<Button variant="secondary" onClick={handleCloseAdd}>
							<span>{t('Close')}</span>
						</Button>
					</Modal.Footer>
				</div>
			</Modal>
		</React.Fragment>
	);
}

export default withTranslation()(ViewCampaign);