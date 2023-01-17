import React, { useEffect, useState } from "react";
import { Link, useHistory } from "react-router-dom";
import {
    Container,
    Form,
    Row,
    Col,
    Button,
    Modal,
    Table,
    Dropdown,
} from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faInfoCircle } from "@fortawesome/free-solid-svg-icons";
import { faFileImport } from "@fortawesome/free-solid-svg-icons";
import Spinner from "../includes/spinner/Spinner";
import Swal from "sweetalert2";
import Select from "react-select";
import DateTimePicker from "react-datetime-picker";
import { daysOfWeek, daysOfMonth, MonthsOfYear } from "../../constants";
import AddEmailsInput from "./AddEmailsInput";
import { react } from "@babel/types";
import Multiselect from "multiselect-react-dropdown";
import moment from "moment-timezone";
import Pagination from "react-js-pagination";
import GetUserPackage from "../Auth/GetUserPackage.js";
import { withTranslation } from "react-i18next";
var addedContacts = [];
var removedContacts = [];
var excludedContacts = [];
var excludedRemovedContacts = [];
var totl_contact_count = 0;
var totl_excludes_count = 0;
var options = [];
var templateOptions = [];
var loading_count = 0;
function CreateEmailCampaign(props) {
    const { t } = props;
    const history = useHistory();
    const statusOptions = [
        { value: 1, label: t("Active") },
        { value: 2, label: t("Draft") },
    ];
    const [templateOptionsState, setTemplateOptionsState] = useState([]);
    const [selectedStatus, setSelectedStatus] = useState({});
    const [campaignId, setCampaignId] = useState("");
    const [name, setName] = useState("");
    const [subject, setSubject] = useState("");
    const [sender_name, setSenderName] = useState("");
    const [sender_email, setSenderEmail] = useState("");
    const [reply_to_email, setReplyToEmail] = useState("");
    const [recursiveCampaignType, setRecursiveCampaignType] = useState("");
    const [dayOfWeek, setDayOfWeek] = useState("");
    const [dayOfMonth, setDayOfMonth] = useState("");
    const [monthOfYear, setMonthOfYear] = useState("");
    const [times, setTimes] = useState("");
    const [sendingCampaignType, setSendingCampaignType] = useState(1);
    const [contactGroup, setContactGroup] = useState("");
    const [templateId, setTemplateId] = useState("");
    const [selectedTemplate, setSelectedTemplate] = useState({});
    const [selectedDate, setSelectedDate] = useState(new Date());
    const [campaignSaveAs, setCampaignSaveAs] = useState(1);
    const [toContacts, SetToContacts] = useState(false);
    const [toTemplates, SetToTemplates] = useState(false);
    const [toLists, SetToLists] = useState(false);
    const [templateImg, setTemplateImg] = useState("");
    const [excludesNum, setExcludesNum] = useState(0);
    const [groups, setGroups] = useState([]);
    const [contacts, setContacts] = useState([]);
    const [uniqueContacts, setUniqueContacts] = useState([]);
    const [testing, setTesting] = useState(false);
    const [emailsList, setEmailsList] = useState([]);
    const [selectedGroup, setSelectedGroup] = useState({});
    const [contactsNum, setContactsNum] = useState(0);
    const [group_ids, setGroup_ids] = useState([]);
    const [selectedGroups, setSelectedGroups] = useState([]);
    const [hideStatus, setHideStatus] = useState(0);

    const [errors, setErrors] = useState([]);
    const [loading, setLoading] = useState("");
    const [disabled, setDisabled] = useState(false);

    const [userPackage, setUserPackage] = useState({});
    const [canSchedule, setCanSchedule] = useState(0);
    const [canRecurr, setCanRecurr] = useState(0);

    const [canDesign, setCanDesign] = useState(0);
    const [canImportBasic, setCanImportBasic] = useState(0);
    const [canImportHTML, setCanImportHTML] = useState(0);

    useEffect(() => {
        loading_count = 0;
        getContactGroup();
        let parseUriSegment = window.location.pathname.split("/");
        if (
            parseUriSegment.indexOf("email-campaign") &&
            parseUriSegment.indexOf("edit") != -1
        ) {
            getCampaign(parseUriSegment[2]);
        }
    }, []);

    useEffect(() => {
        const load = () => {
            if (userPackage != {}) {
                if (userPackage.features) {
                    if (
                        Object.keys(userPackage.features).findIndex(
                            (val) => val === "10"
                        ) >= 0
                    ) {
                        // schedule allowed
                        setCanSchedule(true);
                    } else {
                        setCanSchedule(false);
                    }
                    if (
                        Object.keys(userPackage.features).findIndex(
                            (val) => val === "11"
                        ) >= 0
                    ) {
                        // recursive allowed
                        setCanRecurr(true);
                    } else {
                        setCanRecurr(false);
                    }

                    if (
                        Object.keys(userPackage.features).findIndex(
                            (val) => val === "5"
                        ) >= 0
                    ) {
                        // import basic
                        setCanImportBasic(true);
                    } else {
                        setCanImportBasic(false);
                    }
                    if (
                        Object.keys(userPackage.features).findIndex(
                            (val) => val === "6"
                        ) >= 0
                    ) {
                        // design
                        setCanDesign(true);
                    } else {
                        setCanDesign(false);
                    }
                    if (
                        Object.keys(userPackage.features).findIndex(
                            (val) => val === "7"
                        ) >= 0
                    ) {
                        // import html
                        setCanImportHTML(true);
                        PublicTemplateListing();
                    } else {
                        setCanImportHTML(false);
                    }
                }
            }
        };
        load();
    }, [userPackage]);

    useEffect(() => {
        if (groups.length) {
            let parseUriSegment = window.location.pathname.split("/");
            if (parseUriSegment.indexOf("email-campaign") && parseUriSegment.indexOf("edit") != -1) {
                if (groups.length)
                    getCampaign2(parseUriSegment[2]);
            }
        }
    }, [groups]);

    const [showTemplates, setShowTemplates] = useState(false);
    const [templates, setTemplates] = useState([]);
    const [pageNumber2, setPageNumber2] = useState(1);
    const [perPage2, setperPage2] = useState(10);
    const [totalItems2, setTotalItems2] = useState(0);
    const [TemplateLink, setTemplateLink] = useState(0);

    const handleCloseTemplates = () => {
        setShowTemplates(false);
    };
    const handleShowTemplates = () => {
        setShowTemplates(true);
    };

    const HandleTemplateImport = (id) => {
        setLoading(true);
        axios
            .get(
                "/api/public-campaign-template/import/" +
                id +
                "?lang=" +
                localStorage.lang
            )
            .then((response) => {
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
                if (response.data.status) {
                    getContactGroup();
                    handleCloseTemplates();
                }
            })
            .catch((error) => {
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
            });
    };

    const PublicTemplateListing = () => {
        setLoading(true);
        axios
            .get(
                "/api/public-campaign-template/index/?page=" +
                pageNumber2 +
                "&lang=" +
                localStorage.lang
            )
            .then((response) => {
                if (response.data.status) {
                    loading_count++;
                    if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                        setLoading(false);
                    setTemplates(response.data.data);
                    setperPage2(response.data.meta.per_page);
                    setTotalItems2(response.data.meta.total);
                }
            })
            .catch((error) => {
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false)
                // console.log(error)
            });
    };

    const gotoCreateTemplate = (e, link) => {
        setTemplateLink(link);
        SetToTemplates(true);
        setTesting(false);
        return true;
    };

    //set contact group selected value
    const handleContactChange = (selectedOption) => {
        setSelectedGroup(selectedOption);
        clearExcludes();
        setContactGroup(selectedOption.value);
        setContacts(
            groups[selectedOption.index].contacts.filter(
                (contact) => contact.for_email == 1
            )
        );
        setUniqueContacts(
            groups[selectedOption.index].contacts.filter(
                (contact) => contact.for_email == 1
            )
        );
    };

    const handleStatusChange = (selectedOption) => {
        setSelectedStatus(selectedOption);
    };

    const handleTemplateChange = (selectedOption) => {
        setSelectedTemplate(selectedOption);
        setTemplateId(selectedOption.value);
        setTemplateImg(selectedOption.img);
    };

    //set day of month selected value
    const handleMonthOfYear = (selectedOption) => {
        setMonthOfYear(selectedOption.value);
    };

    //set day of month selected value
    const handleDayOfMonthChange = (selectedOption) => {
        setDayOfMonth(selectedOption.value);
    };

    //set day of week selected value
    const handleDayOfWeekChange = (selectedOption) => {
        setDayOfWeek(selectedOption.value);
    };

    //set send campaign at date value
    const handleCalenderChange = (date) => {
        if (
            !moment(
                moment(date).format("YYYY-MM-DD"),
                "YYYY-MM-DD",
                true
            ).isValid()
        ) {
            // setErrors({
            // 	invalid_format: [t("invalid_format")],
            // });
            setSelectedDate(new Date());
        } else {
            setSelectedDate(date);
        }
    };

    const handleTestingInput = () => {
        setTesting(!testing);
    };

    // modal
    const [show, setShow] = useState(false);
    const handleClose = () => {
        setShow(false);
    };
    const handleShow = (e) => {
        e.preventDefault();
        setShow(true);
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
                "&lang=" +
                localStorage.lang +
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
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
                // console.log(contacts);
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                    loading_count++;
                    if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                        setLoading(false);
                }
            });
    };

    // for contacts
    useEffect(() => {
        getContacts();
    }, [pageNumber, filterName]);

    const handleContactAdd = async (id) => {
        totl_contact_count = contactsNum;
        if (addedContacts.length) {
            await axios.post("/api/add-contact-to-campaignincludes?lang=" + localStorage.lang, { contact: addedContacts, campaign_id: id, type: 2 })
                .then(async (response) => {
                    totl_contact_count =
                        response.data.total > 0
                            ? parseInt(totl_contact_count) +
                            parseInt(response.data.total)
                            : totl_contact_count;
                    setContactsNum(totl_contact_count);
                    await handleContactRemove(id);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                });
        } else {
            await handleContactRemove(id);
        }
    };

    const handleContactRemove = async (id) => {
        if (removedContacts.length) {
            await axios.post("/api/remove-contact-from-campaignincludes?lang=" + localStorage.lang, { contact: removedContacts, campaign_id: id, type: 2, })
                .then((response) => {
                    totl_contact_count =
                        parseInt(totl_contact_count) -
                        parseInt(response.data.total);
                    setContactsNum(totl_contact_count);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                });
        }
    };

    const handleAddContactCheckbox = (contact_id) => {
        if (addedContacts.includes(contact_id)) {
            // add contact to list
            removedContacts.push(contact_id);
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
    };

    const handleAddAllContactsCheckbox = (box) => {
        if (box.checked) {
            contactsAdd.map((cont) => {
                addedContacts.push(cont.hash_id);
                const index = removedContacts.indexOf(cont.hash_id);
                if (index > -1) {
                    removedContacts.splice(index, 1);
                }
                document.getElementById("n" + cont.hash_id).checked = 1;
            });
        } else {
            contactsAdd.map((cont) => {
                document.getElementById("n" + cont.hash_id).checked = 0;
                removedContacts.push(cont.hash_id);
                const index = addedContacts.indexOf(cont.hash_id);
                if (index > -1) {
                    addedContacts.splice(index, 1);
                }
            });
        }
    };

    const handleExcludeCheckbox = (box, contact_id) => {
        if (box.checked) {
            excludedContacts.push(contact_id);
            const index = excludedRemovedContacts.indexOf(contact_id);
            if (index > -1) {
                excludedRemovedContacts.splice(index, 1);
            }
        } else {
            excludedRemovedContacts.push(contact_id);
            const index = excludedContacts.indexOf(contact_id);
            if (index > -1) {
                excludedContacts.splice(index, 1);
            }
        }
        // console.log(excludedContacts);
    };

    // errors
    const hasErrorFor = (field) => {
        return !!errors[field];
    };
    const renderErrorFor = (field) => {
        if (hasErrorFor(field)) {
            return (
                <span className="invalid-feedback">
                    <strong>{errors[field][0]}</strong>
                </span>
            );
        }
    };

    const goBack = () => {
        let params = new URLSearchParams(location.search);
        if (params.get('page')) {
            window.location.href = "/email-campaign/list?page=" + params.get('page');
        }
        else {
            window.location.href = "/email-campaign/list";
        }
    }

    const getCampaign = (id) => {
        setLoading(true);
        axios
            .get(`/api/campaign/` + id + `/edit?lang=` + localStorage.lang, {
                params: {
                    _id: id,
                },
            })
            .then((res) => {
                setCampaignId(res.data.data.hash_id);
                setName(res.data.data.name);
                setSubject(res.data.data.subject);
                setSenderName(res.data.data.sender_name);
                setSenderEmail(res.data.data.sender_email);
                setReplyToEmail(res.data.data.reply_to_email);
                setSendingCampaignType(res.data.data.campaign_type);
                setRecursiveCampaignType(res.data.data.recursive_campaign_type);
                setDayOfWeek(res.data.data.day_of_week);
                setDayOfMonth(res.data.data.day_of_month);
                setMonthOfYear(res.data.data.month_of_year);
                setTimes(res.data.data.no_of_time);
                setContactGroup(res.data.data.group_id);
                setGroup_ids(res.data.data.group_ids);
                setSelectedDate(new Date(res.data.data.schedule_date));
                setExcludesNum(res.data.data.excludes.length);
                var c2 = res.data.data.excludes.map((cont) => cont.hash_id);
                excludedContacts = c2;
                setContactsNum(res.data.data.contacts.length);
                var c = res.data.data.contacts.map((cont) => cont.hash_id);
                addedContacts = c;
                if (res.data.data.status == "Draft") {
                    setSelectedStatus(statusOptions[1]);
                    setHideStatus(0);
                } else {
                    setSelectedStatus(statusOptions[0]);
                    setHideStatus(1);
                }
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
            })
            .catch((error) => {
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
            });
    };

    const getCampaign2 = (id) => {
        setLoading(true);
        axios
            .get(`/api/campaign/` + id + `/edit?lang=` + localStorage.lang, {
                params: {
                    _id: id,
                },
            })
            .then((res) => {
                setTemplateId(res.data.data.template_id);
                var selected_groups = [];
                res.data.data.group_ids.map((val) =>
                    selected_groups.push(options.find((o) => o.value == val))
                );
                setSelectedGroups([...selected_groups]);
                setSelectedTemplate(
                    templateOptions.find(
                        (o) => o.value == res.data.data.template_id
                    )
                );
                setTemplateImg(
                    templateOptions.find(
                        (o) => o.value == res.data.data.template_id
                    ).img
                );
                var selectedOpt = options.find(
                    (o) => o.value == res.data.data.group_id
                );
                setSelectedGroup(selectedOpt);
                var tempcontacts = [];
                res.data.data.group_ids.map((id) => {
                    var selectedOpt = options.find((o) => o.value == id);
                    if (selectedOpt.value != "") var arr1 = [...tempcontacts];
                    var arr2 = groups[selectedOpt.index].contacts;
                    tempcontacts = arr1.concat(arr2);
                });
                setContacts(
                    [...tempcontacts].filter(
                        (contact) => contact.for_email == 1
                    )
                );
                setUniqueContacts(
                    [...tempcontacts].filter(
                        (contact) => contact.for_email == 1
                    )
                );
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
            })
            .catch((error) => {
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
            });
    };

    const getContactGroup = () => {
        setLoading(true);
        axios
            .get("/api/contact/group?lang=" + localStorage.lang)
            .then((res) => {
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
                setGroups(
                    res.data.data.groups.filter((e) => e.contacts.length > 0)
                );
                options = [];
                res.data.data.groups
                    .filter((e) => e.contacts.length > 0)
                    .map((group, index) => {
                        options.push({
                            value: group.hash_id,
                            label: group.name,
                            index: index,
                        });
                    });

                templateOptions = [];
                res.data.data.templates.map((template) => {
                    templateOptions.push({
                        value: template.id,
                        label: template.name,
                        img: template.image,
                    });
                });
                setTemplateOptionsState(templateOptions);
            })
            .catch((error) => {
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
            });
    };


    const SendCampaignType = (e) => {
        if (e.target.value != 3) {
            //empty state value if
            setRecursiveCampaignType("");
            setDayOfWeek("");
            setDayOfMonth("");
            setMonthOfYear("");
            setSelectedDate(new Date());
        }

        setSendingCampaignType(e.target.value);
    };

    const getSendingData = (numb, camp_id = '') => {
        const data = {
            name: name,
            subject: subject,
            sender_name: sender_name,
            sender_email: sender_email,
            reply_to_email: reply_to_email,
            campaign_id: camp_id,
            campaign_type: sendingCampaignType,
            schedule_date: selectedDate ? moment(selectedDate).format("YYYY-MM-DD") != "" ? moment.tz(moment(selectedDate).format("YYYY-MM-DD") + " 12:00", localStorage.timezone).utc().format("YYYY-MM-DD") : null : null,
            recursive_campaign_type: recursiveCampaignType,
            day_of_week: dayOfWeek,
            day_of_month: dayOfMonth,
            month_of_year: monthOfYear,
            group_id: contactGroup,
            group_ids: group_ids,
            template_id: templateId,
            campaign_status: numb == 1 ? 2 : 1,
            campaign_testing: testing,
            emails_list: emailsList,
            no_of_time: times,
        }
        return data;
    };

    const handleSubmit = (event) => {
        event.preventDefault();
        setLoading(true);
        setDisabled(true);
        setErrors([]);

        if (
            name == "" &&
            subject == "" &&
            sender_name == "" &&
            sender_email == "" &&
            reply_to_email == ""
        ) {
            setErrors({
                name: [name == "" ? t("atleast_one_required") : ""],
                subject: [subject == "" ? t("atleast_one_required") : ""],
                sender_name: [
                    sender_name == "" ? t("atleast_one_required") : "",
                ],
                sender_email: [
                    sender_email == "" ? t("atleast_one_required") : "",
                ],
                reply_to_email: [
                    reply_to_email == "" ? t("atleast_one_required") : "",
                ],
            });
            SetToContacts(false);
            SetToTemplates(false);
            loading_count++;
            if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                setLoading(false);
            setDisabled(false);
            return;
        }
        setErrors([]);
        const data = getSendingData(1, campaignId);

        axios.post("/api/campaign/create-update?lang=" + localStorage.lang, data)
            .then(async (res) => {
                if (res.response) {
                    if (res.response.data.errors) {
                        setErrors(res.response.data.errors);
                    }
                } else {
                    if (res.data.status) {
                        console.log("here");
                        if (testing) {
                            console.log("testing");
                            Swal.fire({
                                title: t("Success"),
                                text: res.data.message,
                                icon: "success",
                                showCancelButton: false,
                                confirmButtonText: t("OK"),
                            });
                        } else {
                            const id = res.data.data.hash_id;
                            if (toContacts) {
                                console.log("cont");
                                history.push("/contacts/add-multiple?email=" + id);
                            } else if (toTemplates) {
                                console.log("temp");
                                history.push(TemplateLink + "?campaign=" + "email-campaign" + "&id=" + id);
                            } else if (toLists) {
                                console.log("list");
                                history.push("/mailing-lists/create?email=" + id);
                            } else {
                                console.log("else");
                                await handleContactAdd(id);
                                await handleContactExclude(id);
                                if (campaignSaveAs == 1 && id) {
                                    const data2 = getSendingData(2, id);
                                    await axios.post("/api/campaign/create-update?lang=" + localStorage.lang, data2)
                                        .then((response) => {
                                            Swal.fire({
                                                title: t("Success"),
                                                text: campaignId && hideStatus ? t("Your campaign has been Sent successfully!") : t("Your campaign has been updated successfully!"),
                                                icon: "success",
                                                showCancelButton: false,
                                                confirmButtonText: t("OK"),
                                            }).then((result) => {
                                                goBack();
                                            });
                                        })
                                        .catch((error) => {
                                            if (error.response) {
                                                if (error.response.data.errors) {
                                                    setErrors(error.response.data.errors);
                                                }
                                            }
                                            if (error.response.data.code) {
                                                Swal.fire({
                                                    title: t("Please Upgrade Your Package to Send Campaign to more Contacts"),
                                                    text: error.response.data.message,
                                                    icon: "warning",
                                                    showCancelButton: false,
                                                    confirmButtonText: t("OK"),
                                                });
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
                    }
                }
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
                setDisabled(false);
            })
            .catch((error) => {
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
                setDisabled(false);
                if (error.response) {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                }
                if (error.response.data.code) {
                    Swal.fire({
                        title: t("Please Upgrade Your Package to Send Campaign to more Contacts"),
                        text: error.response.data.message,
                        icon: "warning",
                        showCancelButton: false,
                        confirmButtonText: t("OK"),
                    });
                }
            });

        SetToContacts(false);
    };

    const goToContacts = () => {
        SetToContacts(true);
        return true;
    };

    const gotoCreateMailingList = (e) => {
        SetToLists(true);
        setTesting(false);
        return true;
    };

    const clearExcludes = () => {
        // clears all excludes in the campaign
        setLoading(true);
        setContacts([]);
        setUniqueContacts([]);
        excludedContacts = [];
        axios
            .post("/api/clear-campaignexcludes?lang=" + localStorage.lang, {
                campaign: campaignId,
                type: 2,
            })
            .then((response) => {
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
                setExcludesNum(0);
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                loading_count++;
                if ((campaignId && loading_count >= 4) || (!campaignId && loading_count >= 2))
                    setLoading(false);
            });
    };

    const handleContactExclude = async (id) => {
        totl_excludes_count = excludesNum;
        if (excludedContacts.length) {
            setErrors([]);
            await axios.post("/api/add-contact-to-campaignexcludes?lang=" + localStorage.lang, { contact: excludedContacts, campaign: id, type: 2 })
                .then(async (response) => {
                    totl_excludes_count =
                        response.data.total > 0
                            ? parseInt(totl_excludes_count) +
                            parseInt(response.data.total)
                            : totl_excludes_count;
                    setExcludesNum(totl_excludes_count);
                    await handleContactExcludeUndo(id);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                });
        } else {
            await handleContactExcludeUndo(id);
        }
        handleClose();
    };

    const handleContactExcludeUndo = async (id) => {
        if (excludedRemovedContacts.length) {
            setErrors([]);
            await axios.post("/api/remove-contact-from-campaignexcludes?lang=" + localStorage.lang, { contact: excludedRemovedContacts, campaign: id, type: 2, })
                .then((response) => {
                    totl_excludes_count =
                        parseInt(totl_excludes_count) -
                        parseInt(response.data.total);
                    setExcludesNum(totl_excludes_count);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                });
        }
    };

    const onSelect = (selectedList, selectedItem) => {
        setErrors([]);
        setSelectedGroups(selectedList);
        setGroup_ids(selectedList.map((val) => val.value));
        setContactGroup(selectedItem.value);
        var arr1 = [...contacts];
        var arr2 = groups[selectedItem.index].contacts.filter(
            (contact) => contact.for_email == 1
        );
        const final = arr1.concat(arr2);
        setContacts([...final]);
        setUniqueContacts([...final]);
    };

    const removeOneItem = (list, target) => {
        const targetIndex = list.findIndex(
            (item) => item.hash_id == target.hash_id
        );
        list.splice(targetIndex, 1);
    };

    const onRemove = (selectedList, removedItem) => {
        setErrors([]);
        setSelectedGroups(selectedList);
        setGroup_ids(selectedList.map((val) => val.value));
        var arr1 = [...contacts];
        var arr2 = groups[removedItem.index].contacts;
        arr2.map((i) => removeOneItem(arr1, i));
        if (selectedList.length == 0) {
            setContactGroup("");
            clearExcludes();
        }
        setContacts(arr1);
        setUniqueContacts(arr1);
    };

    return (
        <React.Fragment>
            {loading ? <Spinner /> : null}
            <GetUserPackage
                parentCallback={(data) => {
                    setUserPackage(data);
                }}
            />
            <Container fluid>
                <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                    <div className="page-title">
                        {campaignId ? (
                            <h1>{t("edit_email_campaign")}</h1>
                        ) : (
                            <h1>{t("create_email_campaign")}</h1>
                        )}
                    </div>
                </div>
                <Form
                    className="create-form-holder rounded-box-shadow bg-white"
                    onSubmit={handleSubmit}
                >
                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                        <Form.Label
                            className="mb-2 mb-md-0"
                            htmlFor="campaign-name"
                        >
                            {t("Campaign Name")} <b className="req-sign">*</b>
                        </Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                id="campaign-name"
                                className="form-control"
                                type="text"
                                onChange={(e) => setName(e.target.value)}
                                value={name}
                                placeholder={t("eg Campaign name")}
                            />
                            {renderErrorFor("name")}
                        </div>
                    </Form.Group>

                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                        <Form.Label
                            className="mb-2 mb-md-0"
                            htmlFor="subject-line"
                        >
                            {t("Subject Line")} <b className="req-sign">*</b>
                        </Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                id="subject-line"
                                className="form-control"
                                type="text"
                                onChange={(e) => setSubject(e.target.value)}
                                value={subject}
                                placeholder={t("eg Email Subject")}
                            />
                            {renderErrorFor("subject")}
                        </div>
                    </Form.Group>

                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                        <Form.Label
                            className="mb-2 mb-md-0"
                            htmlFor="sender-name"
                        >
                            {t("Sender Name")} <b className="req-sign">*</b>
                        </Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                id="sender-name"
                                className="form-control"
                                type="text"
                                onChange={(e) => setSenderName(e.target.value)}
                                value={sender_name}
                                placeholder="e.g. Sam Smith"
                            />
                            {renderErrorFor("sender_name")}
                        </div>
                    </Form.Group>

                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                        <Form.Label
                            className="mb-2 mb-md-0"
                            htmlFor="sender-email"
                        >
                            {t("Sender Email Address")}{" "}
                            <b className="req-sign">*</b>
                        </Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                id="sender-email"
                                className="form-control"
                                type="text"
                                onChange={(e) => setSenderEmail(e.target.value)}
                                value={sender_email}
                                placeholder="e.g. example@email.com"
                            />
                            {renderErrorFor("sender_email")}
                        </div>
                    </Form.Group>

                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                        <Form.Label
                            className="mb-2 mb-md-0"
                            htmlFor="reply-to-email"
                        >
                            {t("Reply to Address")}{" "}
                        </Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                id="reply-to-email"
                                className="form-control"
                                type="text"
                                onChange={(e) =>
                                    setReplyToEmail(e.target.value)
                                }
                                value={reply_to_email}
                                placeholder="e.g. example@email.com"
                            />
                            {renderErrorFor("reply_to_email")}
                        </div>
                    </Form.Group>

                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                        <Form.Label className="mb-2 mb-md-0">
                            {t("Select Template")}
                        </Form.Label>
                        <div className="flex-fill input-holder">
                            <div className="subscriber-select w-100">
                                {templateOptionsState.length == 0 ? (
                                    ""
                                ) : (
                                    <Select
                                        onChange={(e) =>
                                            handleTemplateChange(e)
                                        }
                                        options={templateOptionsState}
                                        placeholder={t("Select Template")}
                                        value={selectedTemplate}
                                        classNamePrefix="react-select"
                                    />
                                )}
                                {renderErrorFor("template_id")}
                            </div>
                            {templateImg ? (
                                <div id="template_image">
                                    {" "}
                                    <img src={templateImg} />{" "}
                                </div>
                            ) : (
                                ""
                            )}
                            <div className="add-contact-dropdown mt-3">
                                <Dropdown>
                                    <Dropdown.Toggle
                                        variant="success"
                                        id="dropdown-basic"
                                    >
                                        <span className="btn btn-secondary">
                                            <span>{t("Add New Template")}</span>
                                        </span>
                                    </Dropdown.Toggle>
                                    <Dropdown.Menu>
                                        <ul className="list-unstyled sub-menu">
                                            {canDesign ? (
                                                <li>
                                                    <button
                                                        type="submit"
                                                        onClick={(e) =>
                                                            gotoCreateTemplate(
                                                                e,
                                                                "/email-template/create"
                                                            )
                                                        }
                                                        className="drop-menu-buton btn btn-secondary ml-lg-3 ml-0 mt-0 mr-3 mb-0"
                                                    >
                                                        {t("Design Template")}
                                                    </button>
                                                </li>
                                            ) : (
                                                ""
                                            )}
                                            {canImportHTML ? (
                                                <li>
                                                    <button
                                                        type="submit"
                                                        onClick={(e) =>
                                                            gotoCreateTemplate(
                                                                e,
                                                                "/email-template/import"
                                                            )
                                                        }
                                                        className="drop-menu-buton btn btn-secondary ml-lg-3 ml-0 mt-0 mr-3 mb-0"
                                                    >
                                                        {t(
                                                            "Import HTML Template"
                                                        )}
                                                    </button>
                                                </li>
                                            ) : (
                                                ""
                                            )}
                                            {canImportBasic ? (
                                                <li>
                                                    <Link
                                                        onClick={
                                                            handleShowTemplates
                                                        }
                                                    >
                                                        {t(
                                                            "Import Existing Template"
                                                        )}
                                                    </Link>
                                                </li>
                                            ) : (
                                                ""
                                            )}
                                        </ul>
                                    </Dropdown.Menu>
                                </Dropdown>
                            </div>
                        </div>
                    </Form.Group>

                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                        <Form.Label className="mb-2 mb-md-0">
                            {t("Contact Details")} <b className="req-sign">*</b>
                        </Form.Label>
                        <div className="mt-lg-0 mt-0  d-flex flex-column input-holder flex-sm-row align-items-center justify-content-between mbl-flex-wrap">
                            <p>
                                <FontAwesomeIcon
                                    icon={faInfoCircle}
                                ></FontAwesomeIcon>{" "}
                                {t(
                                    "you_can_either_add_individual_contacts_contact_lists_or_both_to_the_campaign"
                                )}
                            </p>
                        </div>
                    </Form.Group>
                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row form-group">
                        <Form.Label className="mb-2 mb-md-0">
                            {t("Add Contacts")}
                        </Form.Label>
                        <div className="mt-lg-0 mt-3 d-flex input-holder flex-column flex-sm-row align-items-center justify-content-between mbl-flex-wrap">
                            {/* <h3>OR</h3> */}
                            <button
                                type="submit"
                                onClick={handleShowAdd}
                                className="btn btn-secondary ml-lg-3 ml-0 mt-0 mr-3 mb-0"
                            >
                                <span>{t("Add Contacts")}</span>
                            </button>
                            <button
                                type="submit"
                                onClick={goToContacts}
                                className="btn btn-secondary ml-lg-3 mt-0 ms-2 mb-0"
                            >
                                <span>{t("Import/Add new Contacts")}</span>
                            </button>
                            <p className="mb-0 ms-2">
                                {addedContacts.length} {t("Contact(s) Added")}{" "}
                            </p>
                        </div>
                    </Form.Group>
                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row form-group">
                        <Form.Label className="mb-2 mb-md-0">
                            {t("Contact Lists")}
                        </Form.Label>
                        <div className="d-flex flex-lg-row flex-column flex-xl-nowrap flex-wrap align-items-start justify-content-between input-holder">
                            <div className="flex-fill input-holder edit-sms-select">
                                <div className="subscriber-select w-100">
                                    <Multiselect
                                        options={options} // Options to display in the dropdown
                                        onSelect={onSelect} // Function will trigger on select event
                                        onRemove={onRemove} // Function will trigger on remove event
                                        selectedValues={selectedGroups}
                                        displayValue="label" // Property name to display in the dropdown options
                                        placeholder={t("Select Mailing Lists")}
                                        classNamePrefix="my-multi-select"
                                    />
                                    <small> {t("unselect_to_clear")}</small>
                                    {renderErrorFor("group_id")}
                                </div>
                            </div>
                            <button
                                type="submit"
                                onClick={(e) => gotoCreateMailingList(e)}
                                className="btn btn-secondary mt-2 mt-lg-0 ml-lg-3 ml-0 mt-0 mr-3 mb-0"
                            >
                                <span> {t("Add mailing list")}</span>
                            </button>
                            <div className="mt-lg-0 mt-1 d-flex flex-column  align-items-center justify-content-between">
                                {selectedGroups.length ? (
                                    <React.Fragment>
                                        <button
                                            type="submit"
                                            onClick={handleShow}
                                            className="exclude-btn btn btn-secondary ml-lg-3 ml-0 mt-0 mr-3 mb-0"
                                        >
                                            <span>{t("Exclude Contacts")}</span>
                                        </button>
                                        <p className="mb-0 ms-lg-2">
                                            {excludedContacts.length}{" "}
                                            {t("contacts_excluded")}{" "}
                                        </p>
                                    </React.Fragment>
                                ) : (
                                    ""
                                )}
                            </div>
                        </div>
                    </Form.Group>

                    {/* Email campaign schedule options  */}
                    {canSchedule || canRecurr ? (
                        <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                            <Form.Label
                                className="mb-2 mb-md-0"
                                htmlFor="reply-to-address"
                            >
                                {t("Send Campaign")}{" "}
                                <b className="req-sign">*</b>
                            </Form.Label>
                            <div className="flex-fill input-holder radio-btns-holder d-flex flex-column pt-0">
                                <div className="flex-gap d-flex flex-column flex-lg-row flex-md-row align-items-start align-items-lg-center align-items-md-center mbl-flex-wrap">
                                    <div className="radio-holder mr-2">
                                        <label className="custom-radio mb-0">
                                            {t("Immediately")}
                                            <input
                                                type="radio"
                                                name="sendcampaign"
                                                onChange={(e) =>
                                                    SendCampaignType(e)
                                                }
                                                value="1"
                                                checked={
                                                    sendingCampaignType == 1
                                                        ? true
                                                        : false
                                                }
                                            />
                                            <span className="checkmark"></span>
                                        </label>
                                    </div>
                                    {canSchedule ? (
                                        <div className="radio-holder mr-2">
                                            <label className="custom-radio mb-0">
                                                {t("Schedule Once")}
                                                <input
                                                    type="radio"
                                                    name="sendcampaign"
                                                    onChange={(e) =>
                                                        SendCampaignType(e)
                                                    }
                                                    value="2"
                                                    checked={
                                                        sendingCampaignType == 2
                                                            ? true
                                                            : false
                                                    }
                                                />
                                                <span className="checkmark"></span>
                                            </label>
                                        </div>
                                    ) : (
                                        ""
                                    )}
                                    {canRecurr ? (
                                        <div className="radio-holder mr-2">
                                            <label className="custom-radio mb-0">
                                                {t("Recursively")}
                                                <input
                                                    type="radio"
                                                    name="sendcampaign"
                                                    onChange={(e) =>
                                                        SendCampaignType(e)
                                                    }
                                                    value="3"
                                                    checked={
                                                        sendingCampaignType == 3
                                                            ? true
                                                            : false
                                                    }
                                                />
                                                <span className="checkmark"></span>
                                            </label>
                                        </div>
                                    ) : (
                                        ""
                                    )}
                                    {renderErrorFor("campaign_type")}
                                </div>

                                {/* schedule campaign for speicific day. campaign will send only single time  */}
                                {sendingCampaignType == 2 ? (
                                    <div className="calendar-holder pt-3 me-3">
                                        <DateTimePicker
                                            format="y-MM-dd"
                                            className="em-calendar"
                                            onChange={(e) =>
                                                handleCalenderChange(e)
                                            }
                                            value={selectedDate}
                                            minDate={new Date()}
                                        />
                                        {renderErrorFor("invalid_format")}
                                        {renderErrorFor("schedule_date")}
                                    </div>
                                ) : null}

                                {/* Show to create campaign with recursive option */}
                                {sendingCampaignType == 3 ? (
                                    <Form.Group className="align-items-center mb-2 mb-md-4 d-flex flex-column flex-md-row mbl-flex-wrap">
                                        <Form.Label
                                            className="mb-2 mb-md-0"
                                            htmlFor="reply-to-address"
                                        >
                                            {t("Set the Recursion Cycle")}
                                        </Form.Label>
                                        <div className="flex-fill input-holder radio-btns-holder d-flex mbl-flex-wrap">
                                            <div className="radio-holder mr-2">
                                                <label className="custom-radio">
                                                    {t("Weekly")}
                                                    <input
                                                        type="radio"
                                                        name="campaignrecursivetype"
                                                        onChange={(e) =>
                                                            setRecursiveCampaignType(
                                                                e.target.value
                                                            )
                                                        }
                                                        value="1"
                                                        checked={
                                                            recursiveCampaignType ==
                                                                1
                                                                ? true
                                                                : false
                                                        }
                                                    />
                                                    <span className="checkmark"></span>
                                                </label>
                                            </div>
                                            <div className="radio-holder mr-2">
                                                <label className="custom-radio">
                                                    {t("Monthly")}
                                                    <input
                                                        type="radio"
                                                        name="campaignrecursivetype"
                                                        onChange={(e) =>
                                                            setRecursiveCampaignType(
                                                                e.target.value
                                                            )
                                                        }
                                                        value="2"
                                                        checked={
                                                            recursiveCampaignType ==
                                                                2
                                                                ? true
                                                                : false
                                                        }
                                                    />
                                                    <span className="checkmark"></span>
                                                </label>
                                            </div>
                                            <div className="radio-holder mr-2">
                                                <label className="custom-radio">
                                                    {t("Yearly")}
                                                    <input
                                                        type="radio"
                                                        name="campaignrecursivetype"
                                                        onChange={(e) =>
                                                            setRecursiveCampaignType(
                                                                e.target.value
                                                            )
                                                        }
                                                        value="3"
                                                        checked={
                                                            recursiveCampaignType ==
                                                                3
                                                                ? true
                                                                : false
                                                        }
                                                    />
                                                    <span className="checkmark"></span>
                                                </label>
                                            </div>
                                            {renderErrorFor(
                                                "recursive_campaign_type"
                                            )}
                                        </div>
                                    </Form.Group>
                                ) : null}

                                {/* Recursive Yearly- show list of Months */}
                                {recursiveCampaignType == 3 ? (
                                    <Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
                                        <Form.Label className="mb-2 mb-md-0">
                                            {t("Select Month")}
                                        </Form.Label>
                                        <div className="flex-fill input-holder">
                                            <div className="subscriber-select w-100">
                                                <Select
                                                    onChange={(e) =>
                                                        handleMonthOfYear(e)
                                                    }
                                                    options={MonthsOfYear}
                                                    value={MonthsOfYear.find(
                                                        (o) =>
                                                            o.value ==
                                                            parseInt(
                                                                monthOfYear
                                                            )
                                                    )}
                                                    classNamePrefix="react-select"
                                                    placeholder={t(
                                                        "Select Month"
                                                    )}
                                                />
                                                {renderErrorFor(
                                                    "month_of_year"
                                                )}
                                            </div>
                                        </div>
                                    </Form.Group>
                                ) : null}

                                {/* Recursive Monthly- show days no of Month */}
                                {recursiveCampaignType == 2 ||
                                    recursiveCampaignType == 3 ? (
                                    <Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
                                        <Form.Label className="mb-2 mb-md-0">
                                            {t("Select Day of Month")}
                                        </Form.Label>
                                        <div className="flex-fill input-holder">
                                            <div className="subscriber-select w-100">
                                                <Select
                                                    onChange={(e) =>
                                                        handleDayOfMonthChange(
                                                            e
                                                        )
                                                    }
                                                    options={daysOfMonth}
                                                    value={daysOfMonth.find(
                                                        (o) =>
                                                            o.value ==
                                                            parseInt(dayOfMonth)
                                                    )}
                                                    classNamePrefix="react-select"
                                                    placeholder={t(
                                                        "Select Day"
                                                    )}
                                                />
                                                {renderErrorFor("day_of_month")}
                                            </div>
                                        </div>
                                    </Form.Group>
                                ) : null}

                                {/* Recursive Weekly- show days of Week */}
                                {recursiveCampaignType == 1 ? (
                                    <Form.Group className="mb-2 mb-md-2 d-flex flex-column flex-md-row">
                                        <Form.Label className="mb-2 mb-md-0">
                                            {t("Select Day of Week")}
                                        </Form.Label>
                                        <div className="flex-fill input-holder">
                                            <div className="subscriber-select w-100">
                                                <Select
                                                    onChange={(e) =>
                                                        handleDayOfWeekChange(e)
                                                    }
                                                    options={daysOfWeek}
                                                    value={daysOfWeek.find(
                                                        (o) =>
                                                            o.value ==
                                                            parseInt(dayOfWeek)
                                                    )}
                                                    classNamePrefix="react-select"
                                                    placeholder={t(
                                                        "Select Day"
                                                    )}
                                                />
                                                {renderErrorFor("day_of_week")}
                                            </div>
                                        </div>
                                    </Form.Group>
                                ) : null}

                                {/* Recursive weekly / Monthly / Yearly - No of times to send */}
                                {recursiveCampaignType == 1 ||
                                    recursiveCampaignType == 2 ||
                                    recursiveCampaignType == 3 ? (
                                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                                        <Form.Label className="mb-2 mb-md-0">
                                            {t("No of Time")}
                                        </Form.Label>
                                        <div className="flex-fill input-holder">
                                            <input
                                                id="no-of-time"
                                                className="form-control"
                                                type="text"
                                                onChange={(e) =>
                                                    setTimes(e.target.value)
                                                }
                                                value={times}
                                            />
                                            {renderErrorFor("no_of_time")}
                                        </div>
                                    </Form.Group>
                                ) : null}
                            </div>
                        </Form.Group>
                    ) : (
                        ""
                    )}

                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                        <Form.Label className="mb-2 mb-md-0 pt-0">
                            {t("Test Live")}
                        </Form.Label>
                        <div className="flex-fill d-flex input-holder flex-row mbl-flex-wrap">
                            {/* <div className=""> */}
                            <div className="checkbox-holder">
                                <input
                                    name="live_testing"
                                    className="form-checkbox"
                                    type="checkbox"
                                    onClick={handleTestingInput}
                                    checked={testing}
                                />
                            </div>
                            {/* </div> */}
                            {testing ? (
                                <React.Fragment>
                                    <div className="form-group w-100">
                                        <AddEmailsInput
                                            changeList={(val) =>
                                                setEmailsList(val)
                                            }
                                        />
                                        {renderErrorFor("emails_list")}
                                    </div>
                                    <div>
                                        <Button
                                            type="submit"
                                            className="btn-language btn btn-primary ms-xl-3 ms-0 mb-3 mt-sm-0 mt-2"
                                        >
                                            <span> {t("Send")} </span>
                                        </Button>
                                    </div>
                                </React.Fragment>
                            ) : (
                                ""
                            )}
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
                        <Button type="submit" onClick={() => { setTesting(false); setCampaignSaveAs(1); return true; }} disabled={disabled} className="btn btn-primary ms-sm-3 ml-lg-2 ml-md-2 ml-0 mb-3">
                            <span>{campaignId && hideStatus ? t("Update") : t("Send")}</span>
                        </Button>
                    </div>
                </Form>
            </Container>
            {/* // Contacts Modal */}
            <Modal
                show={show}
                onHide={handleClose}
                className="em-modal contact-modal smallmodal "
                centered
            >
                <div>
                    <Modal.Body className="d-flex align-items-center justify-content-center flex-column em-table">
                        <div className="mb-3 group-select-title w-100 text-center">
                            <span className="static-title">
                                {t("selected lists")} :
                            </span>
                            <div className="selected-groups mt-3">
                                {selectedGroups.map((group) => (
                                    <span key={group.hash_id} className="chip">
                                        {" "}
                                        {group.label}{" "}
                                    </span>
                                ))}
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
                                        <th>{t("Contacts Name")}</th>
                                        <th>{t("Email")}</th>
                                        <th>{t("Mobile no.")}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {contacts.length ? (
                                        [
                                            ...new Map(
                                                uniqueContacts.map((item) => [
                                                    item["hash_id"],
                                                    item,
                                                ])
                                            ).values(),
                                        ].map((contact, index) => (
                                            <tr key={contact.hash_id}>
                                                <td>
                                                    <label className="custom-checkbox no-text me-0">
                                                        <input
                                                            className="form-checkbox"
                                                            type="checkbox"
                                                            onClick={(e) =>
                                                                handleExcludeCheckbox(
                                                                    e.target,
                                                                    contact.hash_id
                                                                )
                                                            }
                                                            defaultChecked={
                                                                excludedContacts.includes(
                                                                    contact.hash_id
                                                                )
                                                                    ? true
                                                                    : false
                                                            }
                                                        />
                                                        <span className="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td className="text-capitalize">
                                                    {contact.first_name}{" "}
                                                    {contact.last_name}
                                                </td>
                                                <td>{contact.email}</td>
                                                <td>
                                                    {contact.country_Code}{" "}
                                                    {contact.number}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td
                                                className="text-center"
                                                colSpan="4"
                                            >
                                                {t("No Contacts Found")}
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </Table>
                        </div>
                    </Modal.Body>
                    <Modal.Footer className="update-cancl-btn justify-content-center">
                        {contacts.length ? (
                            <Button
                                variant="primary"
                                onClick={handleClose}
                            >
                                <span>{t("Exclude/Update")}</span>
                            </Button>
                        ) : (
                            ""
                        )}
                    </Modal.Footer>
                </div>
            </Modal>

            {/* // Add Contacts Modal */}
            <Modal
                show={showAdd}
                onHide={handleCloseAdd}
                className="em-modal contact-modal smallmodal new-contact-modal"
                centered
            >
                <Modal.Header closeButton className="add-contact-modal em-table"></Modal.Header>
                <div>
                    <Modal.Body className="d-flex align-items-center justify-content-center flex-column">
                        <div className="mb-3 group-select-title w-100 text-center">
                            <span className="static-title">
                                {t("Add Contacts to This Campaign")}{" "}
                            </span>
                        </div>
                        <div className="mb-3 group-select-title w-100 text-center">
                            <Form
                                className="em-form campaign-new-contact mb-3 mb-md-2"
                                method="GET"
                            >
                                <Row>
                                    <Col lg="5" md="5">
                                        <Form.Group className="d-flex flex-row align-items-center">
                                            <Form.Label className="mb-0 me-3">
                                                {t("Select all Visible")}
                                            </Form.Label>
                                            <label className="custom-checkbox no-text mb-0">
                                                <input
                                                    className="form-checkbox"
                                                    type="checkbox"
                                                    onClick={(e) =>
                                                        handleAddAllContactsCheckbox(
                                                            e.target
                                                        )
                                                    }
                                                />
                                                <span className="checkmark"></span>
                                            </label>
                                        </Form.Group>
                                    </Col>

                                    <Col lg="7" md="7fAdd/Remove Contacts">
                                        <Form.Group className="d-flex flex-row align-items-center mbl-campaign-search">
                                            <Form.Label className="campaign-search-label">
                                                {t("Search Contact Name")}
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
                                        <th>{t("Contacts Name")}</th>
                                        <th>{t("Email")}</th>
                                        <th>{t("Mobile no.")}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {contactsAdd.length ? (
                                        contactsAdd.map((contact, index) => (
                                            <tr key={contact.hash_id}>
                                                <td>
                                                    <label className="custom-checkbox no-text me-0">
                                                        <input
                                                            className="form-checkbox"
                                                            type="checkbox"
                                                            onClick={() =>
                                                                handleAddContactCheckbox(
                                                                    contact.hash_id
                                                                )
                                                            }
                                                            defaultChecked={
                                                                addedContacts.includes(
                                                                    contact.hash_id
                                                                )
                                                                    ? true
                                                                    : false
                                                            }
                                                            id={
                                                                "n" +
                                                                contact.hash_id
                                                            }
                                                        />
                                                        <span className="checkmark"></span>
                                                    </label>
                                                </td>
                                                {/* <td>{(pageNumber - 1) * perPage + index + 1}</td> */}
                                                <td className="text-capitalize">
                                                    {contact.first_name}{" "}
                                                    {contact.last_name}
                                                </td>
                                                <td>{contact.email}</td>
                                                <td>
                                                    {contact.country_Code}{" "}
                                                    {contact.number}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td
                                                className="text-center"
                                                colSpan="4"
                                            >
                                                {t("No Contacts Found")}
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
                    </Modal.Body>
                    <Modal.Footer className="justify-content-center">
                        {contactsAdd.length ? (
                            <>
                                <Button
                                    variant="primary"
                                    onClick={handleCloseAdd}
                                >
                                    <span>{t("Add")}</span>
                                </Button>
                            </>
                        ) : (
                            ""
                        )}
                    </Modal.Footer>
                </div>
            </Modal>

            {/* show public templates Modal */}
            <Modal
                show={showTemplates}
                onHide={handleCloseTemplates}
                size="xl"
                className="existing-temp em-modal dlt-modal"
                centered
            >
                <Modal.Body className="d-flex align-items-center justify-content-center flex-column">
                    <h3>{t("Import Existing Template")} </h3>
                    <p>
                        {t("choose_the_template_that_you_would_like_to_import_and_edit")}
                    </p>
                    <Row className="row d-flex align-items-center _height">
                        {templates.length ? (
                            <>
                                {templates.map((template, index) => (
                                    <Col xl="4" sm="6" key={index}>
                                        <div className="template-holder mb-xl-0 mb-2">
                                            <div className="model-image image-holder img-holder-height">
                                                <img
                                                    className=" img-fluid"
                                                    src={template.image}
                                                    alt="template Image"
                                                />
                                                <ul className="action-list list-unstyled d-flex">
                                                    <li>
                                                        <a
                                                            onClick={() =>
                                                                HandleTemplateImport(
                                                                    template.hash_id
                                                                )
                                                            }
                                                            className="view-icon"
                                                            title={t("import")}
                                                        >
                                                            <FontAwesomeIcon
                                                                icon={
                                                                    faFileImport
                                                                }
                                                            />
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <strong className="template-name d-block text-center">
                                                            {template.name}
                                                        </strong>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </Col>
                                ))}
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
                        ) : (
                            <div className="d-flex justify-content-center ">
                                <h5>{t("You have No templates Yet")}</h5>
                            </div>
                        )}
                    </Row>
                </Modal.Body>
                <Modal.Footer className="justify-content-center">
                    <Button variant="secondary" onClick={handleCloseTemplates}>
                        <span>{t("Cancel")}</span>
                    </Button>
                </Modal.Footer>
            </Modal>
        </React.Fragment>
    );
}
export default withTranslation()(CreateEmailCampaign);
