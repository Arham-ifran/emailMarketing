import React, { Fragment, useState, useEffect } from "react";
import { Container, Row, Col, Form, Table } from "react-bootstrap";
import { Link } from "react-router-dom";
import { Modal, Button, Dropdown } from "react-bootstrap";
import Select from "react-select";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCheck } from "@fortawesome/free-solid-svg-icons";
import { faMinus } from "@fortawesome/free-solid-svg-icons";
import { faPencilAlt } from "@fortawesome/free-solid-svg-icons";
import { faTrashAlt } from "@fortawesome/free-regular-svg-icons";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import DateTimePicker from "react-datetime-picker";
import Moment from "react-moment";
import Spinner from "../../includes/spinner/Spinner";
import Pagination from "react-js-pagination";
import Swal from "sweetalert2";
import PhoneInput from "react-phone-number-input";
import moment from "moment-timezone";
import { withTranslation } from 'react-i18next';
import "./css/AllContacts.css";

function AllContacts(props) {
    const { t } = props;
    const [contacts, setContacts] = useState([]);
    const [deleting, setDeleting] = useState("");
    const [newContacts, setNewContacts] = useState(0);
    const [totalCotacts, setTotalContacts] = useState(0);
    const [deletedCotacts, setDeletedContacts] = useState(0);
    const [subscribedCotacts, setSubscribedContacts] = useState(0);
    const [pageNumber, setPageNumber] = useState(new URLSearchParams(location.search).get('page') ? parseInt(new URLSearchParams(location.search).get('page')) : 1);
    const [perPage, setperPage] = useState(0);
    const [totalItems, setTotalItems] = useState(0);
    const [pageRange, setPageRange] = useState(5);
    const [filterName, setfilterName] = useState("");
    const [filterEmail, setfilterEmail] = useState("");
    const [filterNumber, setfilterNumber] = useState("");
    const [filterCreated, setfilterCreated] = useState("");
    const [filterUpdated, setfilterUpdated] = useState("");
    const [filter, setFilter] = useState(0);
    const [selectedOption, setSelectedOption] = useState("");
    const [selectedOption2, setSelectedOption2] = useState("");
    const [editContacts, setEditContacts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [options2, setOptions2] = useState([]);
    const [disabledForemail, setdisabledForemail] = useState(0);
    const [disabledForsms, setdisabledForsms] = useState(0);

    const handleChange2 = (selectedOption) => {
        setdisabledForemail(0);
        setdisabledForsms(0);
        setLoading(true);
        setSelectedOption2(selectedOption.value);
        // in a loop
        // var changedcontacts = [...editContacts]
        editContacts.forEach((contact, index) => {
            if (selectedOption.email && selectedOption.email == 1) {
                contact.for_email = 1;
            }
            if (selectedOption.sms && selectedOption.sms == 1) {
                contact.for_sms = 1;
            }
        });
        setEditContacts(editContacts);
        if (selectedOption.email && selectedOption.email == 1) {
            setdisabledForemail(1);
        }
        if (selectedOption.sms && selectedOption.sms == 1) {
            setdisabledForsms(1);
        }
        setLoading(false);
    };

    const getContacts = () => {
        setLoading(true);
        setErrors([]);
        const created = (filterCreated != '' ? moment.tz(filterCreated + " 12:00", localStorage.timezone).utc().format('YYYY-MM-DD') : '');
        const updated = (filterUpdated != '' ? moment.tz(filterUpdated + " 12:00", localStorage.timezone).utc().format('YYYY-MM-DD') : '');
        axios
            .get(
                "/api/get-contacts" +
                "?page=" +
                pageNumber +
                "&name=" +
                filterName +
                "&email=" +
                filterEmail +
                "&number=" +
                filterNumber +
                "&created=" +
                created +
                "&updated=" +
                updated +
                "&lang=" + localStorage.lang
            )
            .then((response) => {
                if (!$.trim(response.data.data) && pageNumber !== 1) {
                    setPageNumber(pageNumber - 1);
                    setFilter(!filter);
                }
                setContacts(response.data.data);
                setperPage(response.data.meta.per_page);
                setTotalItems(response.data.meta.total);
                setLoading(false);
                // console.log(contacts);
            })
            .catch((error) => {
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.errors
                ) {
                    setErrors(error.response.data.errors);
                    setLoading(false);
                }
            });
        setLoading(true);
        axios
            .post("/api/get-contacts-info?lang=" + localStorage.lang)
            .then((response) => {
                setNewContacts(response.data.new);
                setTotalContacts(response.data.total);
                setDeletedContacts(response.data.deleted);
                setSubscribedContacts(response.data.subscribed);
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
        setLoading(true);
        const getMailingLists = () => {
            axios
                .get("/api/get-all-groups?lang=" + localStorage.lang)
                .then((response) => {
                    setLoading(false);
                    const data = [
                        // {
                        //     value: "",
                        //     label: t("add later"),
                        // },
                    ];
                    const moredata = [
                        response.data.data.map((row) => ({
                            value: row.hash_id,
                            sms: row.for_sms,
                            email: row.for_email,
                            label: row.name,
                        })),
                    ];
                    const options = data.concat(moredata[0]);
                    setOptions2(options);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                    setLoading(false);
                });
        };
        getMailingLists();
        getContacts();
    }, [pageNumber, filter]);

    const options = [
        // { value: "view", label: "View" },
        { value: "edit", label: t("Edit") },
        { value: "del", label: t("Delete") },
    ];

    const handleChange = (selectedOption) => {
        setSelectedOption(selectedOption.value);
        if (selectedOption.value == "del") {
            var inputs = document.getElementsByTagName("input");
            let first = 1;
            var ids = [];
            for (var i = 1; i < inputs.length; i++) {
                if (inputs[i].type == "checkbox") {
                    if (first) {
                        first = 0;
                    } else {
                        if (inputs[i].checked) {
                            ids.push(inputs[i].className);
                        }
                    }
                }
            }
            if (ids.length)
                handleShowbd();
            else
                handleShowbe();

        } else if (selectedOption.value == "edit") {
            handleShowbe();
        }
    };

    // delete modal
    const [show, setShow] = useState(false);
    const [showbulkDel, setShowBulkDel] = useState(false);
    const [showbulkEdit, setShowBulkEdit] = useState(false);
    const handleClose = () => {
        setShow(false);
        setDeleting("");
    };
    const handleShow = (e) => {
        setDeleting(e.target.closest("button").id);
        setShow(true);
    };
    const handleClosebd = () => {
        setShowBulkDel(false);
    };
    const handleShowbd = () => {
        setShowBulkDel(true);
    };
    const handleClosebe = () => {
        setEditContacts([]);
        setShowBulkEdit(false);
        setSelectedOption2("");
        setdisabledForemail(0);
        setdisabledForsms(0);
    };
    const handleShowbe = () => {
        editSelectedRows();
        setShowBulkEdit(true);
    };

    const handleContactDelete = () => {
        const num = deleting;
        setLoading(true);
        if (num) {
            del([num]);
            handleClose();
        } else {
            // console.log("id not received");
        }
    };

    // date range picker
    const [selectedDate, setSelectedDate] = useState();
    const handleCalenderChange = (date) => {
        if (!(moment(moment(date).format('YYYY-MM-DD'), 'YYYY-MM-DD', true).isValid())) {
            // setErrors({
            //     invalid_format_1: [t("invalid_format")],
            // });
            setfilterCreated("");
            setSelectedDate(null);
        }
        else {
            setfilterCreated(moment(date).format("YYYY-MM-DD"));
            setSelectedDate(date);
        }
    };

    const [selectedDate2, setSelectedDate2] = useState();
    const handleCalenderChange2 = (date) => {
        if (!(moment(moment(date).format('YYYY-MM-DD'), 'YYYY-MM-DD', true).isValid())) {
            // setErrors({
            //     invalid_format_2: [t("invalid_format")],
            // });
            setfilterUpdated("");
            setSelectedDate2(null);
        }
        else {
            setfilterUpdated(moment(date).format("YYYY-MM-DD"));
            setSelectedDate2(date);
        }
    };

    const clearFilter = async () => {
        if (filterName != "") setfilterName("");
        if (filterEmail != "") setfilterEmail("");
        if (filterNumber != "") setfilterNumber("");
        if (filterCreated != "") setfilterCreated("");
        if (filterUpdated != "") setfilterUpdated("");
        setSelectedDate(null);
        setSelectedDate2(null);
        setFilter(!filter);
    };

    // check or uncheck all function
    const selectAll = (e) => {
        var inputs = document.getElementsByTagName("input");
        let check = 0;
        let first = 1;
        for (var i = 0, max = inputs.length; i < max; i++) {
            if (inputs[i].type == "checkbox") {
                if (first) {
                    check = inputs[i].checked;
                    first = 0;
                } else {
                    if (check) inputs[i].checked = true;
                    else inputs[i].checked = false;
                }
            }
        }
    };

    const delSelectedRows = () => {
        var inputs = document.getElementsByTagName("input");
        let first = 1;

        var ids = [];
        for (var i = 1; i < inputs.length; i++) {
            if (inputs[i].type == "checkbox") {
                if (first) {
                    first = 0;
                } else {
                    if (inputs[i].checked) {
                        ids.push(inputs[i].className);
                    }
                }
            }
        }
        // console.log(ids);
        del(ids);
        handleClosebd();
    };

    const del = (ids) => {
        setLoading(true);
        setErrors([]);

        axios
            .post("/api/delete-contacts?lang=" + localStorage.lang, { id: ids })
            .then((response) => {
                // console.log(response.data);
                getContacts();
                setLoading(false);
                Swal.fire({
                    title: t("Success"),
                    text: (ids.length > 1
                        ? t("Your Contacts have been deleted successfully!")
                        : t("Your Contact have been deleted successfully!"))
                        + "\n " + t('but_a_contact_is_not_deleted_if_it_is_included_in_an_active_campaign'),
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonText: t('OK')
                    //cancelButtonText: 'No, keep it'
                });
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                setLoading(false);
            });
    };

    // const {editcontacts, setEditContacts}= setState([]);
    const [errors, setErrors] = useState([]);

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

    const editSelectedRows = () => {
        var conts = [];
        var inputs = document.getElementsByTagName("input");
        let first = 1;

        for (var i = 1; i < inputs.length; i++) {
            if (inputs[i].type == "checkbox") {
                if (first) {
                    first = 0;
                } else {
                    if (inputs[i].checked) {
                        // console.log(inputs[i].id);
                        conts.push({ ...contacts[inputs[i].id] });
                    }
                }
            }
        }
        // console.log(conts);
        setEditContacts(conts);
    };

    const handleGroupAdd = (selectedOption) => {
        setSelectedOption2(selectedOption.value);
        var ids = [];
        var inputs = document.getElementsByTagName("input");
        let first = 1;

        var ids = [];
        for (var i = 1; i < inputs.length; i++) {
            if (inputs[i].type == "checkbox") {
                if (first) {
                    first = 0;
                } else {
                    if (inputs[i].checked) {
                        ids.push(inputs[i].className);
                    }
                }
            }
        }
        // console.log(ids);
        if (ids.length)
            handleAdd(ids, selectedOption.value);
        else
            handleShowbe();
    };

    const handleFname = (e) => {
        const change = e.target.value;
        const index = e.target.attributes.from.value;
        var changedContact = { ...editContacts[index] };
        changedContact.first_name = change;
        var newone = [...editContacts];
        newone[index] = changedContact;
        setEditContacts(newone);
    };
    const handleLname = (e) => {
        const change = e.target.value;
        const index = e.target.attributes.from.value;
        var changedContact = { ...editContacts[index] };
        changedContact.last_name = change;
        var newone = [...editContacts];
        newone[index] = changedContact;
        setEditContacts(newone);
    };

    const handleCountry = (e) => {
        const change = e.target.value;
        const index = e.target.attributes.from.value;
        var changedContact = { ...editContacts[index] };
        changedContact.country_code = change;
        var newone = [...editContacts];
        newone[index] = changedContact;
        setEditContacts(newone);
    };

    const handleNum = (val, ind) => {
        const change = val;
        const index = ind;
        var changedContact = { ...editContacts[index] };
        changedContact.number = change;
        var newone = [...editContacts];
        newone[index] = changedContact;
        setEditContacts(newone);
    };

    const handleEmail = (e) => {
        const change = e.target.value;
        const index = e.target.attributes.from.value;
        var changedContact = { ...editContacts[index] };
        changedContact.email = change;
        var newone = [...editContacts];
        newone[index] = changedContact;
        setEditContacts(newone);
    };

    const handleDoi = (e) => {
        let change = 0;
        if (e.target.checked) {
            change = 1;
        } else {
            change = 0;
        }
        const index = e.target.attributes.from.value;
        var changedContact = { ...editContacts[index] };
        changedContact.double_opt_in = change;
        var newone = [...editContacts];
        newone[index] = changedContact;
        setEditContacts(newone);
    };

    const handleForemail = (e) => {
        let change = 0;
        if (e.target.checked) {
            change = 1;
        } else {
            change = 0;
        }
        const index = e.target.attributes.from.value;
        const changedContact = { ...editContacts[index] };
        changedContact.for_email = change;
        var newone = [...editContacts];
        newone[index] = changedContact;
        setEditContacts(newone);
    };

    const handleForsms = (e) => {
        let change = 0;
        if (e.target.checked) {
            change = 1;
        } else {
            change = 0;
        }
        const index = e.target.attributes.from.value;
        const changedContact = { ...editContacts[index] };
        changedContact.for_sms = change;
        var newone = [...editContacts];
        newone[index] = changedContact;
        setEditContacts(newone);
    };

    const handleSubmit = async (event) => {
        setLoading(true);
        setErrors([]);
        var ids = [];
        var first_names = [];
        var last_names = [];
        var emails = [];
        var country_codes = [];
        var numbers = [];
        var double_opt_ins = [];
        var for_smses = [];
        var for_emails = [];
        editContacts.forEach((contact) => {
            ids.push(contact.hash_id);

            if (contact.first_name) {
                first_names.push(contact.first_name);
            } else {
                first_names.push("");
            }

            if (contact.last_name) {
                last_names.push(contact.last_name);
            } else {
                last_names.push("");
            }

            if (contact.email) {
                emails.push(contact.email);
            } else {
                emails.push("");
            }

            if (contact.country_code) {
                country_codes.push(contact.country_code);
            } else {
                country_codes.push("");
            }

            if (contact.number) {
                numbers.push(contact.number);
            } else {
                numbers.push("");
            }

            if (contact.double_opt_in) {
                double_opt_ins.push(contact.double_opt_in);
            } else {
                double_opt_ins.push(0);
            }

            if (contact.for_email) {
                for_emails.push(contact.for_email);
            } else {
                for_emails.push(0);
            }

            if (contact.for_sms) {
                for_smses.push(contact.for_sms);
            } else {
                for_smses.push(0);
            }
        });

        if (first_names == [] && last_names == []) {
            setErrors({
                first_name: [
                    first_names == [] ? t("This field is required.") : "",
                ],
                last_name: [last_names == [] ? t("This field is required.") : ""],
            });
            setLoading(false);
            return;
        }

        const data = {
            id: ids,
            first_name: first_names,
            last_name: last_names,
            email: emails,
            country_code: country_codes,
            number: numbers,
            double_opt_in: double_opt_ins,
            for_sms: for_smses,
            for_email: for_emails,
        };
        if (selectedOption2 != "") {
            data.list = selectedOption2;
        }
        await axios
            .post("/api/edit-contacts?lang=" + localStorage.lang, data)
            .then((response) => {
                // console.log(response.data);
                // console.log("contacts edited");
                getContacts();
                handleClosebe();

                setLoading(false);
                Swal.fire({
                    title: t("Success"),
                    text: t("Your Contacts have been updated successfully!"),
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonText: t('OK')
                    //cancelButtonText: 'No, keep it'
                });
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                setLoading(false);
            });
        // handleAdd(ids);
    };

    const handleAdd = (ids, selectedGroup) => {
        axios
            .post("/api/add-to-group?lang=" + localStorage.lang, { contact: ids, group: selectedGroup })
            .then((response) => {
                getContacts();
                setSelectedOption2("");
                Swal.fire({
                    title: t("Success"),
                    text: t("Selected contacts have been added to Mailing list successfully!"),
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonText: t('OK')
                    //cancelButtonText: 'No, keep it'
                }).then((result) => {
                    // window.location.href = "/mailing-lists";
                });
            })
            .catch((error) => {
                Swal.fire({
                    title: t("Success"),
                    text: t("Selected contacts have been added to Mailing list successfully!"),
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonText: t('OK')
                    //cancelButtonText: 'No, keep it'
                }).then((result) => {
                    // window.location.href = "/mailing-lists";
                });
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                setLoading(false);
            });
    };

    return (
        <React.Fragment>
            {loading ? <Spinner /> : null}
            <section className="right-canvas email-campaign">
                <Container fluid>
                    <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                        <div className="page-title">
                            <h1>{t('All Contacts')}</h1>
                        </div>
                        <div className="add-contact-dropdown">
                            <Dropdown>
                                <Dropdown.Toggle
                                    variant="success"
                                    id="dropdown-basic"
                                >
                                    <span className="btn btn-secondary">
                                        <span>
                                            {t('Add New')}{" "}
                                            <FontAwesomeIcon
                                                icon={faPlus}
                                                className="ms-2"
                                            />
                                        </span>
                                    </span>
                                </Dropdown.Toggle>
                                <Dropdown.Menu>
                                    <ul className="list-unstyled sub-menu">
                                        <li>
                                            <Link to="/contacts/create">
                                                {t('Single Contact')}
                                            </Link>
                                        </li>
                                        <li>
                                            <Link to="/contacts/add-multiple">
                                                {t('Multiple Contacts')}
                                            </Link>
                                        </li>
                                    </ul>
                                </Dropdown.Menu>
                            </Dropdown>
                        </div>
                    </div>
                    <Row>
                        <Col xxl="3" xl="3" lg="6" md="6" sm="6" xs="12">
                            <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                <span className="title">{t('Total Contacts')}</span>
                                <span className="value">{totalCotacts}</span>
                            </div>
                        </Col>
                        <Col xxl="3" xl="3" lg="6" md="6" sm="6" xs="12">
                            <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                <span className="title">{t('Subscribed')}</span>
                                <span className="value">{subscribedCotacts}</span>
                            </div>
                        </Col>
                        <Col xxl="3" xl="3" lg="6" md="6" sm="6" xs="12">
                            <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                <span className="title">{t('new contacts')}</span>
                                <span className="value">{newContacts}</span>
                            </div>
                        </Col>
                        <Col xxl="3" xl="3" lg="6" md="6" sm="6" xs="12">
                            <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                <span className="title">{t('deleted')}</span>
                                <span className="value">{deletedCotacts}</span>
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
                                <Col lg="4" md="12" xs="12">
                                    <Form.Group className="mb-3 mb-md-4 d-flex flex-column">
                                        <Form.Label>{t('Contact Name')}</Form.Label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            value={filterName}
                                            onChange={(e) =>
                                                setfilterName(e.target.value)
                                            }
                                            placeholder={t('e.g. Sam (without space)')}
                                        />
                                    </Form.Group>
                                </Col>
                                <Col lg="4" md="12" xs="12">
                                    <Form.Group className="mb-3 mb-md-4 d-flex flex-column">
                                        <Form.Label>{t('Email')}</Form.Label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            value={filterEmail}
                                            onChange={(e) =>
                                                setfilterEmail(e.target.value)
                                            }
                                            placeholder="e.g. example@email.com"
                                        />
                                    </Form.Group>
                                </Col>
                                <Col lg="4" md="12" xs="12">
                                    <Form.Group className="mb-3 mb-md-4 d-flex flex-column">
                                        <Form.Label>{t('Contact Number')}</Form.Label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            value={filterNumber}
                                            onChange={(e) =>
                                                setfilterNumber(e.target.value)
                                            }
                                            placeholder="e.g. +49 1579230198"
                                        />
                                    </Form.Group>
                                </Col>
                                <Col lg="4" md="12" xs="12">
                                    <Form.Group className="mb-3 mb-md-4 d-flex flex-column">
                                        <Form.Label>{t('Date Created')}</Form.Label>
                                        <DateTimePicker
                                            format="y-MM-dd"
                                            className="em-calendar w-100"
                                            onChange={(e) => handleCalenderChange(e)}
                                            value={selectedDate}
                                        />
                                        {renderErrorFor("invalid_format_1")}
                                    </Form.Group>
                                </Col>
                                <Col lg="4" md="12" xs="12">
                                    <Form.Group className="mb-3 mb-md-4 d-flex flex-column">
                                        <Form.Label>{t('Last Modified')}</Form.Label>
                                        <DateTimePicker
                                            format="y-MM-dd"
                                            className="em-calendar w-100"
                                            onChange={(e) => handleCalenderChange2(e)}
                                            value={selectedDate2}
                                        />
                                        {renderErrorFor("invalid_format_2")}
                                    </Form.Group>
                                </Col>
                                <Col
                                    xl="4"
                                    xs="12"
                                    className="d-flex justify-content-md-end"
                                >
                                    <Form.Group className="btn-wrapper filter-btns mt-4 mb-4 d-flex flex-column">
                                        <div className="d-flex justify-content-between">
                                            <button
                                                type="button"
                                                className="btn btn-primary"
                                                onClick={() => { if (!hasErrorFor('invalid_format_1') || !hasErrorFor('invalid_format_2')) { getContacts() } }}
                                            >
                                                <span>{t('Apply')}</span>
                                            </button>
                                            <button
                                                type="button"
                                                className="btn btn-secondary"
                                                onClick={() => clearFilter()}
                                            >
                                                <span>{t('Reset')}</span>
                                            </button>
                                        </div>
                                    </Form.Group>
                                </Col>
                            </Row>
                        </Form>

                        <div className="status-table">
                            <Row>
                                <Col xl="4" lg="6" md="5" xs="12">
                                    <Form.Group className="mb-3 mb-md-4">
                                        <Select
                                            onChange={(e) => handleChange(e)}
                                            options={options}
                                            classNamePrefix="react-select"
                                            placeholder={t("Bulk Action")}
                                        />
                                    </Form.Group>
                                </Col>
                                <Col xl="4" lg="6" md="5" xs="12">
                                    <Form.Group className="mb-3 mb-md-4">
                                        <Select
                                            onChange={(e) => handleGroupAdd(e)}
                                            options={options2}
                                            classNamePrefix="react-select"
                                            placeholder={t("Add Contacts to Mailing List")}
                                        />
                                    </Form.Group>
                                </Col>
                            </Row>
                            <div className="table-responsive">
                                <Table className="em-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>
                                                <label className="custom-checkbox no-text me-0">
                                                    <input
                                                        className="form-checkbox"
                                                        type="checkbox"
                                                        onClick={selectAll}
                                                    />
                                                    <span className="checkmark"></span>
                                                </label>
                                            </th>

                                            <th>{t('Sr.')}</th>
                                            <th>{t('Contact Name')}</th>
                                            <th>{t('Email')}</th>
                                            <th>{t('Mobile no.')}</th>
                                            <th>{t('For Email')}</th>
                                            <th>{t('For SMS')}</th>

                                            <th>{t('Date Created')}</th>
                                            <th>{t('Last Modified')}</th>
                                            <th>{t('Mailing List')}</th>
                                            <th>{t('Action')}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {contacts.length ? (
                                            contacts.map((contact, index) => (
                                                <tr key={contact.hash_id}>
                                                    <td>
                                                        <label className="custom-checkbox no-text me-0">
                                                            <input
                                                                className="form-checkbox"
                                                                type="checkbox"
                                                                id={index}
                                                                className={
                                                                    contact.hash_id
                                                                }
                                                            />
                                                            <span className="checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        {(pageNumber - 1) *
                                                            perPage +
                                                            index +
                                                            1}
                                                    </td>
                                                    <td className="text-capitalize">
                                                        {contact.first_name}{" "}
                                                        {contact.last_name}
                                                    </td>
                                                    <td> {contact.email} </td>
                                                    <td>
                                                        {contact.country_Code}{" "}
                                                        {contact.number}
                                                    </td>
                                                    <td className="text-center">
                                                        {contact.for_email == 1 ?
                                                            <FontAwesomeIcon icon={faCheck} />
                                                            :
                                                            <FontAwesomeIcon icon={faMinus} />

                                                        }
                                                    </td>
                                                    <td className="text-center">
                                                        {contact.for_sms == 1 ?
                                                            <FontAwesomeIcon icon={faCheck} />
                                                            :
                                                            <FontAwesomeIcon icon={faMinus} />
                                                        }
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
                                                            (row) => (
                                                                <span
                                                                    className="badge badge-secondary"
                                                                    key={row.id}
                                                                >
                                                                    {row.name}
                                                                </span>
                                                            )
                                                        )}
                                                    </td>
                                                    <td>
                                                        <ul className="action-icons list-unstyled">
                                                            <li>
                                                                <Link
                                                                    to={
                                                                        "/contacts/" +
                                                                        contact.hash_id + "?page=" + pageNumber
                                                                    }
                                                                    className="edit-icon"
                                                                    title={t("Edit")}
                                                                >
                                                                    <FontAwesomeIcon
                                                                        icon={
                                                                            faPencilAlt
                                                                        }
                                                                    />
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <button
                                                                    className="dlt-icon"
                                                                    title={t("Delete")}
                                                                    ariant="primary"
                                                                    id={
                                                                        contact.hash_id
                                                                    }
                                                                >
                                                                    <FontAwesomeIcon
                                                                        icon={
                                                                            faTrashAlt
                                                                        }
                                                                        onClick={
                                                                            handleShow
                                                                        }
                                                                    />
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td
                                                    className="text-center"
                                                    colSpan="11"
                                                >
                                                    {t('No Contacts Found')}
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
                </Container>
            </section>

            {/* Delete Modal */}
            <Modal
                show={show}
                onHide={handleClose}
                className="em-modal dlt-modal new-contact-modal"
                centered
            >
                <Modal.Header closeButton className="add-contact-modal em-table"></Modal.Header>
                <Modal.Body className="d-flex align-items-center justify-content-center flex-column">
                    <span className="dlt-icon">
                        <FontAwesomeIcon icon={faTrashAlt} />
                    </span>
                    <p>{t('Are you sure you want to delete Contact?')}</p>
                </Modal.Body>
                <Modal.Footer className="justify-content-center">
                    <Button variant="primary" onClick={handleContactDelete}>
                        <span>{t('Yes')}</span>
                    </Button>
                    <Button variant="secondary" onClick={handleClose}>
                        <span>{t('Cancel')}</span>
                    </Button>
                </Modal.Footer>
            </Modal>

            {/* Bulk Delete Modal */}
            <Modal
                show={showbulkDel}
                onHide={handleClosebe}
                className="em-modal bulk-dlt-modal"
                centered
            >
                <Modal.Header closeButton className="add-contact-modal em-table"></Modal.Header>
                <Modal.Body className="d-flex align-items-center justify-content-center flex-column">
                    <span className="dlt-icon">
                        <FontAwesomeIcon icon={faTrashAlt} />
                    </span>
                    <p>
                        {t('Are you sure you want to delete ALL selected Contacts?')}
                    </p>
                </Modal.Body>
                <Modal.Footer className="justify-content-center">
                    <Button variant="primary" onClick={delSelectedRows}>
                        <span>{t('Yes')}</span>
                    </Button>
                    <Button variant="secondary" onClick={handleClosebd}>
                        <span>{t('Cancel')}</span>
                    </Button>
                </Modal.Footer>
            </Modal>

            {/* Bulk Delete Modal */}
            <Modal
                show={showbulkEdit}
                onHide={handleClosebe}
                className="em-modal bulk-edit-modal"
            >
                <Modal.Header closeButton className="add-contact-modal em-table"></Modal.Header>
                <Modal.Body className="d-flex align-items-center justify-content-center flex-column">
                    {/* Bulk edit starts */}

                    <Container fluid>
                        {editContacts.length ? (
                            <Row>
                                <Col>
                                    <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                                        <div className="page-title">
                                            <h1>{t('Edit Multiple Contacts')}</h1>
                                        </div>
                                    </div>
                                    <div className="create-form-holder multiple-form-holder bg-white rounded-box-shadow mb-3 mb-md-4">
                                        <div className="multiple-contact">
                                            {editContacts.map(
                                                (contact, index) => (
                                                    <span
                                                        className="indent"
                                                        key={contact.hash_id}
                                                    >
                                                        <Row className="multiple-contact-row">
                                                            <Col
                                                                xl="6"
                                                                md="12"
                                                                sm="12"
                                                            >
                                                                <Form.Group className="mb-3 mb-md-4 d-flex">
                                                                    <Form.Label htmlFor="first-name">
                                                                        {t('First Name')}{" "}
                                                                        <b className="req-sign">
                                                                            *
                                                                        </b>
                                                                    </Form.Label>
                                                                    <div className="flex-fill input-holder">
                                                                        <input
                                                                            name="first_name"
                                                                            value={
                                                                                contact.first_name
                                                                            }
                                                                            className="form-control"
                                                                            type="text"
                                                                            from={
                                                                                index
                                                                            }
                                                                            onChange={(
                                                                                e
                                                                            ) =>
                                                                                handleFname(
                                                                                    e
                                                                                )
                                                                            }
                                                                        />
                                                                        {renderErrorFor(
                                                                            "first_name." +
                                                                            index
                                                                        )}
                                                                        {renderErrorFor(
                                                                            "first_name"
                                                                        )}
                                                                    </div>
                                                                </Form.Group>
                                                            </Col>
                                                            <Col
                                                                xl="6"
                                                                md="12"
                                                                sm="12"
                                                            >
                                                                <Form.Group className="mb-3 mb-md-4 d-flex">
                                                                    <Form.Label htmlFor="last-name">
                                                                        {t('Last Name')}{" "}
                                                                        <b className="req-sign">
                                                                            *
                                                                        </b>
                                                                    </Form.Label>
                                                                    <div className="flex-fill input-holder">
                                                                        <input
                                                                            name="last_name"
                                                                            value={
                                                                                contact.last_name
                                                                            }
                                                                            className="form-control"
                                                                            type="text"
                                                                            from={
                                                                                index
                                                                            }
                                                                            onChange={(
                                                                                e
                                                                            ) =>
                                                                                handleLname(
                                                                                    e
                                                                                )
                                                                            }
                                                                        />
                                                                        {renderErrorFor(
                                                                            "last_name." +
                                                                            index
                                                                        )}
                                                                        {renderErrorFor(
                                                                            "last_name"
                                                                        )}
                                                                    </div>
                                                                </Form.Group>
                                                            </Col>
                                                            <Col
                                                                xl="6"
                                                                md="12"
                                                                sm="12"
                                                            >
                                                                <Form.Group className="mb-3 mb-md-4 d-flex">
                                                                    <Form.Label htmlFor="email">
                                                                        {t('Email')}
                                                                    </Form.Label>
                                                                    <div className="flex-fill input-holder">
                                                                        <input
                                                                            name="email"
                                                                            value={
                                                                                contact.email
                                                                            }
                                                                            className="form-control"
                                                                            type="text"
                                                                            from={
                                                                                index
                                                                            }
                                                                            onChange={(
                                                                                e
                                                                            ) =>
                                                                                handleEmail(
                                                                                    e
                                                                                )
                                                                            }
                                                                        />
                                                                        {renderErrorFor(
                                                                            "email." +
                                                                            index
                                                                        )}
                                                                    </div>
                                                                </Form.Group>
                                                            </Col>
                                                            <Col
                                                                xl="6"
                                                                md="12"
                                                                sm="12"
                                                            >
                                                                <Form.Group className="mb-3 mb-md-4 d-flex">
                                                                    <Form.Label htmlFor="country">
                                                                        {t('Phone Number')}
                                                                    </Form.Label>
                                                                    <div className="flex-fill input-holder  flex-column">
                                                                        <PhoneInput
                                                                            placeholder={t('Enter phone number')}
                                                                            onChange={(
                                                                                number
                                                                            ) =>
                                                                                handleNum(
                                                                                    number,
                                                                                    index
                                                                                )
                                                                            }
                                                                            name="number"
                                                                            value={
                                                                                contact.number
                                                                            }
                                                                            className="form-control"
                                                                            from={
                                                                                index
                                                                            }
                                                                        />
                                                                        {renderErrorFor(
                                                                            "number." +
                                                                            index
                                                                        )}
                                                                    </div>
                                                                </Form.Group>
                                                            </Col>
                                                            <Col
                                                                xl="12"
                                                                md="12"
                                                                sm="12"
                                                                className="multi-form-edit-detail"
                                                            >
                                                                <Form.Group className="mb-3 mb-md-4 d-flex">
                                                                    <Form.Label className="form-label">
                                                                        {t('For')}{" "}
                                                                        <b className="req-sign">
                                                                            *
                                                                        </b>
                                                                    </Form.Label>
                                                                    <div className="d-flex flex-row align-items-center justify-content-start flex-fill input-holder sms-email-wrap">
                                                                        <Form.Label>
                                                                            {t('SMS')}
                                                                        </Form.Label>
                                                                        <input
                                                                            name="for_sms"
                                                                            className="form-checkbox"
                                                                            type="checkbox"
                                                                            disabled={
                                                                                disabledForsms
                                                                            }
                                                                            checked={
                                                                                contact.for_sms ==
                                                                                    1 ||
                                                                                    contact.for_sms ==
                                                                                    "1"
                                                                                    ? true
                                                                                    : false
                                                                            }
                                                                            from={
                                                                                index
                                                                            }
                                                                            onClick={(
                                                                                e
                                                                            ) =>
                                                                                handleForsms(
                                                                                    e
                                                                                )
                                                                            }
                                                                        />
                                                                        <Form.Label>
                                                                            {t('Email')}
                                                                        </Form.Label>
                                                                        <input
                                                                            name="for_email"
                                                                            className="form-checkbox"
                                                                            type="checkbox"
                                                                            disabled={
                                                                                disabledForemail
                                                                            }
                                                                            checked={
                                                                                contact.for_email ==
                                                                                    1 ||
                                                                                    contact.for_email ==
                                                                                    "1"
                                                                                    ? true
                                                                                    : false
                                                                            }
                                                                            from={
                                                                                index
                                                                            }
                                                                            onClick={(
                                                                                e
                                                                            ) =>
                                                                                handleForemail(
                                                                                    e
                                                                                )
                                                                            }
                                                                        />
                                                                        {renderErrorFor(
                                                                            "for_sms." +
                                                                            index
                                                                        )}
                                                                        {renderErrorFor(
                                                                            "for_email." +
                                                                            index
                                                                        )}
                                                                        {renderErrorFor(
                                                                            "for." +
                                                                            index
                                                                        )}
                                                                    </div>
                                                                </Form.Group>
                                                            </Col>
                                                            <Col sm="12">
                                                                <hr />
                                                            </Col>
                                                        </Row>
                                                    </span>
                                                )
                                            )}
                                        </div>
                                    </div>
                                    <div className="mutli-contct-list-holder">
                                        <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                                            <Form.Label
                                                className="mb-2 mb-md-0"
                                                forhtml="title"
                                            >
                                                {t('Add Contacts to Mailing List')}
                                            </Form.Label>
                                            <div className="flex-fill input-holder">
                                                <div className="mutli-contct-list-select">
                                                    <Select
                                                        onChange={(e) =>
                                                            handleChange2(e)
                                                        }
                                                        options={options2}
                                                        classNamePrefix="react-select"
                                                        placeholder={t("Select Mailing List")}
                                                    />
                                                </div>
                                            </div>
                                        </Form.Group>
                                    </div>
                                </Col>
                            </Row>
                        ) : (
                            <div>
                                <h4 className="text-center">
                                    {t('No contacts selected')}
                                </h4>
                            </div>
                        )}
                    </Container>

                    {/* Bulk edit ends */}
                </Modal.Body>
                <Modal.Footer className="justify-content-end">
                    {editContacts.length ? (
                        <Button variant="primary" onClick={handleSubmit}>
                            <span>Save</span>
                        </Button>
                    ) : (
                        ""
                    )}
                    <Button variant="secondary" onClick={handleClosebe}>
                        <span>{t('Cancel')}</span>
                    </Button>
                </Modal.Footer>
            </Modal>
        </React.Fragment>
    );
}

export default withTranslation()(AllContacts);
