import React, { useEffect, useState } from 'react';
import { Container, Form, Row, Col, Button, Modal, Table } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faInfoCircle } from "@fortawesome/free-solid-svg-icons"
import Spinner from '../includes/spinner/Spinner';
import Select from 'react-select';
import DateTimePicker from 'react-datetime-picker';
import { daysOfWeek, daysOfMonth, MonthsOfYear } from '../../constants';
import RangeSlider from 'react-bootstrap-range-slider';
import './assets/CreateSplitTesting.css';
import 'react-bootstrap-range-slider/dist/react-bootstrap-range-slider.css';
import Multiselect from 'multiselect-react-dropdown';
import Pagination from "react-js-pagination";
import { withTranslation } from 'react-i18next';
import { Link } from 'react-router-dom';
var addedContacts = [];
var excludedContacts = [];

//contacts group option
var options = [];
var templateOptions = [];
var loading_count = 0;
function ViewCampaign(props) {
	const { t } = props;
	const [templateOptionsState, setTemplateOptionsState] = useState([]);
	const [name, setName] = useState('');
	const [subject, setSubject] = useState('');
	const [paramDisabled, setParamDisabled] = useState('');
	const [splitTestParam, setSplitTestParam] = useState('');

	const [subjectPlanA, setSubjectPlanA] = useState('');
	const [subjectPlanB, setSubjectPlanB] = useState('');
	const [contentPlanA, setContentPlanA] = useState('');
	const [contentPlanB, setContentPlanB] = useState('');
	const [sizeOfGroup, setSizeOfGroup] = useState(50);

	const [sender_name, setSenderName] = useState('');
	const [sender_email, setSenderEmail] = useState('');
	const [reply_to_email, setReplyToEmail] = useState('');
	const [recursiveCampaignType, setRecursiveCampaignType] = useState('');
	const [dayOfWeek, setDayOfWeek] = useState('');
	const [dayOfMonth, setDayOfMonth] = useState('');
	const [monthOfYear, setMonthOfYear] = useState('');
	const [sendingCampaignType, setSendingCampaignType] = useState(1);
	const [selectedTemplate, setSelectedTemplate] = useState({});
	const [selectedDate, setSelectedDate] = useState('')
	const [loading, setLoading] = useState('');
	const [excludesNum, setExcludesNum] = useState(0);
	const [contactsNum, setContactsNum] = useState(0);
	const [groups, setGroups] = useState([]);
	const [contacts, setContacts] = useState([]);
	const [uniqueContacts, setUniqueContacts] = useState([]);
	const [times, setTimes] = useState('');
	const [selectedGroups, setSelectedGroups] = useState([])

	const [templateImg, setTemplateImg] = useState('')
	const [templateImgA, setTemplateImgA] = useState('')
	const [templateImgB, setTemplateImgB] = useState('')

	const getCampaign = (id) => {
		setLoading(true);
		axios.get(`/api/campaign/` + id + `/edit` + '?lang=' + localStorage.lang, {
			params: {
				_id: id
			}
		})
			.then((res) => {
				setName(res.data.data.name);
				setSubject(res.data.data.subject);
				setSplitTestParam(res.data.data.split_test_param);
				setSenderName(res.data.data.sender_name);
				setSenderEmail(res.data.data.sender_email);
				setReplyToEmail(res.data.data.reply_to_email);

				setParamDisabled(res.data.data.split_test_param);
				setSubjectPlanA(res.data.data.split_subject_1);
				setSubjectPlanB(res.data.data.split_subject_2);
				setSizeOfGroup(res.data.data.size_of_group);
				setTimes(res.data.data.no_of_time);

				setSendingCampaignType(res.data.data.campaign_type);
				setRecursiveCampaignType(res.data.data.recursive_campaign_type);
				setDayOfWeek(res.data.data.day_of_week);
				setDayOfMonth(res.data.data.day_of_month);
				setMonthOfYear(res.data.data.month_of_year);
				setExcludesNum(res.data.data.excludes.length);
				var c2 = res.data.data.excludes.map(cont => cont.hash_id);
				excludedContacts = c2;
				setContactsNum(res.data.data.contacts.length);
				var c = res.data.data.contacts.map(cont => cont.hash_id);
				addedContacts = c;
				setSelectedDate(new Date(res.data.data.schedule_date));
				setContentPlanA(res.data.data.split_content_1);
				setContentPlanB(res.data.data.split_content_2);
				loading_count++;
				if (loading_count >= 2)
					setLoading(false);
			})
			.catch((error) => {
				loading_count++;
				if (loading_count >= 2)
					setLoading(false);
			});
	};

	const getCampaign2 = (id) => {
		setLoading(true);
		axios.get(`/api/campaign/` + id + `/edit` + '?lang=' + localStorage.lang, {
			params: {
				_id: id
			}
		})
			.then((res) => {
				setSelectedTemplate(templateOptions.find(o => o.value == res.data.data.template_id));
				setTemplateImg(templateOptions.find(o => o.value == res.data.data.template_id).img)
				setTemplateImgA(templateOptions.find(o => o.value == res.data.data.split_content_1).img)
				setTemplateImgB(templateOptions.find(o => o.value == res.data.data.split_content_2).img)
				var selected_groups = []
				res.data.data.group_ids.map(val => selected_groups.push(options.find(o => o.value == val)))
				setSelectedGroups([...selected_groups])
				var tempcontacts = [];
				res.data.data.group_ids.map(id => {
					var selectedOpt = options.find(o => o.value == id)
					if (selectedOpt.value != "")
						var arr1 = [...tempcontacts];
					var arr2 = groups[selectedOpt.index].contacts;
					tempcontacts = arr1.concat(arr2);
				})
				setContacts([...tempcontacts].filter(contact => contact.for_email == 1))
				setUniqueContacts([...tempcontacts].filter(contact => contact.for_email == 1))
				const grp = options.find(o => o.value == res.data.data.group_id);
				loading_count++;
				if (loading_count >= 2)
					setLoading(false);
			})
			.catch((error) => {
				loading_count++;
				if (loading_count >= 2)
					setLoading(false);
			});
	};



	// add contacts modal
	const [contactsAdd, setContactsAdd] = useState([]);
	const [adding, setAdding] = useState(false);
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
				"&type=" + 2
			)
			.then((response) => {
				if (!$.trim(response.data.data) && pageNumber !== 1) {
					setPageNumber(pageNumber - 1);
				}
				setContactsAdd(response.data.data);
				setperPage(response.data.meta.per_page);
				setTotalItems(response.data.meta.total);
				loading_count++;
				if (loading_count >= 2)
					setLoading(false);
				// // console.log(contacts);
			})
			.catch((error) => {
				if (error.response.data.errors) {
					loading_count++;
					if (loading_count >= 2)
						setLoading(false);
				}
			});
	};

	const goBack = () => {
		let params = new URLSearchParams(location.search);
		if (params.get('page')) {
			window.location.href = "/split-testing/list?page=" + params.get('page');
		}
		else {
			window.location.href = "/split-testing/list";
		}
	}

	const getContactGroup = () => {
		setLoading(true);
		axios.get(`/api/contact/group` + '?lang=' + localStorage.lang)
			.then((res) => {
				loading_count++;
				if (loading_count >= 2)
					setLoading(false);
				setGroups(res.data.data.groups);

				options = []
				res.data.data.groups.map((group, index) => {
					options.push({ value: group.hash_id, label: group.name, index: index })
				});

				templateOptions = []
				res.data.data.templates.map((template) => {
					templateOptions.push({ value: template.id, label: template.name, img: template.image })
				});
				setTemplateOptionsState(templateOptions)
			})
			.catch((error) => {
				loading_count++;
				if (loading_count >= 2)
					setLoading(false);
			});
	};

	useEffect(() => {
		loading_count = 0;
		getContactGroup();
	}, []);

	useEffect(() => {
		let parseUriSegment = window.location.pathname.split("/");
		if (parseUriSegment.indexOf('split-testing') && parseUriSegment.indexOf('view') != -1) {
			getCampaign(parseUriSegment[3]);
		}
	}, []);
	useEffect(() => {
		if (groups.length) {
			let parseUriSegment = window.location.pathname.split("/");
			if (parseUriSegment.indexOf('split-testing') && parseUriSegment.indexOf('view') != -1) {
				if (groups.length)
					getCampaign2(parseUriSegment[3]);
			}
		}
	}, [groups]);

	// for contacts
	useEffect(() => {
		getContacts()
	}, [pageNumber, filterName]);

	// modal
	const [show, setShow] = useState(false);
	const handleClose = () => {
		setShow(false);
	};
	const handleShow = (e) => {
		e.preventDefault();
		setShow(true);
	};

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>{t('view_split_campaign')}</h1>
					</div>
				</div>
				<Form className="create-form-holder rounded-box-shadow bg-white">

					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-lg-row">
						<Form.Label className="mb-2 mb-md-0" htmlFor="campaign-name">{t('Campaign Name')} <b className="req-sign">*</b></Form.Label>
						<div className="flex-fill input-holder">
							<input id="campaign-name" className="form-control" type="text" disabled value={name} placeholder={t('eg Campaign name')} />
						</div>
					</Form.Group>

					<Form.Group className="mb-3 mb-md-4 d-flex flex-lg-row flex-column">
						<Form.Label className="mb-lg-0 mb-2" htmlFor="sender-name">{t('Sender Name')} <b className="req-sign">*</b></Form.Label>
						<div className="flex-fill input-holder">
							<input id="sender-name" className="form-control" type="text" disabled value={sender_name} placeholder="e.g. Sam Smith" />
						</div>
					</Form.Group>

					<Form.Group className="mb-3 mb-md-4 d-flex flex-lg-row flex-column">
						<Form.Label className="mb-lg-0 mb-2" htmlFor="sender-email">{t('Sender Email Address')} <b className="req-sign">*</b></Form.Label>
						<div className="flex-fill input-holder">
							<input id="sender-email" className="form-control" type="text" disabled value={sender_email} placeholder="e.g. exampale@email.com" />
						</div>
					</Form.Group>

					<Form.Group className="mb-3 mb-md-4 d-flex  flex-lg-row flex-column subject-wrapper">
						<Form.Label className="mb-lg-0 mb-2" htmlFor="reply-to-address">{t('Split Test Parameters')} <b className="req-sign">*</b></Form.Label>
						<div className="flex-fill input-holder radio-btns-holder d-flex">
							<div className="checkbox-holder mr-2 ms-4 d-flex flex-row align-items-center flex-gap">
								{/* <span className="custom-checkbox____"> */}
								<input type="checkbox" className="form-checkbox" name="subject-line" type="checkbox" className="form-checkbox mr-2" disabled checked={splitTestParam == 1 ? true : false} />
								<label className="custom-control-label" htmlFor="Subject Line">{t('Subject Line')}</label>
								{/* </span> */}
							</div>
							<div className="checkbox-holder mr-2 ms-4 d-flex flex-row align-items-center flex-gap">
								{/* <span className="custom-checkbox"> */}
								<input type="checkbox" className="form-checkbox" name="email-content" type="checkbox" value={paramDisabled} className="form-checkbox mr-2" disabled checked={splitTestParam == 2 ? true : false} />
								<label className="custom-control-label" htmlFor="Email Content">{t('Email Content')}</label>
								{/* </span> */}
							</div>
						</div>
					</Form.Group>


					{
						paramDisabled == 1 ?
							<>
								<Form.Group className="mb-3 mb-md-4 d-flex">
									<Form.Label htmlFor="reply-to-email">{t('Subject Plan A')} <b className="req-sign">*</b></Form.Label>
									<div className="flex-fill input-holder">
										<input id="reply-to-email" className="form-control" type="text" disabled value={subjectPlanA} placeholder={t("eg Email Subject one")} />
									</div>
								</Form.Group>
								<Form.Group className="mb-3 mb-md-4 d-flex">
									<Form.Label htmlFor="reply-to-email">{t('Subject Plan B')} <b className="req-sign">*</b></Form.Label>
									<div className="flex-fill input-holder">
										<input id="reply-to-email" className="form-control" type="text" disabled value={subjectPlanB} placeholder={t("eg Email Subject two")} />
									</div>
								</Form.Group>
							</>
							: null
					}

					{
						paramDisabled == 2 ?
							<>

								<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
									<Form.Label className="mb-2 mb-md-0" htmlFor="subject-line">{t('Subject Line')} <b className="req-sign">*</b></Form.Label>
									<div className="flex-fill input-holder">
										<input id="subject-line" className="form-control" type="text" disabled value={subject} placeholder={t("eg Email Subject")} />
									</div>
								</Form.Group>

								<Form.Group className="mb-3 mb-md-4 d-flex">
									<Form.Label className="mb-2 mb-md-0">{t('Content Plan A')} <b className="req-sign">*</b></Form.Label>
									<div className="flex-fill input-holder">
										<div className="subscriber-select w-100">
											{templateOptionsState.length == 0 ?
												""
												:
												<Select
													disabled
													options={templateOptionsState}
													classNamePrefix="react-select"
													value={templateOptionsState.find(o => o.value == contentPlanA)}
													placeholder={t("Select Template one")}
												/>
											}
										</div>
									</div>
									{templateImgA ? <div id="template_image"> <img src={templateImgA} /> </div> : ""}
								</Form.Group>


								<Form.Group className="mb-3 mb-md-4 d-flex">
									<Form.Label className="mb-2 mb-md-0">{t('Content Plan B')} <b className="req-sign">*</b></Form.Label>
									<div className="flex-fill input-holder">
										<div className="subscriber-select w-100">
											{templateOptionsState.length == 0 ?
												""
												:
												<Select
													disabled
													options={templateOptionsState}
													classNamePrefix="react-select"
													value={templateOptionsState.find(o => o.value == contentPlanB)}
													placeholder={t("select_template_two")}
												/>
											}
										</div>
									</div>
									{templateImgB ? <div id="template_image"> <img src={templateImgB} /> </div> : ""}
								</Form.Group>
							</>
							: null
					}


					<Form.Group className="mb-3 mb-md-4 d-flex align-items-center flex-lg-row flex-column">
						<Form.Label className="mbl-label-w" htmlFor="reply-to-address">{t('Select Sizes of Test Group')} <b className="req-sign">*</b></Form.Label>
						<div className="flex-fill input-holder">

							<RangeSlider
								value={sizeOfGroup}
								tooltip="on"
								variant="dark"
								min="10"
								max="80"
								tooltipLabel={currentValue => `${currentValue}%`}
								disabled
							/>
						</div>
					</Form.Group>

					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label htmlFor="reply-to-email">{t('Reply to Address')}</Form.Label>
						<div className="flex-fill input-holder">
							<input id="reply-to-email" className="form-control" type="text" disabled value={reply_to_email} placeholder="e.g. example@email.com" />
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
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label className="mb-2 mb-md-0">{t('Contacts')}</Form.Label>
						<div className="mt-lg-0 mt-0  d-flex flex-column input-holder flex-sm-row align-items-center justify-content-between mbl-flex-wrap">
							{/* <h3>OR</h3> */}
							<button type="submit" onClick={handleShowAdd} className="btn btn-secondary ml-lg-3 ml-0 mt-0 mr-3 mb-0">
								<span>{t('Contacts')}</span>
							</button>
							<p className="mb-0 ms-2">{contactsNum} {t('Contact(s) Added')} </p>
						</div>
					</Form.Group>
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row form-group">
						<Form.Label className="mb-2 mb-md-0">{t('Contact Lists')}</Form.Label>
						<div className="d-flex flex-lg-row flex-column flex-xl-nowrap flex-wrap align-items-start justify-content-between input-holder">
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
									: ""
								}
							</div>
						</div>
					</Form.Group>

					{
						splitTestParam == 1 ?

							<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
								<Form.Label className="mb-2 mb-md-0">{t('Select Template')} <b className="req-sign">*</b></Form.Label>
								<div className="flex-fill input-holder">
									<div className="subscriber-select w-100">
										{templateOptionsState.length == 0 ?
											""
											:
											<Select
												disabled
												options={templateOptionsState}
												value={selectedTemplate}
												classNamePrefix="react-select"
												placeholder={t("Select Template")}
											/>
										}
										{templateImg ? <div id="template_image"> <img src={templateImg} /> </div> : ""}
									</div>
								</div>
							</Form.Group>
							: null
					}

					{/* Email campaign schedule options  */}
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label className="mb-2 mb-md-0" htmlFor="reply-to-address">{t('Send Campaign')} <b className="req-sign">*</b></Form.Label>
						<div className="flex-fill input-holder radio-btns-holder d-flex flex-column pt-0">
							<div className="d-flex flex-column flex-lg-row flex-md-row align-items-start align-items-lg-center align-items-md-center flex-gap mbl-flex-wrap">
								<div className="radio-holder mr-2">
									<label className="custom-radio mb-0">{t('Immediately')}
										<input type="radio" name="sendcampaign" disabled value="1" checked={sendingCampaignType == 1 ? true : false} />
										<span className="checkmark"></span>
									</label>
								</div>
								<div className="radio-holder mr-2">
									<label className="custom-radio mb-0">{t('Schedule Once')}
										<input type="radio" name="sendcampaign" disabled value="2" checked={sendingCampaignType == 2 ? true : false} />
										<span className="checkmark"></span>
									</label>
								</div>
								<div className="radio-holder mr-2">
									<label className="custom-radio mb-0">{t('Recursively')}
										<input type="radio" name="sendcampaign" disabled value="3" checked={sendingCampaignType == 3 ? true : false} />
										<span className="checkmark"></span>
									</label>
								</div>
							</div>

							{/* schedule campaign for speicific day. campaign will send only single time  */}
							{
								sendingCampaignType == 2 ?
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
								sendingCampaignType == 3 ?
									<Form.Group className="align-items-center mb-2 mb-md-4 d-flex flex-column flex-md-row mbl-flex-wrap">
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
													classNamePrefix="react-select"
													value={MonthsOfYear.find(o => o.value == parseInt(monthOfYear))}
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
										<Form.Label className="mb-2 mb-md-0">{t('Select Day of Month')}</Form.Label>
										<div className="flex-fill input-holder">
											<div className="subscriber-select w-100">
												<Select
													disabled
													options={daysOfMonth}
													classNamePrefix="react-select"
													value={daysOfMonth.find(o => o.value == parseInt(dayOfMonth))}
													placeholder={t("Select Day")}
												/>
											</div>
										</div>
									</Form.Group>
									: null
							}

							{/* Recursive Weekly- show days of Week */}
							{
								recursiveCampaignType == 1 ?
									<Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
										<Form.Label className="mb-2 mb-md-0">{t('Select Day of Week')}</Form.Label>
										<div className="flex-fill input-holder">
											<div className="subscriber-select w-100">
												<Select
													disabled
													options={daysOfWeek}
													classNamePrefix="react-select"
													value={daysOfWeek.find(o => o.value == parseInt(dayOfWeek))}
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
										<Form.Label className="mb-2 mb-md-0">{t('No of Time')}</Form.Label>
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
				</Form>
			</Container>
			{/* // Contacts Modal */}
			<Modal show={show} onHide={handleClose} className="em-modal contact-modal" centered>
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
											<label className="custom-checkbox no-text me-0  d-none">
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
										</tr>}
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
			<Modal show={showAdd} onHide={handleCloseAdd} className="em-modal contact-modal  smallmodal new-contact-modal" centered>
				<div>
					<Modal.Body className="d-flex align-items-center justify-content-center flex-column em-table">
						<div className="mb-3 group-select-title w-100 text-center">
							<span className="static-title">{t('Contact(s) Added')} </span>
						</div>
						<div className="group-select-title w-100 text-center">
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