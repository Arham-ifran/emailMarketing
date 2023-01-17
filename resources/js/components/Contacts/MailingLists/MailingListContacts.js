import React, { useState, useEffect } from "react";
import { Link, useParams } from "react-router-dom";
import { Container, Table, Modal, Button } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faEye } from "@fortawesome/free-regular-svg-icons";
import { faPencilAlt } from "@fortawesome/free-solid-svg-icons";
import { faTrashAlt } from "@fortawesome/free-regular-svg-icons";
import Moment from "react-moment";
import Spinner from "../../includes/spinner/Spinner";
import "./css/EditMailList.css";
import Swal from 'sweetalert2';
import moment from 'moment-timezone';
import { withTranslation } from 'react-i18next';
function MailingListContacts(props) {
    const { t } = props;
    const [name, setName] = useState("");
    const [description, setDescription] = useState("");
    const { listID } = useParams();
    const [double_opt_in, setDoi] = useState();
    const [contacts, setContacts] = useState([]);
    const [deleting, setDeleting] = useState("");
    const [loading, setLoading] = useState(false);

    const [errors, setErrors] = useState([]);
    const [trigger, setTrigger] = useState(0);

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

    // delete modal
    const [show, setShow] = useState(false);
    const handleClose = () => {
        setShow(false);
        setDeleting("");
    };
    const handleShow = (e) => {
        setDeleting(e.target.closest("button").id);
        setShow(true);
    };

    const goBack = () => {
        let params = new URLSearchParams(location.search);
        if (params.get('page')) {
            window.location.href = "/mailing-lists?page=" + params.get('page');
        }
        else {
            window.location.href = "/mailing-lists";
        }
    }

    const handleContactDelete = () => {
        const num = deleting;
        setErrors([])
        setLoading(true);
        if (num) {
            // console.log({id:[num]});
            axios
                .post("/api/delete-contacts?lang=" + localStorage.lang, { id: [num] })
                .then((response) => {
                    // console.log(response.data);
                    setLoading(false);
                    Swal.fire({
                        title: t('Success'),
                        text: t('Your Contact has been deleted successfully!'),
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: t('OK'),
                        //cancelButtonText: 'No, keep it'
                    })
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                    setLoading(false);
                });
        }
        setTrigger(!trigger);
        handleClose();
    };

    useEffect(() => {
        const getList = () => {
            setLoading(true);
            axios
                .get("/api/get-group/" + listID + "?lang=" + localStorage.lang)
                .then((response) => {
                    const data_received = response.data.data;

                    setName(data_received.name);
                    setDescription(data_received.description);
                    setDoi(data_received.double_opt_in);
                    setContacts(data_received.contacts);
                    setLoading(false);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                    setLoading(false);
                });
        };
        getList();
    }, [trigger]);

    const handleRemove = (id) => {
        setErrors([])
        setLoading(true);
        axios
            .post("/api/remove-from-group?lang=" + localStorage.lang, { contact: [id], group: listID })
            .then((response) => {
                // console.log(response.data);
                setLoading(false);
                Swal.fire({
                    title: t('Success'),
                    text: t('Your Contacts is removed from Mailing list successfully!'),
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                    //cancelButtonText: 'No, keep it'
                })
            })
            .catch((error) => {
                if (
                    error.response &&
                    error.response.hasOwnProperty("data") &&
                    error.response.data.hasOwnProperty("message") &&
                    error.response.data.message
                ) {
                    document.getElementById("errorMessage").innerHTML =
                        error.response.data.message;
                }
                setLoading(false);
            });
        setTrigger(!trigger);
    };

    return (
        <React.Fragment>
            {loading ? <Spinner /> : null}
            <Container fluid>
                <div className="form-table-wrapper rounded-box-shadow bg-white">
                    <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                        <div className="page-title">
                            {/* <h5>Viewing Mailing List</h5> */}
                            <h1>{name}</h1>
                            <small>{description}</small>
                        </div>
                    </div>
                    <div className="table-responsive">
                        <Table className="table em-table align-middle">
                            <thead>
                                <tr>
                                    <th>{t('Contact Name')}</th>
                                    <th>{t('Email')}</th>
                                    <th>{t('Mobile Number')}</th>
                                    <th>{t('Date Created')}</th>
                                    <th>{t('Last Modified')}</th>
                                    {/* <th>Contact actions</th> */}
                                    {/* <th>List Actions</th> */}
                                    <th>{t('Actions')}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {contacts.length ?
                                    contacts.map((contact) => (
                                        <tr key={contact.hash_id}>
                                            <td>
                                                {contact.first_name}
                                                {" "}
                                                {contact.last_name}
                                            </td>
                                            <td> {contact.email} </td>
                                            <td>
                                                {contact.country_Code}
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
                                                <ul className="action-icons list-unstyled">
                                                    <li>
                                                        <button
                                                            className="dlt-icon"
                                                            title={t("remove")}
                                                            ariant="primary"
                                                            onClick={() =>
                                                                handleRemove(
                                                                    contact.hash_id
                                                                )
                                                            }
                                                        >
                                                            <FontAwesomeIcon
                                                                icon={faTrashAlt}
                                                            />
                                                        </button>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    ))
                                    :
                                    (
                                        <tr>
                                            <td className="text-center" colSpan="6">
                                                {t('No Contacts Found')}
                                            </td>
                                        </tr>
                                    )
                                }
                            </tbody>
                        </Table>
                    </div>
                </div>

                <div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
                    <Link
                        // to="/mailing-lists"
                        onClick={() => goBack()}
                        className="btn btn-secondary ms-3 mb-3"
                    >
                        <span>{t('Back')}</span>
                    </Link>
                </div>
            </Container>
            {/* Delete Modal */}
            <Modal
                show={show}
                onHide={handleClose}
                className="em-modal dlt-modal"
                centered
            >
                <Modal.Body className="d-flex align-items-center justify-content-center flex-column">
                    <span className="dlt-icon">
                        <FontAwesomeIcon icon={faTrashAlt} />
                    </span>
                    <p>{t('Are you sure you want to delete Contact?')}</p>
                    <small>
                        {t('If you click yes, It will be permanently deleted from your contacts')}
                    </small>
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
        </React.Fragment>
    );
}

export default withTranslation()(MailingListContacts);
