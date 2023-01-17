import React, { useState, useEffect, useMemo } from "react";
import { Link } from "react-router-dom";
import Select from "react-select";
import { Container, Form, Row, Col } from "react-bootstrap";
import Spinner from "../../includes/spinner/Spinner";
import "./css/CreateContact.css";
import Swal from 'sweetalert2';
import PhoneInput from 'react-phone-number-input'
import 'react-phone-number-input/style.css'
import { withTranslation } from 'react-i18next';

function CreateContact(props) {
    const { t } = props;
    const [selectedOption, setSelectedOption] = useState("");
    const [options, setOptions] = useState([]);
    const [loading, setLoading] = useState(false);

    const [first_name, setFname] = useState("");
    const [last_name, setLname] = useState("");
    const [email, setEmail] = useState("");
    const [country_code, setCountry] = useState("");
    const [number, setNumber] = useState("");
    const [double_opt_in, setDoi] = useState(0);
    const [for_sms, setFor_sms] = useState(0);
    const [for_email, setFor_Email] = useState(0);
    const [errors, setErrors] = useState([]);
    const [disabledForemail, setdisabledForemail] = useState(0);
    const [disabledForsms, setdisabledForsms] = useState(0);
    const [limit, setLimit] = useState('');
    const [contactsLimit, setContactsLimit] = useState(0);

    const handleDoi = () => { setDoi(!double_opt_in); };
    const handleForsms = () => { setFor_sms(!for_sms); };
    const handleForemail = () => { setFor_Email(!for_email); };

    const hasErrorFor = (field) => { return !!errors[field] }

    const renderErrorFor = (field) => {
        if (hasErrorFor(field)) {
            if (field == 'error_message' || field == 'for.0') {
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

    const handleChange = (selectedOption) => {

        // console.log(selectedOption,"selectedOption");

        setSelectedOption(selectedOption.value);

        setdisabledForemail(0);
        setdisabledForsms(0);

        if (selectedOption.email && selectedOption.email == 1) {
            setFor_Email(selectedOption.email);
            setdisabledForemail(1);
        } else {
            setFor_Email(0);
        }
        if (selectedOption.sms && selectedOption.sms == 1) {
            setFor_sms(selectedOption.sms);
            setdisabledForsms(1);
        } else {
            setFor_sms(0);
        }
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
                            label: row.name,
                            value: row.hash_id,
                            sms: row.for_sms,
                            email: row.for_email,

                        })),
                    ];

                    const opt = data.concat(moredata[0]);
                    setOptions(opt);
                    setLoading(false);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                    setLoading(false);
                });
        };
        getMailingLists();
    }, []);

    const handleSubmit = async (event) => {
        event.preventDefault();
        setLoading(true);

        if (first_name == "" && last_name == "") {
            setErrors({
                first_name: [first_name == "" ? t('required') : ''],
                last_name: [last_name == "" ? t('required') : ''],
            });
            setLoading(false);
            return;
        }

        const data = {
            first_name: [first_name],
            last_name: [last_name],
            email: [email],
            country_code: [country_code],
            number: [number],
            double_opt_in: [double_opt_in],
            for_sms: [for_sms],
            for_email: [for_email],
        };
        if (selectedOption != "") {
            data.list = selectedOption;
        }
        setErrors([]);
        await axios
            .post("/api/add-contacts?lang=" + localStorage.lang, data)
            .then((response) => {
                // console.log(response);
                setLoading(false);
                Swal.fire({
                    title: t('Success'),
                    text: t('Your Contact has been added successfully!'),
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                    //cancelButtonText: 'No, keep it'
                }).then((result) => {
                    window.location.href = "/contacts";
                });
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

    return (
        <React.Fragment>
            {loading ? <Spinner /> : null}
            {/* <Row>
                {limit ?
                    <Col xs="12" className="bg-danger text-center text-white p-2">
                        {limit}
                    </Col>
                    :
                    <Col xs="12" className="bg-info text-center p-2">
                        {t('contacts_remaining_in_package')}: {contactsLimit}
                    </Col>
                }
            </Row> */}

            <Container fluid>
                <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                    <div className="page-title">
                        <h1>{t('Add Contact')}</h1>
                    </div>
                </div>
                <Form
                    className="create-form-holder rounded-box-shadow bg-white"
                    onSubmit={handleSubmit}
                >
                    <Form.Group className="mb-3 mb-md-4 d-flex">
                        <Form.Label htmlFor="first-name">{t('First Name')} <b className="req-sign">*</b></Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                name="first_name"
                                className="form-control"
                                type="text"
                                onChange={(e) => setFname(e.target.value)}
                                placeholder='e.g. Sam'
                            />
                            {renderErrorFor('first_name.0')}
                            {renderErrorFor('first_name')}
                        </div>
                    </Form.Group>
                    <Form.Group className="mb-3 mb-md-4 d-flex">
                        <Form.Label htmlFor="last-name">{t('Last Name')} <b className="req-sign">*</b></Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                name="last_name"
                                className="form-control"
                                type="text"
                                onChange={(e) => setLname(e.target.value)}
                                placeholder='e.g. Smith'
                            />
                            {renderErrorFor('last_name.0')}
                            {renderErrorFor('last_name')}
                        </div>
                    </Form.Group>
                    <Form.Group className="mb-3 mb-md-4 d-flex">
                        <Form.Label htmlFor="email">{t('Email')}</Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                name="email"
                                className="form-control"
                                type="text"
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder='e.g. example@email.com'
                            />
                            {renderErrorFor('email.0')}
                        </div>
                    </Form.Group>
                    <Form.Group className="mb-3 mb-md-4 d-flex">
                        <Form.Label htmlFor="country">{t('Phone Number')}</Form.Label>
                        <div className="flex-fill input-holder  flex-column">
                            {/* <input
                                name="country_code"
                                className="form-control"
                                type="text"
                                onChange={(e) => setCountry(e.target.value)}
                            /> */}
                            <PhoneInput
                                placeholder={t("Enter phone number")}
                                className="form-control"
                                onChange={number => setNumber(number)}
                                placeholder="e.g. +49 1579230198"
                            />
                            {/* {renderErrorFor('country_code')} */}
                            {renderErrorFor('number.0')}
                        </div>
                    </Form.Group>
                    {/* <Form.Group className="mb-3 mb-md-4 d-flex">
                        <Form.Label htmlFor="number">Phone Number</Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                name="number"
                                className="form-control"
                                type="text"
                                onChange={(e) => setNumber(e.target.value)}
                            />
                        </div>
                    </Form.Group> */}
                    <Form.Group className="mb-3 mb-md-4 d-flex">
                        <Form.Label htmlFor="for_sms">
                            {t('For')} <b className="req-sign">*</b>
                        </Form.Label>
                        <div className="d-flex flex-row align-items-center justify-content-start flex-fill input-holder sms-email-wrap">
                            <label> {t('SMS')} </label>
                            <input
                                name="for_sms"
                                className="form-checkbox"
                                disabled={disabledForsms}
                                type="checkbox"
                                checked={for_sms}
                                onClick={(e) => handleForsms(e)}
                            />
                            <label > {t('Email')} </label>
                            <input
                                name="for_email"
                                className="form-checkbox"
                                disabled={disabledForemail}
                                type="checkbox"
                                checked={for_email}
                                onClick={(e) => handleForemail(e)}
                            />
                            {renderErrorFor('for_sms.0')}
                            {renderErrorFor('for_email.0')}
                            {renderErrorFor('for.0')}
                        </div>
                    </Form.Group>
                    {/* <Form.Group className="mb-3 mb-md-4 d-flex">
                        <Form.Label htmlFor="double_opt_in">
                            Double opt in
                        </Form.Label>
                        <div className="flex-fill input-holder">
                            <input
                                name="double_opt_in"
                                className="form-checkbox"
                                type="checkbox"
                                onClick={(e) => handleDoi(e)}
                            />
                            {renderErrorFor('double_opt_in')}
                        </div>
                    </Form.Group> */}
                    <Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
                        <Form.Label htmlFor="mailing-list">
                            {t('Add Contact to Mailing List')}
                        </Form.Label>
                        <div className="flex-fill input-holder">
                            <div className="subscriber-select">
                                <Select
                                    onChange={(e) => handleChange(e)}
                                    options={options}
                                    classNamePrefix="react-select"
                                    placeholder={t("Select Mailing List")}
                                />
                            </div>
                        </div>
                    </Form.Group>
                    {/* <div className="btns-holder right-btns d-flex flex-row-reverse pt-5">
                        <Link
                            to="/my-mailing-list"
                            className="btn btn-primary ms-3 mb-3"
                        >
                            <span>Next</span>
                        </Link>
                        <Link
                            to="/split-testing"
                            className="btn btn-secondary ms-3 mb-3"
                        >
                            <span>Back</span>
                        </Link>
                    </div> */}
                    {renderErrorFor('error_message')}
                    <div className="btns-holder right-btns d-flex flex-row-reverse pt-5">
                        <button
                            type="submit"
                            className="btn btn-primary ms-3 ml-2 mb-3"
                        >
                            <span>{t('Create')}</span>
                        </button>
                        <Link
                            to="/contacts"
                            className="btn btn-secondary ms-3 mb-3"
                        >
                            <span>{t('Back')}</span>
                        </Link>
                    </div>
                </Form>
            </Container>
        </React.Fragment>
    );
}

export default withTranslation()(CreateContact);
