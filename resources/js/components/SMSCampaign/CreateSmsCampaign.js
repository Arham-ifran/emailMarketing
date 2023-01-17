import React, { useState, useEffect } from 'react';
import { Container, Table, Form, Row, Col, Modal, Button } from 'react-bootstrap';
import { Link, useParams, useHistory } from "react-router-dom";
import Select from 'react-select';
import './css/EditSmsCampaign.css';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faInfoCircle } from "@fortawesome/free-solid-svg-icons"
import DateTimePicker from 'react-datetime-picker';
import Spinner from "../includes/spinner/Spinner";
import Swal from 'sweetalert2';
import { daysOfWeek, daysOfMonth, MonthsOfYear } from '../../constants';
import PhoneInput from 'react-phone-number-input'
import AddNumbersInput from './AddNumbersInput';
import Multiselect from 'multiselect-react-dropdown';
import Pagination from "react-js-pagination";
import GetUserPackage from "../Auth/GetUserPackage.js";
import { withTranslation } from 'react-i18next';
import moment from "moment-timezone";

var addedContacts = [];
var removedContacts = [];
var excludedContacts = [];
var excludedRemovedContacts = [];
var totl_contact_count = 0;
var totl_excludes_count = 0;
var smsTemplates = [];
var loading_count = 0;
function EditSmsCampaign(props) {
	const { t } = props;
	const history = useHistory();
	const [campaignId, setCampaignId] = useState('');
	const [recursiveCampaignType, setRecursiveCampaignType] = useState('');
	const [dayOfWeek, setDayOfWeek] = useState('');
	const [dayOfMonth, setDayOfMonth] = useState('');
	const [monthOfYear, setMonthOfYear] = useState('');
	const [sendCampaign, setSendCampaign] = useState(1);
	const [selectedDate, setSelectedDate] = useState(new Date())
	const [options, setOptions] = useState([])
	const [templateOptions, setTemplateOptions] = useState([])
	const [name, setName] = useState("");
	const [contactsNum, setContactsNum] = useState(0);
	const [excludesNum, setExcludesNum] = useState(0);
	const [message, setMessage] = useState("");
	const [sender_name, setSendername] = useState("");
	const [sender_number, setSendernum] = useState("");
	const [times, setTimes] = useState("");
	const [toGroup, setToGroup] = useState("");
	const [status, setStatus] = useState("");
	const [groups, setGroups] = useState([]);
	const [contacts, setContacts] = useState([]);
	const [uniqueContacts, setUniqueContacts] = useState([]);
	const [campaignSaveAs, setCampaignSaveAs] = useState(1)
	const [toContacts, SetToContacts] = useState(false)
	const [toTemplates, SetToTemplates] = useState(false)
	const [toLists, SetToLists] = useState(false)
	const statusOptions = [{ value: 1, label: t("Active") }, { value: 2, label: t("Draft") }];
	const [selectedStatus, setSelectedStatus] = useState();
	const [testing, setTesting] = useState(false);
	const [numbersList, setNumbersList] = useState([]);
	const [group_ids, setGroup_ids] = useState([])
	const [selectedGroups, setSelectedGroups] = useState([])
	const [hideStatus, setHideStatus] = useState(0)

	const [userPackage, setUserPackage] = useState({});
	const [canSchedule, setCanSchedule] = useState(0);
	const [canRecurr, setCanRecurr] = useState(0);

	useEffect(() => {
		loading_count = 0;
		getGroups();
		getSmsTemplates()
		let parseUriSegment = window.location.pathname.split("/");
		if (parseUriSegment.indexOf('sms-campaign') && parseUriSegment.indexOf('edit') != -1) {
			setCampaignId(parseUriSegment[2]);
			getCampaign(parseUriSegment[2]);
		}
	}, []);

	useEffect(() => {
		const load = () => {
			if (userPackage != {}) {
				if (userPackage.features) {
					if (Object.keys(userPackage.features).findIndex(val => val === "10") >= 0) { // schedule allowed
						setCanSchedule(true)
					} else {
						setCanSchedule(false)
					}
					if (Object.keys(userPackage.features).findIndex(val => val === "11") >= 0) { // recursive allowed
						setCanRecurr(true)
					} else {
						setCanRecurr(false)
					}
				}
			}
		}
		load();
	}, [userPackage])

	useEffect(() => {
		let parseUriSegment = window.location.pathname.split("/");
		if (parseUriSegment.indexOf('sms-campaign') && parseUriSegment.indexOf('edit') != -1) {
			setCampaignId(parseUriSegment[2]);
			if (groups.length)
				getCampaign2(parseUriSegment[2]);
		}
	}, [groups]);

	// modal
	const [show, setShow] = useState(false);
	const handleClose = () => {
		setShow(false);
	};
	const handleShow = (e) => {
		e.preventDefault();
		setShow(true);
	};

	const handleStatusChange = (selectedOption) => {
		setSelectedStatus(selectedOption);
	}

	//set day of month selected value
	const handleMonthOfYear = (selectedOption) => {
		setMonthOfYear(selectedOption.value);
	}

	//set day of month selected value
	const handleDayOfMonthChange = (selectedOption) => {
		setDayOfMonth(selectedOption.value);
	}

	//set day of week selected value
	const handleDayOfWeekChange = (selectedOption) => {
		setDayOfWeek(selectedOption.value);
	}

	//set send campaign at date value
	const handleCalenderChange = (date) => {
		if (!(moment(moment(date).format('YYYY-MM-DD'), 'YYYY-MM-DD', true).isValid())) {
			setSelectedDate(new Date());
		}
		else {
			setSelectedDate(date)
		}
	}

	// set send to mailing list
	const handleChange = (selectedOption) => {
		setToGroup(selectedOption.value)
		clearExcludes();
		setContacts(groups[selectedOption.index].contacts.filter(contact => contact.for_sms == 1))
		setUniqueContacts(groups[selectedOption.index].contacts.filter(contact => contact.for_sms == 1))
	}

	const handleTestingInput = () => {
		setTesting(!testing)
	}

	// For Loading and error messages
	const [loading, setLoading] = useState(false);
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


	// reset to immidiately
	const SendCampaign = (e) => {
		if (e.target.value != 3) {
			setRecursiveCampaignType('');
			setDayOfWeek('');
			setDayOfMonth('');
			setMonthOfYear('');
			setSelectedDate(new Date());
		}
		setSendCampaign(e.target.value);
	};

	// Get Mailing Lists
	const getGroups = () => {
		setLoading(true);
		axios.get("/api/get-all-groups?lang=" + localStorage.lang)
			.then((response) => {
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
				const data = [];
				const moredata = [
					response.data.data.filter(e => e.contacts.length > 0).map((row, index) => ({
						value: row.hash_id,
						index: index,
						label: row.name,
					})),
				];
				const opt = data.concat(moredata[0]);
				setOptions(opt);
				setGroups(response.data.data.filter(e => e.contacts.length > 0));
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
			});
	}

	// Get Mailing Lists
	const getSmsTemplates = () => {
		setLoading(true);
		axios.get("/api/get-sms-templates?lang=" + localStorage.lang)
			.then((response) => {
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
				const data = response.data.data.data.map(row => ({
					value: row.message,
					label: row.name,
				}));
				// console.log(data);
				setTemplateOptions(data);
				// smsTemplates = response.data.data.data;
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
			});
	}

	const getCampaign = (id) => {
		setLoading(true);

		axios.get("/api/get-sms-campaign/" + id + "?lang=" + localStorage.lang)
			.then((response) => {
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
				const received_data = response.data.data[0]
				setName(received_data.name);
				setMessage(received_data.message);
				setSendernum(received_data.sender_number);
				setSendername(received_data.sender_name);
				setSendCampaign(received_data.type);

				setToGroup(received_data.group_id);
				if (received_data.status == "Draft") {
					setSelectedStatus(statusOptions[1])
					setHideStatus(0);
				} else {
					setSelectedStatus(statusOptions[0])
					setHideStatus(1);
				}
				setGroup_ids(received_data.group_ids);
				setStatus(received_data.status);
				setContactsNum(received_data.contacts.length);
				var c = received_data.contacts.map(cont => cont.hash_id);
				addedContacts = c;
				setExcludesNum(received_data.excludes.length);
				var c2 = received_data.excludes.map(cont => cont.hash_id);
				excludedContacts = c2;
				setSelectedStatus(statusOptions.find(o => o.label == received_data.status))

				if (received_data.type == 3) {
					setRecursiveCampaignType(received_data.recursive_campaign_type);
					setDayOfWeek(received_data.day_of_week);
					setDayOfMonth(received_data.day_of_month);
					setMonthOfYear(received_data.month_of_year);
					setTimes(received_data.no_of_time);
				}
				if (received_data.type == 2) {
					var date = received_data.schedule_date;
					setSelectedDate(new Date(date))
				}

			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
			});
	};

	const getCampaign2 = (id) => {
		setLoading(true);
		axios.get("/api/get-sms-campaign/" + id + "?lang=" + localStorage.lang)
			.then((response) => {
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
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
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
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
		axios.get("/api/get-contacts" + "?page=" + pageNumber + "&name=" + filterName + "&lang=" + localStorage.lang + "&type=" + 1)
			.then((response) => {
				if (!$.trim(response.data.data) && pageNumber !== 1) {
					setPageNumber(pageNumber - 1);
				}
				setContactsAdd(response.data.data);
				setperPage(response.data.meta.per_page);
				setTotalItems(response.data.meta.total);
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
				// // console.log(contacts);
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
					loading_count++;
					if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
						setLoading(false);
				}
			});
	};

	useEffect(() => {
		getContacts()
	}, [pageNumber, filterName]);

	const handleContactAdd = async (id) => {
		totl_contact_count = contactsNum;
		if (addedContacts.length) {
			await axios.post("/api/add-contact-to-campaignincludes?lang=" + localStorage.lang, { contact: addedContacts, campaign_id: id, type: 1 })
				.then(async (response) => {
					totl_contact_count = (response.data.total > 0) ?
						parseInt(totl_contact_count) + parseInt(response.data.total)
						: totl_contact_count;
					setContactsNum(totl_contact_count);
					await handleContactRemove(id);
				})
				.catch((error) => {
					console.log("err");
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
				});
		}
		else {
			await handleContactRemove(id);
		}
	}

	const handleContactRemove = async (id) => {

		if (removedContacts.length) {
			await axios.post("/api/remove-contact-from-campaignincludes?lang=" + localStorage.lang, { contact: removedContacts, campaign_id: id, type: 1 })
				.then((response) => {
					totl_contact_count = parseInt(totl_contact_count) - parseInt(response.data.total);
					console.log("765");
					setContactsNum(totl_contact_count);
					console.log("6453");
				})
				.catch((error) => {
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
				});
		}
	}

	const handleContactExclude = async (id) => {
		totl_excludes_count = excludesNum;
		if (excludedContacts.length) {
			await axios.post("/api/add-contact-to-campaignexcludes?lang=" + localStorage.lang, { contact: excludedContacts, campaign: id, type: 1 })
				.then(async (response) => {
					totl_excludes_count = (response.data.total > 0) ?
						parseInt(totl_excludes_count) + parseInt(response.data.total)
						: totl_excludes_count;
					setExcludesNum(totl_excludes_count);
					await handleContactExcludeUndo(id);
				})
		}
		else {
			await handleContactExcludeUndo(id);
		}
	}

	const handleAddContactCheckbox = (contact_id) => {
		if (addedContacts.includes(contact_id)) {
			// add contact to list
			removedContacts.push(contact_id)
			const index = addedContacts.indexOf(contact_id);
			if (index > -1) {
				addedContacts.splice(index, 1);
			}
		} else {
			// remove contact from list
			addedContacts.push(contact_id);
			const index = removedContacts.indexOf(contact_id);
			if (index > -1) {
				removedContacts.splice(index, 1);
			}
		}
	}

	const handleAddAllContactsCheckbox = (box) => {
		if (box.checked) {
			contactsAdd.map(cont => {
				addedContacts.push(cont.hash_id);
				const index = removedContacts.indexOf(cont.hash_id);
				if (index > -1) {
					removedContacts.splice(index, 1);
				}
				document.getElementById('n' + cont.hash_id).checked = 1;
			})
		} else {
			contactsAdd.map(cont => {
				document.getElementById('n' + cont.hash_id).checked = 0;
				removedContacts.push(cont.hash_id)
				const index = addedContacts.indexOf(cont.hash_id);
				if (index > -1) {
					addedContacts.splice(index, 1);
				}
			})
		}
	}

	const handleExcludeCheckbox = (box, contact_id) => {
		if (box.checked) {
			excludedContacts.push(contact_id);
			const index = excludedRemovedContacts.indexOf(contact_id);
			if (index > -1) {
				excludedRemovedContacts.splice(index, 1);
			}
		}
		else {
			excludedRemovedContacts.push(contact_id)
			const index = excludedContacts.indexOf(contact_id);
			if (index > -1) {
				excludedContacts.splice(index, 1);
			}
		}
		// console.log(excludedContacts);
	}


	const getSendingData = (num, camp_id = '') => {
		const data = {
			name: name,
			message: message,
			sender_name: sender_name,
			sender_number: sender_number,
			type: sendCampaign,
			recursive_campaign_type: recursiveCampaignType,
			day_of_week: dayOfWeek,
			day_of_month: dayOfMonth,
			month_of_year: monthOfYear,
			schedule_date: selectedDate ? (moment(selectedDate).format('YYYY-MM-DD') != '' ? moment.tz(moment(selectedDate).format('YYYY-MM-DD') + ' 12:00', localStorage.timezone).utc().format('YYYY-MM-DD') : null) : null,
			no_of_time: times,
			group_id: toGroup,
			group_ids: group_ids,
			campaign_id: camp_id,
			campaign_status: num == 1 ? 2 : 1,
			campaign_testing: testing,
			numbers_list: numbersList,
		};
		return data
	}

	const goBack = () => {
		let params = new URLSearchParams(location.search);
		if (params.get('page')) {
			window.location.href = "/sms-campaign?page=" + params.get('page');
		}
		else {
			window.location.href = "/sms-campaign";
		}
	}

	const handleUpdate = (event) => {
		event.preventDefault();
		setLoading(true);
		const data = getSendingData(1, campaignId);

		if (name == "") {
			setErrors({
				name: [name == "" ? t('atleast_one_required') : ''],
				message: [message == "" ? t('atleast_one_required') : ''],
				sender_name: [sender_name == "" ? t('atleast_one_required') : ''],
				sender_number: [sender_number == "" ? t('atleast_one_required') : ''],
			});
			loading_count++;
			if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
				setLoading(false);
			SetToContacts(false);
			SetToTemplates(false);
			return;
		}

		setErrors([]);
		axios.post("/api/edit-sms-campaign?lang=" + localStorage.lang, data)
			.then(async (res) => {
				if (testing) {
					Swal.fire({
						title: t('Success'),
						text: res.data.message,
						icon: 'success',
						showCancelButton: false,
						confirmButtonText: t('OK'),
					})
				} else {
					const id = res.data.data.hash_id;
					if (toContacts) {
						history.push("/contacts/add-multiple?sms=" + id);
					} else if (toTemplates) {
						history.push("/sms-template/create?sms=" + id);
					} else if (toLists) {
						history.push("/mailing-lists/create?sms=" + id);
					} else {
						await handleContactAdd(id);
						await handleContactExclude(id);
						if (campaignSaveAs == 1 && id) {
							const data2 = getSendingData(2, id);
							await axios.post("/api/edit-sms-campaign?lang=" + localStorage.lang, data2)
								.then(() => {
									Swal.fire({
										title: t('Success'),
										text: campaignId && hideStatus ? t('Your campaign has been sent successfully!') : t('Your campaign has been updated successfully!'),
										icon: 'success',
										showCancelButton: false,
										confirmButtonText: t('OK'),
									}).then((result) => {
										goBack();
									});
								})
								.catch((error) => {
									if (error.response.data.errors) {
										setErrors(error.response.data.errors);
									}
									if (error.response.data.code) {
										Swal.fire({
											title: t('Please Upgrade Your Package to Send Campaign to more Contacts'),
											text: error.response.data.message,
											icon: 'warning',
											showCancelButton: false,
											confirmButtonText: t('OK'),
										})
									}
								});
						}
						else {
							Swal.fire({
								title: t('Success'),
								text: t('your_campaign_has_been_saved_as_draft'),
								icon: 'success',
								showCancelButton: false,
								confirmButtonText: t('OK'),
							}).then((result) => {
								goBack();
							});
						}
					}
				}
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
			})
			.catch((error) => {
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				if (error.response.data.code) {
					Swal.fire({
						title: t('Please Upgrade Your Package to Send Campaign to more Contacts'),
						text: error.response.data.message,
						icon: 'warning',
						showCancelButton: false,
						confirmButtonText: t('OK'),
					})
				}
			});
		SetToContacts(false);
	};

	// save as draft and goto contacts
	const goToContacts = () => {
		SetToContacts(true)
		setTesting(false);
		return true;
	}

	const gotoCreateTemplate = (e) => {
		SetToTemplates(true);
		setTesting(false);
		return true;
	}

	const gotoCreateMailingList = (e) => {
		SetToLists(true);
		setTesting(false);
		return true;
	}

	const clearExcludes = () => {
		// clears all excludes in the campaign
		setLoading(true);
		setContacts([]);
		setUniqueContacts([]);
		excludedContacts = [];
		axios.post("/api/clear-campaignexcludes?lang=" + localStorage.lang, { campaign: campaignId, type: 1 })
			.then((response) => {
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
				setExcludesNum(0)
			})
			.catch((error) => {
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
				loading_count++;
				if ((campaignId && loading_count >= 5) || (!campaignId && loading_count >= 3))
					setLoading(false);
			});
	}

	const handleContactExcludeUndo = async (id) => {
		if (excludedRemovedContacts.length) {
			setErrors([])
			await axios.post("/api/remove-contact-from-campaignexcludes?lang=" + localStorage.lang, { contact: excludedRemovedContacts, campaign: id, type: 1 })
				.then((response) => {
					totl_excludes_count = parseInt(totl_excludes_count) - parseInt(response.data.total);
					setExcludesNum(totl_excludes_count);
				})
				.catch((error) => {
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
				});
		}
	}

	const onSelect = (selectedList, selectedItem) => {
		setErrors([]);
		setSelectedGroups(selectedList);

		setGroup_ids(selectedList.map(val => val.value))
		setToGroup(selectedItem.value)
		var arr1 = [...contacts]
		var arr2 = groups[selectedItem.index].contacts.filter(contact => contact.for_sms == 1);
		const final = arr1.concat(arr2);
		setContacts([...final]);
		setUniqueContacts([...final]);
	}
	const removeOneItem = (list, target) => {
		const targetIndex = list.findIndex(item => item.hash_id == target.hash_id);
		list.splice(targetIndex, 1)
	}

	const onRemove = (selectedList, removedItem) => {
		setErrors([]);
		setSelectedGroups(selectedList);
		setGroup_ids(selectedList.map(val => val.value))
		var arr1 = [...contacts]
		var arr2 = groups[removedItem.index].contacts.filter(contact => contact.for_sms == 1);
		arr2.map(i => removeOneItem(arr1, i))
		if (selectedList.length == 0) {
			setToGroup('')
			clearExcludes()
		}
		setContacts(arr1);
		setUniqueContacts(arr1);
	}

	const handleTemplateSelect = (selected) => {
		setMessage(selected.value);
	}

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						{campaignId ? <h1>{t('edit_sms_campaign')}</h1> : <h1>{t('create_sms_campaign')}</h1>}
					</div>
				</div>
				<Form className="create-form-holder" onSubmit={handleUpdate}>
					<div className="bg-white rounded-box-shadow">
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="campaign-name">{t('Campaign Name')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<input id="campaign-name" onChange={(e) => setName(e.target.value)} className="form-control" value={name} type="text" placeholder={t("eg Campaign name")} />
								{renderErrorFor('name')}
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="sender-name">{t('Sender Name')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<input id="sender-name" onChange={(e) => setSendername(e.target.value)} className="form-control" value={sender_name} type="text" placeholder="e.g. Sam Smith" />
								{renderErrorFor('sender_name')}
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="sender-no">{t('reply_to_number')}</Form.Label>
							<div className="flex-fill input-holder PhoneInput d-flex  flex-column">
								<PhoneInput
									className="form-control d-flex"
									placeholder={t("Enter phone number")}
									value={sender_number}
									onChange={number => setSendernum(number)}
									placeholder="e.g. +49 1579230198"
								/>
								{renderErrorFor('sender_number')}
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

						<Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0">{t('Select SMS Template')}</Form.Label>
							<div className="flex-fill input-holder select-styel">
								{templateOptions.length == 0 ?
									""
									:
									<Select
										onChange={(e) => handleTemplateSelect(e)}
										options={templateOptions}
										classNamePrefix="react-select"
										placeholder={t("Select SMS Template")}
									/>
								}

							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0"></Form.Label>
							<div className="flex-fill input-holder">
								<button type="submit" onClick={(e) => gotoCreateTemplate(e)} className="btn btn-secondary mt-2 ml-lg-3 ml-0 mt-0 mr-3 mb-0">
									<span>	{t('Add New Template')}</span>
								</button>
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="sms-text">{t('SMS Text')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<textarea id="sms-text" rows="5" cols="5" maxLength='250' onChange={(e) => setMessage(e.target.value)} maxLength='250' value={message} className="form-control" placeholder={t("eg Message here")} />
								<small> {250 - message.length} {t('characters_remaining')} </small>
								<p>
									<FontAwesomeIcon icon={faInfoCircle}></FontAwesomeIcon>
									{" "}
									{t('limit_is_250_characters_including_spaces')}
								</p>
								{renderErrorFor('message')}
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
									<span>{t('Add Contacts')}</span>
								</button>
								<button type="submit" onClick={goToContacts} className="btn btn-secondary ml-lg-3 mt-0 ms-2 mb-0">
									<span>{t('Import/Add new Contacts')}</span>
								</button>
								<p className="mb-0 ms-2">{addedContacts.length} {t('Contact(s) Added')} </p>
							</div>
						</Form.Group>
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row form-group">
							<Form.Label className="mb-2 mb-md-0">{t('Contact Lists')}</Form.Label>
							<div className="d-flex flex-xl-row flex-column flex-lg-nowrap flex-wrap align-items-start justify-content-between input-holder">
								<div className="flex-fill input-holder edit-sms-select">
									<div className="subscriber-select w-100">
										<Multiselect
											options={options} // Options to display in the dropdown
											onSelect={onSelect} // Function will trigger on select event
											onRemove={onRemove} // Function will trigger on remove event
											selectedValues={selectedGroups}
											displayValue="label" // Property name to display in the dropdown options
											placeholder={t("Select Mailing Lists")}
										/>
										{/* <Select
											onChange={(e) => handleChange(e)}
											options={options}
											value={options.find(o => o.value == toGroup)}
											classNamePrefix="react-select"
										/> */}
										<small> {t('unselect_to_clear')}</small>
										{renderErrorFor('group_id')}
									</div>
								</div>
								<button type="submit" onClick={(e) => gotoCreateMailingList(e)} className="btn btn-secondary mt-lg-0 mt-2 ml-lg-3 ml-0 mt-0 mr-3 mb-0">
									<span>	{t('Add mailing list')}</span>
								</button>
								<div className="mt-lg-0 mt-1 d-flex flex-column align-items-center justify-content-between">
									{selectedGroups.length ?
										<React.Fragment>
											<button type="submit" onClick={handleShow} className="exclude-btn btn btn-secondary ml-lg-3 ml-0 mt-0 mr-3 mb-0">
												<span>{t('Exclude Contacts')}</span>
											</button>
											<p className="mb-0 ms-lg-2">{excludedContacts.length} {t('contacts_excluded')} </p>
										</React.Fragment>
										: ""}
								</div>
							</div>
						</Form.Group>

						{/* Email campaign schedule options  */}
						{canSchedule || canRecurr ?
							<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
								<Form.Label className="mb-2 mb-md-0" htmlFor="reply-to-address">{t('Send Campaign')} <b className="req-sign">*</b></Form.Label>
								<div className="flex-fill input-holder radio-btns-holder d-flex flex-column pt-0">
									<div className="d-flex flex-column flex-lg-row flex-md-row align-items-start align-items-lg-center align-items-md-center flex-gap mbl-flex-wrap">
										<div className="radio-holder mr-2">
											<label className="custom-radio mb-0">{t('Immediately')}
												<input type="radio" name="sendcampaign" onChange={(e) => { setSendCampaign(e.target.value); setRecursiveCampaignType(""); }} value="1" checked={sendCampaign == 1 ? true : false} />
												<span className="checkmark"></span>
											</label>
										</div>
										{canSchedule ?
											<div className="radio-holder mr-2">
												<label className="custom-radio mb-0">{t('Schedule Once')}
													<input type="radio" name="sendcampaign" onChange={(e) => { setSendCampaign(e.target.value); setRecursiveCampaignType(""); }} value="2" checked={sendCampaign == 2 ? true : false} />
													<span className="checkmark"></span>
												</label>
											</div>
											: ""
										}

										{canRecurr ?
											<div className="radio-holder mr-2">
												<label className="custom-radio mb-0">{t('Recursively')}
													<input type="radio" name="sendcampaign" onChange={(e) => setSendCampaign(e.target.value)} value="3" checked={sendCampaign == 3 ? true : false} />
													<span className="checkmark"></span>
												</label>
											</div>
											: ""
										}
										{renderErrorFor('type')}
									</div>

									{/* schedule campaign for speicific day. campaign will send only single time  */}
									{
										sendCampaign == 2 ?
											<div className="calendar-holder pt-3 me-3">
												<DateTimePicker
													format="y-MM-dd"
													className="em-calendar"
													onChange={(e) => handleCalenderChange(e)}
													value={selectedDate}
													minDate={new Date()}
												/>
												{renderErrorFor("invalid_format")}
												{renderErrorFor('schedule_date')}
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
															<input type="radio" name="campaignrecursivetype" onChange={(e) => setRecursiveCampaignType(e.target.value)} value="1" checked={recursiveCampaignType == 1 ? true : false} />
															<span className="checkmark"></span>
														</label>
													</div>
													<div className="radio-holder mr-2">
														<label className="custom-radio">
															{t('Monthly')}
															<input type="radio" name="campaignrecursivetype" onChange={(e) => setRecursiveCampaignType(e.target.value)} value="2" checked={recursiveCampaignType == 2 ? true : false} />
															<span className="checkmark"></span>
														</label>
													</div>
													<div className="radio-holder mr-2">
														<label className="custom-radio">
															{t('Yearly')}
															<input type="radio" name="campaignrecursivetype" onChange={(e) => setRecursiveCampaignType(e.target.value)} value="3" checked={recursiveCampaignType == 3 ? true : false} />
															<span className="checkmark"></span>
														</label>
													</div>
													{renderErrorFor('recursive_campaign_type')}
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
															onChange={(e) => handleMonthOfYear(e)}
															options={MonthsOfYear}
															value={MonthsOfYear.find(o => o.value == monthOfYear)}
															classNamePrefix="react-select"
															placeholder={t("Select Month")}
														/>
													</div>
												</div>
												{renderErrorFor('month_of_year')}
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
															onChange={(e) => handleDayOfMonthChange(e)}
															options={daysOfMonth}
															value={daysOfMonth.find(o => o.value == dayOfMonth)}
															classNamePrefix="react-select"
															placeholder={t("Select Day")}
														/>
													</div>
												</div>
												{renderErrorFor('day_of_month')}
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
															onChange={(e) => handleDayOfWeekChange(e)}
															options={daysOfWeek}
															value={daysOfWeek.find(o => o.value == dayOfWeek)}
															classNamePrefix="react-select"
															placeholder={t("Select Day")}
														/>
													</div>
												</div>
												{renderErrorFor('day_of_week')}
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
														onChange={(e) => setTimes(e.target.value)}
														value={times}
													/>
												</div>
												{renderErrorFor('no_of_time')}
											</Form.Group>
											: null
									}
								</div>
							</Form.Group>
							: ""
						}

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row ">
							<Form.Label className="mb-2 mb-md-0 pt-0">{t('Test Live')}</Form.Label>
							<div className="flex-fill d-flex flex-row input-holder mbl-flex-wrap">
								{/* <div className=""> */}
								<div className="checkbox-holder">
									<input
										name="live_testing"
										className="form-checkbox"
										type="checkbox"
										onClick={handleTestingInput}
										defaultChecked={testing}
									/>
								</div>
								{/* </div> */}
								{testing ?
									<React.Fragment>
										<div className="form-group w-100">
											<AddNumbersInput changeList={val => setNumbersList(val)} />
											{renderErrorFor('numbers_list')}
										</div>
										<div>
											<Button type="submit" className=" btn-language btn btn-primary ms-xl-3 ms-0 mb-3 mt-sm-0 mt-2">
												<span> {t('Send')} </span>
											</Button>
										</div>
									</React.Fragment>
									: ""}
							</div>
						</Form.Group>
						<div className="btns-holder right-btns d-flex flex-sm-row flex-column pt-5 buttons-setting-mobile justify-content-sm-end">
							<Link onClick={() => goBack()} className="btn btn-secondary ms-sm-3 mb-3"><span>{t('Back')}</span></Link>
							{!hideStatus ?
								<button type="submit" onClick={() => { setTesting(false); setCampaignSaveAs(2); return true; }} className="btn btn-warning ms-sm-3 ml-lg-2 ml-md-2 ml-0 mb-3">
									<span>{t('save_as_draft')}</span>
								</button>
								: ""
							}
							<button type="submit" onClick={() => { setTesting(false); setCampaignSaveAs(1); return true; }} className="btn btn-primary ms-sm-3 ml-lg-2 ml-md-2 ml-0 mb-3">
								<span>{campaignId && hideStatus ? t('Update') : t('Send')}</span>
							</button>
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
												// onClick={
												// 	selectAllContacts
												// }
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
															onClick={(e) => handleExcludeCheckbox(e.target, contact.hash_id)}
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
						{contacts.length ?
							<Button variant="primary" onClick={handleClose}>
								<span>{t('Exclude/Update')}</span>
							</Button>
							: ""}
					</Modal.Footer>
				</div>
			</Modal>
			{/* // Add Contacts Modal */}
			<Modal show={showAdd} onHide={handleCloseAdd} className="em-modal contact-modal smallmodal  new-contact-modal" centered>
				<Modal.Header closeButton className="add-contact-modal em-table">
				</Modal.Header>
				<div>
					<Modal.Body className="d-flex align-items-center justify-content-center flex-column">
						<div className="mb-3 group-select-title w-100 text-center">
							<span className="static-title"></span>
						</div>
						<div className="mb-3 group-select-title w-100 text-center">
							<Form className="em-form campaign-new-contact mb-3 mb-md-2" method="GET">
								<Row>
									<Col lg="5">
										<Form.Group className="d-flex flex-row align-items-center">
											<Form.Label className="mb-0 me-3">
												{t('Select all Visible')}
											</Form.Label>
											<label className="custom-checkbox no-text mb-0">
												<input
													className="form-checkbox"
													type="checkbox"
													onClick={(e) => handleAddAllContactsCheckbox(e.target)}
												/>
												<span className="checkmark"></span>
											</label>
										</Form.Group>
									</Col>

									<Col lg="7">
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
												// onClick={
												// 	selectAllContacts
												// }
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
															onClick={() => handleAddContactCheckbox(contact.hash_id)}
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
						{contactsAdd.length ?
							<Button variant="primary" onClick={handleCloseAdd}>
								<span>{t('Add')}</span>
							</Button>
							: ""}
					</Modal.Footer>
				</div>
			</Modal>
		</React.Fragment>
	);
}

export default withTranslation()(EditSmsCampaign);