import React, { Fragment, useState, useEffect } from "react";
import { Container, Row, Col, Form, Table } from "react-bootstrap";
import { Link } from "react-router-dom";
import { Modal, Button } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faListAlt } from "@fortawesome/free-regular-svg-icons";
import { faPencilAlt } from "@fortawesome/free-solid-svg-icons";
import { faTrashAlt } from "@fortawesome/free-regular-svg-icons";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { faCheck } from "@fortawesome/free-solid-svg-icons";
import { faMinus } from "@fortawesome/free-solid-svg-icons";
import DateTimePicker from 'react-datetime-picker';
import Moment from "react-moment";
import Spinner from "../../includes/spinner/Spinner";
import Pagination from "react-js-pagination";
import Swal from 'sweetalert2';
import moment from 'moment-timezone';
import { withTranslation } from 'react-i18next';

import "./css/AllMailingLists.css";
import { useCallback } from "react";
function AllMailingLists(props) {
    const { t } = props;
    const [lists, setLists] = useState([]);
    const [deleting, setDeleting] = useState("");
    const [newGroups, setNewGroups] = useState(0);
    const [totalGroups, setTotalGroups] = useState(0);
    const [deletedGroups, setDeletedGroups] = useState(0);
    const [existingGroups, setExistingGroups] = useState(0);
    const [pageNumber, setPageNumber] = useState(new URLSearchParams(location.search).get('page') ? parseInt(new URLSearchParams(location.search).get('page')) : 1);
    const [perPage, setperPage] = useState(0);
    const [totalItems, setTotalItems] = useState(0);
    const [pageRange, setPageRange] = useState(5);
    const [filterName, setfilterName] = useState("");
    const [filterCreated, setfilterCreated] = useState("");
    const [filterUpdated, setfilterUpdated] = useState("");
    const [filter, setFilter] = useState(0);
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState([]);

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

    // date range picker
    const [selectedDate, setSelectedDate] = useState()
    const handleCalenderChange = (date) => {
        if (!(moment(moment(date).format('YYYY-MM-DD'), 'YYYY-MM-DD', true).isValid())) {
            // setErrors({
            //     invalid_format_1: [t("invalid_format")],
            // });
            setfilterCreated("");
            setSelectedDate(null);
        }
        else {
            setfilterCreated(moment(date).format('YYYY-MM-DD'));
            setSelectedDate(date);
        }
    }
    const [selectedDate2, setSelectedDate2] = useState()
    const handleCalenderChange2 = (date) => {
        if (!(moment(moment(date).format('YYYY-MM-DD'), 'YYYY-MM-DD', true).isValid())) {
            // setErrors({
            //     invalid_format_2: [t("invalid_format")],
            // });
            setfilterUpdated("");
            setSelectedDate2(null);
        }
        else {
            setfilterUpdated(moment(date).format('YYYY-MM-DD'));
            setSelectedDate2(date);
        }
    }

    const getLists = () => {
        setLoading(true);
        const created = (filterCreated != '' ? moment.tz(filterCreated + " 12:00", localStorage.timezone).utc().format('YYYY-MM-DD') : '')
        const updated = (filterUpdated != '' ? moment.tz(filterUpdated + " 12:00", localStorage.timezone).utc().format('YYYY-MM-DD') : '')
        axios
            .get(
                "/api/get-groups" +
                "?page=" +
                pageNumber +
                "&name=" +
                filterName +
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
                setLists(response.data.data);
                setperPage(response.data.meta.per_page);
                setTotalItems(response.data.meta.total);
                setLoading(false);
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                setLoading(false);
            });
        setErrors([])
        setLoading(true);
        axios
            .post("/api/get-groups-info?lang=" + localStorage.lang)
            .then((response) => {
                // console.log(response.data);
                setNewGroups(response.data.new);
                setTotalGroups(response.data.total);
                setExistingGroups(response.data.existing);
                setDeletedGroups(response.data.deleted);
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
        getLists();
    }, [pageNumber, filter]);

    const handleListDelete = () => {
        const num = deleting;
        if (num) {
            setErrors([])
            setLoading(true);
            axios
                .post("/api/delete-group/" + num + "?lang=" + localStorage.lang)
                .then((response) => {
                    setLoading(false);
                    getLists();
                    Swal.fire({
                        title: t('Success'),
                        text: t('Your Mailing list has been deleted successfully!'),
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: t('OK'),
                        //cancelButtonText: 'No, keep it'
                    })
                })
                .catch((error) => {
                    Swal.fire({
                        title: t("oops!"),
                        text: t("mailing_list_will_not_be_deleted_if_it_is_included_in_an_active_campaign"),
                        icon: "warning",
                        showCancelButton: false,
                        confirmButtonText: t('OK')
                        //cancelButtonText: 'No, keep it'
                    });
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                    setLoading(false);
                });
            handleClose();
        }
    };

    const clearFilter = async () => {
        if (filterName != "") setfilterName("");
        if (filterCreated != "") setfilterCreated("");
        if (filterUpdated != "") setfilterUpdated("");
        setSelectedDate(null);
        setSelectedDate2(null);
        setFilter(!filter);
    };

    return (
        <Fragment>
            {loading ? <Spinner /> : null}
            <section className="right-canvas email-campaign">
                <Container fluid>
                    <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                        <div className="page-title">
                            <h1>{t('Mailing Lists')}</h1>
                        </div>
                        <div className="create-campaign">
                            <Link
                                to="/mailing-lists/create"
                                className="btn btn-secondary"
                            >
                                <span>
                                    {t('Create List')}{" "}
                                    <FontAwesomeIcon
                                        icon={faPlus}
                                        className="ms-2"
                                    />
                                </span>
                            </Link>
                        </div>
                    </div>
                    <Row>
                        <Col xxl="4" xl="4" lg="6" md="6" sm="6" xs="12">
                            <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                <span className="title">{t('Total Lists')}</span>
                                <span className="value">{totalGroups}</span>
                            </div>
                        </Col>
                        <Col xxl="4" xl="4" lg="6" md="6" sm="6" xs="12">
                            <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                <span className="title">{t('New Lists')}</span>
                                <span className="value">{newGroups}</span>
                            </div>
                        </Col>
                        <Col xxl="4" xl="4" lg="6" md="6" sm="6" xs="12">
                            <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                <span className="title">{t('deleted')}</span>
                                <span className="value">{deletedGroups}</span>
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
                                        <Form.Label>
                                            {t('Mailing List Name')}
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
                                            placeholder="e.g. MyList"
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
                                        {renderErrorFor("invalid_format_1")}
                                    </Form.Group>
                                </Col>
                                <Col xl="4" lg="6" md="12" xs="12">
                                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column">
                                        <Form.Label>{t('Last Modified')}</Form.Label>
                                        <DateTimePicker
                                            format="y-MM-dd"
                                            className="em-calendar w-100"
                                            onChange={(e) => handleCalenderChange2(e)} value={selectedDate2}
                                        />
                                        {renderErrorFor("invalid_format_2")}
                                    </Form.Group>
                                </Col>
                                <Col xl="12" xs="12" className="d-flex justify-content-end">
                                    <Form.Group className="btn-wrapper filter-btns mt-4 mb-4 d-flex flex-column">
                                        <div className="d-flex justify-content-between">
                                            <button
                                                type="button"
                                                className="btn btn-primary"
                                                onClick={() => { if (!hasErrorFor('invalid_format_2') || !hasErrorFor('invalid_format_2')) { getLists() } }
                                                }
                                            >
                                                <span>{t('Apply')}</span>
                                            </button>
                                            <button
                                                type="button"
                                                className="btn btn-secondary"
                                                onClick={() =>
                                                    clearFilter()
                                                }
                                            >
                                                <span>{t('Reset')}</span>
                                            </button>
                                        </div>
                                    </Form.Group>
                                </Col>
                            </Row>
                        </Form>

                        <div className="status-table">
                            <div className="table-responsive">
                                <Table className="em-table align-middle">
                                    <thead>
                                        <tr>
                                            {/* <th>Sr.</th> */}
                                            <th>{t('Mailing List Name')}</th>
                                            <th>{t('For Email')}</th>
                                            <th>{t('For SMS')}</th>
                                            <th>{t('Total Contacts')}</th>
                                            <th>{t('Date Created')}</th>
                                            <th>{t('Last Modified')}</th>
                                            <th>{t('Actions')}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {lists.length ? (
                                            lists.map((group, index) => (
                                                <tr key={group.hash_id}>
                                                    {/* <td>{(pageNumber - 1) * perPage + index + 1}</td> */}
                                                    <td className="text-capitalize">
                                                        {group.name}
                                                    </td>
                                                    <td className="text-center">
                                                        {group.for_email == 1 ?
                                                            <FontAwesomeIcon icon={faCheck} />
                                                            :
                                                            <FontAwesomeIcon icon={faMinus} />

                                                        }
                                                    </td>
                                                    <td className="text-center">
                                                        {group.for_sms == 1 ?
                                                            <FontAwesomeIcon icon={faCheck} />
                                                            :
                                                            <FontAwesomeIcon icon={faMinus} />
                                                        }
                                                    </td>
                                                    <td> {group.contacts.length} </td>
                                                    <td>
                                                        <Moment format="DD MMMM YYYY">
                                                            {moment.tz(moment(group.created_at).utc(), localStorage.timezone)}
                                                        </Moment>
                                                    </td>
                                                    <td>
                                                        <Moment format="DD MMMM YYYY">
                                                            {moment.tz(moment(group.updated_at).utc(), localStorage.timezone)}
                                                        </Moment>
                                                    </td>
                                                    <td>
                                                        <ul className="action-icons list-unstyled">
                                                            <li>
                                                                <Link
                                                                    to={
                                                                        "/mailing-lists/" +
                                                                        group.hash_id +
                                                                        "/edit?page=" + pageNumber
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
                                                                <Link
                                                                    to={
                                                                        "/mailing-lists/" +
                                                                        group.hash_id + "?page=" + pageNumber
                                                                    }
                                                                    className="view-icon"
                                                                    title={t("Contacts List")}
                                                                >
                                                                    <FontAwesomeIcon
                                                                        icon={
                                                                            faListAlt
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
                                                                        group.hash_id
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
                                                <td className="text-center" colSpan="7">
                                                    {t('No Mailing Lists Found')}
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
                className="em-modal dlt-modal"
                centered
            >
                <Modal.Body className="d-flex align-items-center justify-content-center flex-column">
                    <span className="dlt-icon">
                        <FontAwesomeIcon icon={faTrashAlt} />
                    </span>
                    <p>{t('Are you sure you want to delete Mailing List?')}</p>
                </Modal.Body>
                <Modal.Footer className="justify-content-center">
                    <Button variant="primary" onClick={handleListDelete}>
                        <span>{t('Yes')}</span>
                    </Button>
                    <Button variant="secondary" onClick={handleClose}>
                        <span>{t('Cancel')}</span>
                    </Button>
                </Modal.Footer>
            </Modal>
        </Fragment>
    );
}

export default withTranslation()(AllMailingLists);
