import { isEmpty } from "lodash";
import React, { useState, useEffect } from "react";
import { Container, Row, Col, Form, Modal, Button } from "react-bootstrap";
import { Link, useHistory, useParams } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { faMinus } from "@fortawesome/free-solid-svg-icons";
import { faLongArrowAltDown } from "@fortawesome/free-solid-svg-icons";
import Select from "react-select";
import Spinner from "../../includes/spinner/Spinner";
import "./css/AddMultipleContact.css";
import PhoneInput from 'react-phone-number-input'
import Swal from 'sweetalert2';
import { react } from "@babel/types";
import { withTranslation } from 'react-i18next';
import axios from "axios";

function AddMultipleContact(props) {
    const { t } = props;
    const history = useHistory();
    const [options, setOptions] = useState([]);
    const [selectedOption, setSelectedOption] = useState("");
    const [contacts, setContacts] = useState([{}]);
    const [loading, setLoading] = useState(false);
    const [file, setFile] = useState({});
    const [errors, setErrors] = useState([]);
    const [disabledForemail, setdisabledForemail] = useState(0);
    const [disabledForsms, setdisabledForsms] = useState(0);
    const [canSendFile, setCanSendFile] = useState(false);
    const [fields, setfields] = useState([]);
    const [selectedfields, setSelectedfields] = useState({
        first_name: "",
        last_name: "",
        for_sms: "",
        for_email: "",
        number: "",
        email: "",
    });
    const [usage, setUsage] = useState(0); //0 = none , 1 = sms , 2 = email
    const [limit, setLimit] = useState('');
    const [contactsLimit, setContactsLimit] = useState(0);

    const [SmsCamp, setSmsCamp] = useState("");
    const [EmailCamp, setEmailCamp] = useState("");
    const [SplitCamp, setSplitCamp] = useState("");
    const [mailingList, setMailingList] = useState(false);

    const goBack = () => {
        if (mailingList) {
            if (mailingList == 0)
                window.location.href = "/mailing-lists/create";
            else
                window.location.href = "/mailing-lists/" + mailingList + "/edit";
        }
        else if (SmsCamp) {
            window.location.href = "/sms-campaign/" + SmsCamp + "/edit";
        }
        else if (EmailCamp) {
            window.location.href = "/email-campaign/" + EmailCamp + "/edit";
        }
        else if (SplitCamp) {
            window.location.href = "/split-testing/" + SplitCamp + "/edit";
        }
        else {
            window.location.href = "/contacts";
        }
    }

    const handleChange = (selectedOption) => {
        setSelectedOption(selectedOption.value);
        setdisabledForemail(0);
        setdisabledForsms(0);
        // in a loop 
        contacts.forEach((contact) => {
            if (selectedOption.email && selectedOption.email == 1) {
                contact.for_email = 1;
            }
            if (selectedOption.sms && selectedOption.sms == 1) {
                contact.for_sms = 1;
            }
        });
        if (selectedOption.email && selectedOption.email == 1) {
            setdisabledForemail(1);
        }
        if (selectedOption.sms && selectedOption.sms == 1) {
            setdisabledForsms(1);
        }
        setContacts(contacts);
    };
    const handlefieldChange = (selectedOption, name) => {
        // console.log(name);
        // console.log(selectedOption);
        const value = selectedOption.value;
        setSelectedfields(selectedfields => ({
            ...selectedfields,
            [name]: value
        }));
    };

    const hasErrorFor = (field) => { return !!errors[field] }
    const renderErrorFor = (field, val = 0) => {
        if (hasErrorFor(field)) {
            if (field == 'error_message' || val) {
                Swal.fire({
                    text: errors[field][0],
                    icon: 'info',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                }).then((result) => {
                    setErrors([]);
                })
            }
            else
                return (
                    <span className='invalid-feedback'>
                        <strong>{errors[field][0]}</strong>
                    </span>
                )
        }
    }

    // delete modal
    const [show, setShow] = useState(false);
    const handleClose = () => {
        setShow(false);
    };
    const handleShow = (e) => {
        setShow(true);
    };

    useEffect(() => {
        // axios.get('/api/can-add-contacts?lang=' + localStorage.lang)
        //     .then(response => {
        //         setLimit('')
        //         setContactsLimit(response.data.contacts)
        //     }).catch((error) => {
        //         if (error.response.data.errors) {
        //             setErrors(error.response.data.errors);
        //             setLimit(error.response.data.message)
        //         }
        //     });

        const getMailingLists = () => {
            setLoading(true);
            axios
                .get("/api/get-all-groups?lang=" + localStorage.lang)
                .then((response) => {
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
                    setOptions(options);
                    setLoading(false);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                    setLoading(false);
                });
        };
        let params = new URLSearchParams(location.search);
        if (params.get('sms')) {
            setSmsCamp(params.get('sms'));
            setUsage(1);
        }
        else if (params.get('email')) {
            setEmailCamp(params.get('email'));
            setUsage(2);
        }
        else if (params.get('split')) {
            setSplitCamp(params.get('split'));
            setUsage(2);
        }
        else if (params.get('mailing_list')) {
            setMailingList(params.get('mailing_list'));
        }
        getMailingLists();
    }, []);

    const addAnotherContact = () => {
        setContacts([...contacts, {}]);
        // console.log(contacts);
    };

    const removeContactRow = (removeIndex) => {
        setContacts(contacts.filter((y, index) => index != removeIndex));
    };

    const handleFname = (e) => {
        const change = e.target.value;
        const index = e.target.attributes.from.value;
        const changedContact = { ...contacts[index] };
        changedContact.first_name = change;
        var newone = [...contacts];
        newone[index] = changedContact;
        setContacts(newone);
    };
    const handleLname = (e) => {
        const change = e.target.value;
        const index = e.target.attributes.from.value;
        const changedContact = { ...contacts[index] };
        changedContact.last_name = change;
        var newone = [...contacts];
        newone[index] = changedContact;
        setContacts(newone);
    };

    const handleCountry = (e) => {
        const change = e.target.value;
        const index = e.target.attributes.from.value;
        const changedContact = { ...contacts[index] };
        changedContact.country_code = change;
        var newone = [...contacts];
        newone[index] = changedContact;
        setContacts(newone);
    };

    const handleNum = (val, ind) => {
        const change = val;
        // console.log(ind);
        const index = ind;
        var changedContact = { ...contacts[index] };
        changedContact.number = change;
        var newone = [...contacts];
        newone[index] = changedContact;
        // console.log(newone);
        setContacts(newone);
    };

    const handleEmail = (e) => {
        const change = e.target.value;
        const index = e.target.attributes.from.value;
        const changedContact = { ...contacts[index] };
        changedContact.email = change;
        var newone = [...contacts];
        newone[index] = changedContact;
        setContacts(newone);
    };

    const handleDoi = (e) => {
        let change = 0;
        if (e.target.checked) {
            change = 1;
        } else {
            change = 0;
        }
        const index = e.target.attributes.from.value;
        const changedContact = { ...contacts[index] };
        changedContact.double_opt_in = change;
        var newone = [...contacts];
        newone[index] = changedContact;
        setContacts(newone);
    };

    const handleForemail = (e) => {
        let change = 0;
        if (e.target.checked) {
            change = 1;
        } else {
            change = 0;
        }
        const index = e.target.attributes.from.value;
        const changedContact = { ...contacts[index] };
        changedContact.for_email = change;
        var newone = [...contacts];
        newone[index] = changedContact;
        setContacts(newone);
    };

    const handleForsms = (e) => {
        let change = 0;
        if (e.target.checked) {
            change = 1;
        } else {
            change = 0;
        }
        const index = e.target.attributes.from.value;
        const changedContact = { ...contacts[index] };
        changedContact.for_sms = change;
        var newone = [...contacts];
        newone[index] = changedContact;
        setContacts(newone);
    };

    const handleSubmit = async (event) => {
        // event.preventDefault();
        var first_names = [];
        var last_names = [];
        var emails = [];
        var country_codes = [];
        var numbers = [];
        var double_opt_ins = [];
        var for_smses = [];
        var for_emails = [];
        // console.log(contacts);
        contacts.forEach((contact) => {
            if (contact.first_name) { first_names.push(contact.first_name); }
            else { first_names.push(""); }

            if (contact.last_name) { last_names.push(contact.last_name); }
            else { last_names.push(""); }

            if (contact.email) { emails.push(contact.email); }
            else { emails.push(""); }

            if (contact.country_code) { country_codes.push(contact.country_code); }
            else { country_codes.push(""); }

            if (contact.number) { numbers.push(contact.number); }
            else { numbers.push(""); }

            if (contact.double_opt_in) { double_opt_ins.push(contact.double_opt_in); }
            else { double_opt_ins.push(0); }

            if (usage == 2) { for_emails.push(1); }
            else if (contact.for_email) { for_emails.push(contact.for_email); }
            else { for_emails.push(0); }

            if (usage == 1) { for_smses.push(1); }
            else if (contact.for_sms) { for_smses.push(contact.for_sms); }
            else { for_smses.push(0); }

        });

        if (first_names == [] && last_names == []) {
            setErrors({
                first_name: [first_names == [] ? t('required') : ''],
                last_name: [last_names == [] ? t('required') : ''],
            });
            setLoading(false);
            return;
        }

        const data = {
            first_name: first_names,
            last_name: last_names,
            email: emails,
            country_code: country_codes,
            number: numbers,
            double_opt_in: double_opt_ins,
            for_sms: for_smses,
            for_email: for_emails,
        }
        if (selectedOption != "") {
            data.list = selectedOption;
        }
        // console.log(first_name);
        setLoading(true);
        setErrors([])
        await axios
            .post("/api/add-contacts?lang=" + localStorage.lang, data)
            .then((response) => {
                if (selectedOption != "")
                    handleAdd(response.data.data);
                else
                    Swal.fire({
                        title: t('Success'),
                        text: t('Your Contacts have been added successfully!'),
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: t('OK'),
                        //cancelButtonText: 'No, keep it'
                    }).then((result) => {
                        goBack()
                    });
                setLoading(false);
            })
            .catch((error) => {
                setLoading(false);
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                if (error.response.data.code) {
                    Swal.fire({
                        title: t('Please Upgrade Your Package to Add more Contacts'),
                        text: error.response.data.message,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonText: t('OK'),
                        //cancelButtonText: 'No, keep it'
                    })
                }
            });
    };

    const handleAdd = (list) => {
        if (selectedOption != "") {
            var ids = [];
            list.map(row => (ids.push(row.hash_id)))
            setLoading(true);
            setErrors([])
            axios
                .post("/api/add-to-group?lang=" + localStorage.lang, { contact: ids, group: selectedOption })
                .then((response) => {
                    setLoading(false);
                    Swal.fire({
                        title: t('Success'),
                        text: t('Your Contacts have been added successfully!'),
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: t('OK'),
                        //cancelButtonText: 'No, keep it'
                    }).then((result) => {
                        goBack()
                    });
                })
                .catch((error) => {
                    setLoading(false);
                    Swal.fire({
                        title: t('Success'),
                        text: t('Your Contacts have been added successfully!'),
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: t('OK'),
                        //cancelButtonText: 'No, keep it'
                    }).then((result) => {
                        goBack()
                    });
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                });
        }
        if (SmsCamp != "" || EmailCamp != "" || SplitCamp != "") {
            var ids = [];
            list.map(row => (ids.push(row.hash_id)))
            setErrors([])
            setLoading(true);
            const cmpn = SmsCamp ? SmsCamp : (EmailCamp ? EmailCamp : SplitCamp);
            const type = SmsCamp ? 1 : 2
            axios
                .post("/api/add-contact-to-campaignincludes?lang=" + localStorage.lang, { contact: ids, campaign_id: cmpn, type: type })
                .then((response) => {
                    setLoading(false);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                    setLoading(false);
                });
        }
        return true;
    };

    const onChangeHandler = (event) => {
        setFile(event.target.files[0]);
        // console.log(file);
        // send file to server to get response 
        setLoading(true);
        const data = new FormData();
        data.append("file", event.target.files[0]);
        setErrors([])

        axios
            .post("/api/check-file-import?lang=" + localStorage.lang, data)
            .then((response) => {
                setLoading(false);
                setCanSendFile(true);
                setErrors("");
                setfields(
                    response.data.fields.map((row, index) => ({
                        value: index,
                        label: row,
                    })),
                )
            }).catch(error => {
                setCanSendFile(false);
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                setLoading(false);
            });
    };

    const onClickHandler = (e) => {
        e.preventDefault();
        setLoading(true);
        const data = new FormData();
        data.append('first_name', selectedfields.first_name);
        data.append('last_name', selectedfields.last_name);
        data.append('for_sms', selectedfields.for_sms);
        data.append('for_email', selectedfields.for_email);
        data.append('email', selectedfields.email);
        data.append('number', selectedfields.number);
        data.append("file", file);
        setErrors([])

        axios
            .post("/api/file-import?lang=" + localStorage.lang, data)
            .then((res) => {
                setLoading(false);
                handleAdd(res.data.data);
                Swal.fire({
                    title: t('Success'),
                    text: t('Your Contacts have been imported successfully!'),
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                    //cancelButtonText: 'No, keep it'
                }).then((result) => {
                    goBack()
                });
            }).catch(error => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                setLoading(false);
            });
    };

    const Line = () => (
        <hr style={{ color: '#1D2579', backgroundColor: '#1D2579', height: 5 }} />
    );

    const onDownload = () => {
        // e.preventDefault();
        axios({
            url: '/api/file-export?lang=' + localStorage.lang,
            method: 'GET',
            responseType: 'blob', // important
        }).then((response) => {
            setLoading(false);
            // console.log(response.data);
            handleShow(true);
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'sample.xlsx'); //or any other extension
            document.body.appendChild(link);
            link.click();
        }).catch(error => {
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
                    <Row>
                        {/* {limit ?
                            <Col xs="12" className="bg-danger text-center text-white p-2">
                                {limit}
                            </Col>
                            :
                            <Col xs="12" className="bg-info text-center p-2">
                                {t('contacts_remaining_in_package')}: {contactsLimit}
                            </Col>
                        } */}
                        <Col xs="12">
                            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                                <div className="page-title">
                                    <h1>{t('Bulk upload Multiple Contacts')}</h1>
                                </div>
                            </div>
                            <div className="bg-white rounded-box-shadow mb-3 mb-md-4">
                                <Form
                                    className={"create-form-holder multiple-form-box " + (canSendFile ? "import-content-box" : "")}
                                    onSubmit={onClickHandler}
                                    encType="multipart/form-data"
                                >
                                    <Col
                                        xl="6"
                                        lg="12"
                                        md="12"
                                        sm="12"
                                    >
                                        <Form.Group className="d-flex">
                                            <Form.Label forhtml="Import Contacts">
                                                {t('Import Contacts')}
                                            </Form.Label>
                                            <div className="flex-fill input-holder">
                                                <input
                                                    type="file"
                                                    name="file"
                                                    className="custom-file-input form-control"
                                                    id="customFile"
                                                    onChange={onChangeHandler}
                                                />
                                                {renderErrorFor('file')}
                                            </div>
                                        </Form.Group>
                                    </Col>

                                    {canSendFile ?
                                        <React.Fragment>
                                            <div className="import-fields-box">
                                                <h5>{t('Map fields')}:</h5>
                                                <small>{t('Please note that the data will not be inserted if the fields are not mapped properly')}</small>
                                                <small>{t('Contacts will be added in accordance to the package limitations, and extra rows will be ignored.')}</small>
                                                {/* display when no error in file. */}
                                                <div className="row d-flex flex-row flex-wrap pt-2 pt-xxl-2">
                                                    <Form.Group className="col-lg-6 mb-2 mb-md-4 d-flex flex-column">
                                                        <Form.Label className="mb-2 mb-md-0" forhtml="title">
                                                            {t('First Name')}
                                                        </Form.Label>
                                                        <div className="flex-fill">
                                                            <div className="mutli-contct-list-select">
                                                                <Select
                                                                    onChange={(e) =>
                                                                        handlefieldChange(e, "first_name")
                                                                    }
                                                                    options={fields}
                                                                    classNamePrefix="react-select"
                                                                />
                                                                {renderErrorFor('first_name')}
                                                            </div>
                                                        </div>
                                                    </Form.Group>
                                                    <Form.Group className="col-lg-6 mb-2 mb-md-4 d-flex flex-column">
                                                        <Form.Label className="mb-2 mb-md-0" forhtml="title">
                                                            {('Last Name')}
                                                        </Form.Label>
                                                        <div className="flex-fill">
                                                            <div className="mutli-contct-list-select">
                                                                <Select
                                                                    onChange={(e) =>
                                                                        handlefieldChange(e, "last_name")
                                                                    }
                                                                    options={fields}
                                                                    classNamePrefix="react-select"
                                                                />
                                                                {renderErrorFor('last_name')}
                                                            </div>
                                                        </div>
                                                    </Form.Group>
                                                    <Form.Group className="col-xl-6 mb-2 mb-md-4 d-flex flex-column sms-row">
                                                        <Form.Label className="mb-2 mb-md-0" forhtml="title">
                                                            {t('For SMS')}
                                                        </Form.Label>
                                                        <div className="flex-fill ">
                                                            <div className="mutli-contct-list-select">
                                                                <Select
                                                                    onChange={(e) =>
                                                                        handlefieldChange(e, "for_sms")
                                                                    }
                                                                    options={fields}
                                                                    classNamePrefix="react-select"
                                                                />
                                                                {renderErrorFor('for_sms')}
                                                            </div>
                                                        </div>
                                                    </Form.Group>
                                                    {/* <br> */}
                                                    <Form.Group className="col-lg-6 mb-2 mb-md-4 d-flex flex-column">
                                                        <Form.Label className="mb-2 mb-md-0" forhtml="title">
                                                            {t('For Email')}
                                                        </Form.Label>
                                                        <div className="flex-fill">
                                                            <div className="mutli-contct-list-select">
                                                                <Select
                                                                    onChange={(e) =>
                                                                        handlefieldChange(e, "for_email")
                                                                    }
                                                                    options={fields}
                                                                    classNamePrefix="react-select"
                                                                />
                                                                {renderErrorFor('for_email')}
                                                            </div>
                                                        </div>
                                                    </Form.Group>
                                                    <Form.Group className="col-lg-6 mb-2 mb-md-4 d-flex flex-column">
                                                        <Form.Label className="mb-2 mb-md-0" forhtml="title">
                                                            {t('Number')}
                                                        </Form.Label>
                                                        <div className="flex-fill">
                                                            <div className="mutli-contct-list-select">
                                                                <Select
                                                                    onChange={(e) =>
                                                                        handlefieldChange(e, "number")
                                                                    }
                                                                    options={fields}
                                                                    classNamePrefix="react-select"
                                                                />
                                                                {renderErrorFor('number')}
                                                            </div>
                                                        </div>
                                                    </Form.Group>
                                                    <Form.Group className="col-lg-6 mb-2 mb-md-4 d-flex flex-column">
                                                        <Form.Label className="mb-2 mb-md-0" forhtml="title">
                                                            {t('Email')}
                                                        </Form.Label>
                                                        <div className="flex-fill">
                                                            <div className="mutli-contct-list-select">
                                                                <Select
                                                                    onChange={(e) =>
                                                                        handlefieldChange(e, "email")
                                                                    }
                                                                    options={fields}
                                                                    classNamePrefix="react-select"
                                                                />
                                                                {renderErrorFor('email')}
                                                            </div>
                                                        </div>
                                                    </Form.Group>
                                                </div>
                                            </div>
                                            {renderErrorFor('invalid_or_limit')}
                                            <div className="mbl-buttons btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
                                                <button className="btn btn-primary" type="submit">
                                                    <span> {t('Import data')} </span>
                                                </button>
                                            </div>
                                        </React.Fragment>
                                        :
                                        // ""
                                        <div className="mbl-buttons btns-holder right-btns d-flex flex-row-reverse">
                                            <a
                                                className="btn btn-secondary ms-xl-2 ms-lg-2 ms-md-2 ms-sm-2 ms-0 mbl-margin"
                                                // href="/api/file-export"
                                                onClick={onDownload}
                                            >
                                                <span>
                                                    <FontAwesomeIcon
                                                        icon={faLongArrowAltDown}
                                                        className="me-2"
                                                    />
                                                    {t('Download Sample')}</span>
                                            </a>
                                            <a
                                                className="btn btn-secondary"
                                                onClick={() => setShow(true)}
                                            >
                                                <span>{t('Instructions')}</span>
                                            </a>
                                        </div>
                                    }
                                </Form>
                            </div>
                        </Col>
                    </Row>
                </Container>
            </section>
            <section className="right-canvas email-campaign">
                <Container fluid>
                    <Row>
                        <Col xs="12">
                            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                                <div className="page-title">
                                    <h1>{t('Add Multiple Contacts')}</h1>
                                </div>
                            </div>
                            <div className="multi-records bg-white create-form-holder rounded-box-shadow mb-3 mb-md-4">
                                <div className="multiple-contact">
                                    {contacts.map((contact, index) => (
                                        <span className="indent" key={index}>
                                            <div className="d-flex justify-content-end mb-3">
                                                <button type="button" onClick={(e) => removeContactRow(index)} title={t("delete_row")} class="delete-btn btn btn-danger">
                                                    <FontAwesomeIcon
                                                        icon={faMinus}
                                                    />
                                                </button>
                                            </div>
                                            <Row className="multiple-contact-row">
                                                <Col
                                                    xl="6"
                                                    md="12"
                                                    sm="12"
                                                >
                                                    <Form.Group className="mb-2 mb-md-4 d-flex">
                                                        <Form.Label htmlFor="first-name">
                                                            {t('First Name')} <b className="req-sign">*</b>
                                                        </Form.Label>
                                                        <div className="flex-fill input-holder">
                                                            <input
                                                                name="first_name"
                                                                className="form-control"
                                                                type="text"
                                                                from={index}
                                                                onChange={(e) =>
                                                                    handleFname(
                                                                        e
                                                                    )
                                                                }
                                                                placeholder="e.g. Sam"
                                                                value={contact.first_name}
                                                            />
                                                            {renderErrorFor('first_name.' + index)}
                                                            {renderErrorFor('first_name')}
                                                        </div>
                                                    </Form.Group>
                                                </Col>
                                                <Col
                                                    xl="6"
                                                    md="12"
                                                    sm="12"
                                                >
                                                    <Form.Group className="mb-2 mb-md-4 d-flex">
                                                        <Form.Label htmlFor="last-name">
                                                            {t('Last Name')} <b className="req-sign">*</b>
                                                        </Form.Label>
                                                        <div className="flex-fill input-holder">
                                                            <input
                                                                name="last_name"
                                                                className="form-control"
                                                                type="text"
                                                                from={index}
                                                                onChange={(e) =>
                                                                    handleLname(
                                                                        e
                                                                    )
                                                                }
                                                                placeholder="e.g. Smith"
                                                                value={contact.last_name}
                                                            />
                                                            {renderErrorFor('last_name.' + index)}
                                                            {renderErrorFor('last_name')}
                                                        </div>
                                                    </Form.Group>
                                                </Col>
                                                <Col
                                                    xl="6"
                                                    md="12"
                                                    sm="12"
                                                >
                                                    <Form.Group className="mb-2 mb-md-4 d-flex">
                                                        <Form.Label htmlFor="email">
                                                            {t('Email')}
                                                        </Form.Label>
                                                        <div className="flex-fill input-holder">
                                                            <input
                                                                name="email"
                                                                className="form-control"
                                                                type="text"
                                                                from={index}
                                                                onChange={(e) =>
                                                                    handleEmail(
                                                                        e
                                                                    )
                                                                }
                                                                placeholder="e.g example@email.com"
                                                                value={contact.email}
                                                            />
                                                            {renderErrorFor('email.' + index)}
                                                        </div>
                                                    </Form.Group>
                                                </Col>
                                                <Col
                                                    xl="6"
                                                    md="12"
                                                    sm="12"
                                                >
                                                    <Form.Group className="mb-2 mb-md-4 d-flex">
                                                        <Form.Label htmlFor="number">
                                                            {t('Phone Number')}
                                                        </Form.Label>
                                                        <div className="flex-fill input-holder d-flex flex-column">
                                                            <PhoneInput
                                                                placeholder={t("Enter phone number")}
                                                                onChange={(number) => handleNum(number, index)}
                                                                name="number"
                                                                className="form-control"
                                                                from={index}
                                                                placeholder="e.g. +49 1579230198"
                                                                value={contact.number}
                                                            />
                                                            {renderErrorFor('number.' + index)}
                                                        </div>
                                                    </Form.Group>
                                                </Col>
                                                <Col
                                                    xl="6"
                                                    md="12"
                                                    sm="12"
                                                >
                                                    <Form.Group className="mb-2 mb-md-2 d-flex align-items-center">
                                                        <Form.Label htmlFor="for_sms">
                                                            {t('For')} <b className="req-sign">*</b>
                                                        </Form.Label>
                                                        <div className="d-flex flex-row align-items-center justify-content-start flex-fill input-holder sms-email-wrap ">
                                                            <Form.Label htmlFor="sms" className=" heading-col">
                                                                {t('SMS')}
                                                            </Form.Label>
                                                            <input
                                                                name="for_sms"
                                                                className="form-checkbox heading-col"
                                                                disabled={disabledForsms}
                                                                type="checkbox"
                                                                checked={usage == 1 ? 1 : (contact.for_sms == 1 || contact.for_sms == '1')}
                                                                from={index}
                                                                onClick={(e) => handleForsms(e)}
                                                                disabled={usage == 1 ? 1 : 0}
                                                            />
                                                            <Form.Label htmlFor="for_email" className=" heading-col">
                                                                {t('Email')}
                                                            </Form.Label>
                                                            <input
                                                                name="for_email"
                                                                className="form-checkbox"
                                                                disabled={disabledForemail}
                                                                type="checkbox"
                                                                checked={usage == 2 ? 1 : (contact.for_email == 1 || contact.for_email == '1')}
                                                                from={index}
                                                                onClick={(e) => handleForemail(e)}
                                                                disabled={usage == 2 ? 1 : 0}
                                                            />

                                                        </div>

                                                    </Form.Group>
                                                    <div className="row mb-3">
                                                        <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12 text-end">
                                                            {renderErrorFor('for_sms.' + index)}
                                                            {renderErrorFor('for_email.' + index)}
                                                            {renderErrorFor('for.' + index, 1)}
                                                        </div>
                                                    </div>
                                                </Col>
                                                {/* <Col
                                                    xxl="2"
                                                    xl="3"
                                                    md="4"
                                                    sm="6"
                                                >
                                                    <Form.Group className="mb-3 mb-md-4 d-flex">
                                                        <Form.Label htmlFor="double_opt_in">
                                                            Double opt in
                                                        </Form.Label>
                                                        <div className="flex-fill input-holder">
                                                            <input
                                                                name="double_opt_in"
                                                                className="form-checkbox"
                                                                type="checkbox"
                                                                from={index}
                                                                onChange={(e) =>
                                                                    handleDoi(e)
                                                                }
                                                            />
                                                            {renderErrorFor('double_opt_in.'+index)}
                                                        </div>
                                                    </Form.Group>
                                                </Col> */}
                                            </Row>
                                            <div style={{ border: 'solid 1px' }} className={contacts.length == index + 1 ? "d-none" : "mb-4"}></div>
                                        </span>
                                    ))}

                                    <Row>
                                        <Col xs="12">
                                            <span
                                                className="btn btn-secondary"
                                                onClick={addAnotherContact}
                                            >

                                                <span>
                                                    <FontAwesomeIcon
                                                        icon={faPlus}
                                                        className="me-2"
                                                    />
                                                    {t('Add Another Contact')}
                                                </span>

                                            </span>
                                        </Col>
                                    </Row>
                                </div>
                            </div>
                            {usage == 0 ?
                                <div className="mutli-contct-list-holder">
                                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                                        <Form.Label
                                            className="mb-2 mb-md-0"
                                            forhtml="title"
                                        >
                                            {t('Add Contact to Mailing List')}
                                        </Form.Label>
                                        <div className="flex-fill input-holder">
                                            <div className="mutli-contct-list-select">
                                                <Select
                                                    onChange={(e) =>
                                                        handleChange(e)
                                                    }
                                                    options={options}
                                                    classNamePrefix="react-select"
                                                    placeholder={t('Select Mailing List')}
                                                />
                                            </div>
                                        </div>
                                    </Form.Group>
                                </div>
                                : ""}
                            {renderErrorFor('error_message')}
                            <div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
                                <button
                                    className="btn btn-primary ms-3 mb-3"
                                    onClick={handleSubmit}
                                >
                                    <span>{t('Add')}</span>
                                </button>
                                <button
                                    className="btn btn-secondary ms-3 mr-2 mb-3"
                                    onClick={() => goBack()}
                                >
                                    <span>{t('Back')}</span>
                                </button>
                                {/* <Link
                                    className="btn btn-secondary ms-3 mr-2 mb-3"
                                    to="/contacts"
                                >
                                </Link> */}
                            </div>
                        </Col>
                    </Row>
                </Container>
            </section>

            {/* Intructions Modal */}
            <Modal
                show={show}
                onHide={handleClose}
                className="em-modal bulk-dlt-modal instructions-modal"
                centered
            >
                <Modal.Body >
                    <div className="page-title">
                        <h1 className="text-center mb-3"> {t('Bulk Upload Instructions')} </h1>
                    </div>
                    <p> <b>{t('First Name')}: </b> e.g. Sam </p>
                    <p> <b>{t('Last Name')}: </b> e.g. Smith </p>
                    <p> <b>{t('Phone Number')}: </b> {t("phone_bulk_instruction")} </p>
                    <p> <b>{t('Email')}: </b> e.g example@email.com </p>
                    <p> <b>{t('For SMS')}: </b>  <b>0</b> {t('or')} <b>1</b>. \n <b>1</b> {t('for_sms_bulk_instruction')} <b>0</b> {t('if_not_bulk_instruction')}  </p>
                    <p> <b>{t('For Email')}: </b>  <b>0</b> {t('or')} <b>1</b>. \n <b>1</b> {t('for_email_bulk_instruction')} <b>0</b> {t('if_not_bulk_instruction')}  </p>
                    <p className="mt-3 note-text"> <b>{t('Note')}: </b> {t('note_bulk_instruction')} </p>
                </Modal.Body>
                <Modal.Footer className="justify-content-center">
                    <Button variant="secondary" onClick={handleClose} className="btn btn-secondary">
                        <span>OK</span>
                    </Button>
                </Modal.Footer>
            </Modal>

        </React.Fragment>
    );
}

export default withTranslation()(AddMultipleContact);
