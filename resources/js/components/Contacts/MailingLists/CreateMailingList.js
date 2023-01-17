import React, { useState, useEffect } from "react";
import { Link, useHistory } from "react-router-dom";
import { Container, Row, Col, Form, Modal, Table, Button } from "react-bootstrap";
import Spinner from "../../includes/spinner/Spinner";
import "./css/CreateMailingList.css";
import Swal from 'sweetalert2';
import Pagination from "react-js-pagination";
import { withTranslation } from 'react-i18next';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faInfoCircle } from "@fortawesome/free-solid-svg-icons";
var addedContacts = [];
var removed = 0;
var removedContacts = [];
var loading_count = 0;
function CreateMailingList(props) {
    const { t } = props;
    const history = useHistory();
    const [name, setName] = useState("");
    const [description, setDescription] = useState("");
    const [double_opt_in, setDoi] = useState(0);
    const [for_sms, setFor_sms] = useState(0);
    const [for_email, setFor_Email] = useState(0);
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState([]);
    const [editing, setEditing] = useState(false);
    const [groupId, setGroupId] = useState();
    const [contacts, setContacts] = useState([]);
    const [pageNumber, setPageNumber] = useState(1);
    const [perPage, setperPage] = useState(0);
    const [totalItems, setTotalItems] = useState(0);
    const [pageRange, setPageRange] = useState(5);
    const [filterName, setfilterName] = useState("");

    const handleDoi = () => { setDoi(!double_opt_in); };
    const handleForsms = () => { setFor_sms(!for_sms); };
    const handleForemail = () => { setFor_Email(!for_email); };

    const [SmsCamp, setSmsCamp] = useState("");
    const [EmailCamp, setEmailCamp] = useState("");
    const [SplitCamp, setSplitCamp] = useState("");

    const goBack = (back = false) => {
        if (SmsCamp) {
            window.location.href = "/sms-campaign/" + SmsCamp + "/edit";
        }
        else if (EmailCamp) {
            window.location.href = "/email-campaign/" + EmailCamp + "/edit";
        }
        else if (SplitCamp) {
            window.location.href = "/split-testing/" + SplitCamp + "/edit";
        }
        else {
            let params = new URLSearchParams(location.search);
            if (params.get('page')) {
                window.location.href = "/mailing-lists?page=" + params.get('page');
            }
            else {
                window.location.href = "/mailing-lists";
            }
        }
    }

    const hasErrorFor = (field) => {
        return !!errors[field]
    }

    const renderErrorFor = (field) => {
        if (hasErrorFor(field)) {
            if (field == 'for') {
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

    const handleSubmit = (event) => {
        event.preventDefault();
        setLoading(true);

        if (name == "" && description == "") {
            setErrors({
                name: [name == "" ? t('required') : ''],
                description: [description == "" ? t('required') : ''],
            });
            loading_count++;
            if ((groupId && loading_count >= 2) || (!groupId && loading_count >= 1))
                setLoading(false);
            return;
        }

        const data = {
            name: name,
            description: description,
            double_opt_in: double_opt_in,
            for_sms: for_sms,
            for_email: for_email,
            id: groupId,
        };

        setErrors([]);
        axios
            .post("/api/add-group?lang=" + localStorage.lang, data)
            .then(async (res) => {
                loading_count++;
                if ((groupId && loading_count >= 2) || (!groupId && loading_count >= 1))
                    setLoading(false);
                if (addedContacts.length) {
                    await handleContactsRemove(res.data.data.hash_id)
                    await handleContactAdd(res.data.data.hash_id)
                }
                else
                    Swal.fire({
                        title: t('Success'),
                        text: (groupId ? t('Your Mailing list has been updated successfully!') : t('Your Mailing list has been created successfully!')),
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: t('OK'),
                        //cancelButtonText: 'No, keep it'
                    }).then((result) => {
                        goBack();
                    });
            })
            .catch((error) => {
                loading_count++;
                if ((groupId && loading_count >= 2) || (!groupId && loading_count >= 1))
                    setLoading(false);
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
            });
    };

    const handleContactsRemove = async (listId) => {
        if (removedContacts.length) {
            await axios.post("/api/remove-from-group?lang=" + localStorage.lang, { contact: removedContacts, group: listId })
                .then((response) => {
                    // console.log(response.data);
                })
                .catch((error) => { });
        }
    };

    const getList = (id) => {
        setLoading(true);
        setGroupId(id);
        axios
            .get("/api/get-group/" + id + "?lang=" + localStorage.lang)
            .then((response) => {
                loading_count++;
                if ((groupId && loading_count >= 2) || (!groupId && loading_count >= 1))
                    setLoading(false);
                const data_received = response.data.data;

                // console.log(data_received);
                setName(data_received.name);
                setDescription(data_received.description);
                // setDoi(data_received.double_opt_in);
                setFor_sms(data_received.for_sms);
                setFor_Email(data_received.for_email);
                var c = data_received.contacts.map(cont => cont.hash_id);
                addedContacts = c;

            })
            .catch((error) => {
                loading_count++;
                if ((groupId && loading_count >= 2) || (!groupId && loading_count >= 1))
                    setLoading(false);
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
            });
    };

    // In case of edit...
    useEffect(() => {
        loading_count = 0;
        removed = 0;
        removedContacts = [];
        let parseUriSegment = window.location.pathname.split("/");
        if (parseUriSegment.indexOf('mailing-lists') && parseUriSegment.indexOf('edit') != -1) {
            getList(parseUriSegment[2]);
            setEditing(true);
        }
        let params = new URLSearchParams(location.search);
        if (params.get('sms')) {
            setSmsCamp(params.get('sms'));
        }
        else if (params.get('email')) {
            setEmailCamp(params.get('email'));
        }
        else if (params.get('split')) {
            setSplitCamp(params.get('split'));
        }
    }, []);

    // for contacts
    useEffect(() => {
        getContacts()
    }, [pageNumber, filterName]);

    const getContacts = () => {
        setLoading(true);
        axios
            .get(
                "/api/get-contacts" +
                "?page=" +
                pageNumber +
                "&name=" +
                filterName +
                "&lang=" + localStorage.lang
            )
            .then((response) => {
                if (!$.trim(response.data.data) && pageNumber !== 1) {
                    setPageNumber(pageNumber - 1);
                }
                setContacts(response.data.data);
                setperPage(response.data.meta.per_page);
                setTotalItems(response.data.meta.total);
                loading_count++;
                if ((groupId && loading_count >= 2) || (!groupId && loading_count >= 1))
                    setLoading(false);
                // console.log(contacts);
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                    loading_count++;
                    if ((groupId && loading_count >= 2) || (!groupId && loading_count >= 1))
                        setLoading(false);
                }
            });
    };

    // modal
    const [show, setShow] = useState(false);
    const handleClose = (event) => {
        setShow(false);
        if (filterName != "") setfilterName("");
    };
    const handleShow = (e) => {
        setShow(true);
    };

    const handleContactAdd = async (listId) => {
        if (addedContacts.length) {
            setLoading(true);
            await axios.post("/api/add-to-group?lang=" + localStorage.lang, { contact: addedContacts, group: listId })
                .then((response) => {
                    loading_count++;
                    if ((groupId && loading_count >= 2) || (!groupId && loading_count >= 1))
                        setLoading(false);
                    Swal.fire({
                        title: t('Success'),
                        text: (groupId ? t('Your Mailing list has been updated successfully!') : t('Your Mailing list has been created successfully!')),
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: t('OK'),
                    }).then((result) => {
                        goBack();
                    });
                })
                .catch((error) => {
                    loading_count++;
                    if ((groupId && loading_count >= 2) || (!groupId && loading_count >= 1))
                        setLoading(false);
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                });
        }
    }

    const handleAddContactCheckbox = (contact_id) => {
        if (addedContacts.includes(contact_id)) {
            // remove contact from list
            const index = addedContacts.indexOf(contact_id);
            if (index > -1) {
                addedContacts.splice(index, 1);
            }
            removedContacts.push(contact_id);

        } else {
            // add contact to list
            addedContacts.push(contact_id);
            const index = removedContacts.indexOf(contact_id);
            if (index > -1) {
                removedContacts.splice(index, 1);
            }
        }
    }

    const handleAddAllContactsCheckbox = (box) => {
        if (box.checked) {
            contacts.map(cont => {
                addedContacts.push(cont.hash_id);
                document.getElementById('n' + cont.hash_id).checked = 1;
            })
        } else {
            contacts.map(cont => {
                document.getElementById('n' + cont.hash_id).checked = 0;
                const index = addedContacts.indexOf(cont.hash_id);
                if (index > -1) {
                    addedContacts.splice(index, 1);
                }
            })
        }
    }

    return (
        <React.Fragment>
            {loading ? <Spinner /> : null}
            <Container fluid>
                <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                    <div className="page-title">
                        <h1>{t('Create Mailing List')}</h1>
                    </div>
                </div>
                <Form className="create-form-holder" onSubmit={handleSubmit}>
                    <div className="rounded-box-shadow bg-white">
                        <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                            <Form.Label
                                className="mb-2 mb-md-0"
                                htmlFor="campaign-name"
                            >
                                {t('Name')} <b className="req-sign">*</b>
                            </Form.Label>
                            <div className="flex-fill input-holder">
                                <input
                                    id="campaign-name"
                                    className="form-control"
                                    type="text"
                                    name="name"
                                    onChange={(e) => setName(e.target.value)}
                                    value={name}
                                />
                                {renderErrorFor('name')}
                            </div>
                        </Form.Group>
                        <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                            <Form.Label
                                className="mb-2 mb-md-0"
                                htmlFor="sender-name"
                            >
                                {t('What do you know about them?')} <b className="req-sign">*</b>
                            </Form.Label>
                            <div className="flex-fill input-holder">
                                <textarea
                                    rows="5"
                                    cols="5"
                                    className="form-control"
                                    name="descripton"
                                    onChange={(e) =>
                                        setDescription(e.target.value)
                                    }
                                    value={description}
                                    maxLength='250'
                                >{description}</textarea>
                                <small> {250 - description.length} {t('characters_remaining')} </small>
                                <p>
                                    <FontAwesomeIcon icon={faInfoCircle}></FontAwesomeIcon>
                                    {" "}
                                    {t('limit_is_250_characters_including_spaces')}
                                </p>
                                {renderErrorFor('description')}
                            </div>
                        </Form.Group>
                        <Form.Group className="mb-3 mb-md-4 d-flex">
                            <Form.Label htmlFor="for_sms">
                                {t('For')} <b className="req-sign">*</b>
                            </Form.Label>
                            <div className="d-flex flex-row align-items-center justify-content-start flex-fill input-holder sms-email-wrap">
                                <label> {t('SMS')} </label>
                                <input
                                    name="for_sms"
                                    className="form-checkbox"
                                    type="checkbox"
                                    checked={for_sms == 1 ? 1 : 0}
                                    onClick={(e) => handleForsms(e)}
                                />
                                <label > {t('Email')} </label>
                                <input
                                    name="for_email"
                                    className="form-checkbox"
                                    type="checkbox"
                                    checked={for_email == 1 ? 1 : 0}
                                    onClick={(e) => handleForemail(e)}
                                />
                                {renderErrorFor('for_sms')}
                                {renderErrorFor('for_email')}
                                {renderErrorFor('for')}
                            </div>
                        </Form.Group>
                        <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                            <Form.Label
                                className="mb-2 mb-md-0"
                                htmlFor="sender-name"
                            >
                                {t('Add Contacts to mailing list')}
                            </Form.Label>
                            <div className="flex-fill input-holder mailing-contact-btn">
                                <button
                                    type="button"
                                    className="btn btn-secondary"
                                    onClick={(e) => handleShow(e)}
                                >
                                    <span> {t('Add Contacts')} </span>
                                </button>
                                <Link to={"/contacts/add-multiple?mailing_list=" + (groupId ? groupId : 0)}
                                    className="btn btn-secondary ms-2"
                                    onClick={(e) => handleShow(e)}
                                >
                                    <span> {t('Import/Add new Contacts')} </span>
                                </Link>
                                <p className="mb-0 ms-2 make-inline">{addedContacts.length} {t('Contact(s) Added')} </p>
                            </div>
                        </Form.Group>
                    </div>
                    <div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
                        <button
                            type="submit"
                            className="btn btn-primary ms-3 ml-2 mb-3"
                        >
                            <span> {groupId ? t('Update') : t('Create')}</span>
                        </button>
                        <button
                            type="button"
                            onClick={() => goBack(true)}
                            className="btn btn-secondary ms-3 mb-3"
                        >
                            <span>{t('Back')}</span>
                        </button>
                    </div>
                </Form>
            </Container>

            {/* // Contacts Modal */}
            <Modal show={show} onHide={handleClose} className="em-modal contact-modal smallmodal new-contact-modal" centered>
                <div>
                    <Modal.Body className="d-flex align-items-center justify-content-center flex-column em-table">
                        <div className="mb-3 group-select-title w-100 text-center">
                            <span className="static-title">{t('Add Contacts to This Mailing List')} </span>
                        </div>
                        <div className="mb-3 group-select-title w-100 text-center">
                            <Form className="em-form campaign-new-contact mb-3 mb-md-2" method="GET">
                                <Row>
                                    <Col lg="5">
                                        <Form.Group className="d-flex flex-row align-items-center">
                                            <Form.Label className="mb-0 me-3">
                                                {t('Select all Visible')}
                                            </Form.Label>
                                            <label className="custom-checkbox no-text me-0">
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
                                                />
                                                <span className="checkmark"></span>
                                            </label>
                                        </th>
                                        <th>{t('Sr.')}</th>
                                        <th>{t('Contacts Name')}</th>
                                        <th>{t('Email')}</th>
                                        <th>{t('Mobile no.')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {contacts.length ?
                                        contacts.map((contact, index) => (
                                            (for_sms == 0 && for_email == 0) || (contact.for_sms == for_sms || contact.for_email == for_email) ?
                                                <tr key={contact.hash_id}>
                                                    <td>
                                                        <label className="custom-checkbox no-text me-0">
                                                            <input
                                                                className="form-checkbox"
                                                                type="checkbox"
                                                                id={"n" + contact.hash_id}
                                                                onClick={() => handleAddContactCheckbox(contact.hash_id)}
                                                                defaultChecked={addedContacts.includes(contact.hash_id) ? true : false}
                                                            />
                                                            <span className="checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>{(pageNumber - 1) * perPage + index + 1}</td>
                                                    <td className="text-capitalize">{contact.first_name}{" "}{contact.last_name}</td>
                                                    <td>{contact.email}</td>
                                                    <td>{contact.country_Code}{" "}{contact.number}</td>
                                                </tr>
                                                : ""
                                        )) :
                                        <tr>
                                            <td className="text-center" colSpan="5">
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
                        <Button variant="secondary" onClick={handleClose}>
                            <span>{t('Close')}</span>
                        </Button>
                    </Modal.Footer>
                </div>
            </Modal>
        </React.Fragment>
    );
}

export default withTranslation()(CreateMailingList);
