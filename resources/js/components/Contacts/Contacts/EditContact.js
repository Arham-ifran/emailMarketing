import React, { useState, useEffect } from "react";
import { Link, useParams } from "react-router-dom";
import Select from "react-select";
import { Container, Row, Col, Form } from "react-bootstrap";
import Spinner from "../../includes/spinner/Spinner";
import "./css/EditContact.css";
import Swal from 'sweetalert2';
import PhoneInput from 'react-phone-number-input'
import { withTranslation } from 'react-i18next';

function EditContact(props) {
    const { t } = props;
    const [first_name, setFname] = useState("");
    const [last_name, setLname] = useState("");
    const [email, setEmail] = useState("");
    const [country_code, setCountry] = useState("");
    const [number, setNumber] = useState("");
    const { contactId } = useParams();
    const [double_opt_in, setDoi] = useState();
    const [for_sms, setFor_sms] = useState();
    const [for_email, setFor_Email] = useState();
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState([]);
    const [groups, setGroups] = useState([]);
    const [trigger, setTrigger] = useState([]);
    const [selectedOption, setSelectedOption] = useState("");
    const [options, setOptions] = useState([]);

    const handleDoi = () => { setDoi(!double_opt_in); };
    const handleForsms = () => { setFor_sms(!for_sms); };
    const handleForemail = () => { setFor_Email(!for_email); };
    const [disabledForemail, setdisabledForemail] = useState(0);
    const [disabledForsms, setdisabledForsms] = useState(0);

    const handleChange = (selected) => {
        setSelectedOption(selected.value);
        setdisabledForemail(0);
        setdisabledForsms(0);
        if (selected.email && selected.email == 1) {
            setFor_Email(1);
            setdisabledForemail(1);
        } else {
            // setFor_Email(0);
        }
        if (selected.sms && selected.sms == 1) {
            setFor_sms(1);
            setdisabledForsms(1);
        } else {
            // setFor_sms(0);
        }
    };

    const hasErrorFor = (field) => { return !!errors[field] }
    const renderErrorFor = (field) => {
        if (hasErrorFor(field)) {
            if (field == 'for.0') {
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

    useEffect(() => {
        const getContact = () => {
            setLoading(true);
            axios
                .get("/api/get-contact/" + contactId + "?lang=" + localStorage.lang)
                .then((response) => {
                    setLoading(false);
                    const data_received = response.data.data[0];

                    setFname(data_received.first_name);
                    setLname(data_received.last_name);
                    setEmail(data_received.email);
                    setNumber(data_received.number);
                    setDoi(data_received.double_opt_in);
                    setFor_sms(data_received.for_sms);
                    setFor_Email(data_received.for_email);
                    setGroups(data_received.groups);
                    setSelectedOption(data_received.groups[0].hash_id)

                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                    setLoading(false);
                });
        };
        getContact();
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

    const goBack = () => {
        let params = new URLSearchParams(location.search);
        if (params.get('page')) {
            window.location.href = "/contacts?page=" + params.get('page');
        }
        else {
            window.location.href = "/contacts";
        }
    }

    const handleSubmit = (event) => {
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
            id: [contactId],
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
        axios
            .post("/api/edit-contacts?lang=" + localStorage.lang, data)
            .then((res) => {
                setLoading(false);
                Swal.fire({
                    title: t('Success'),
                    text: t('Your Contact has been updated successfully!'),
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                    //cancelButtonText: 'No, keep it'
                }).then((result) => {
                    goBack()
                });
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                setLoading(false);
            });
    };

    return (
        <React.Fragment>
            {loading ? <Spinner /> : null}
            <Container fluid>
                <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
                    <div className="page-title">
                        <h1>{t('View / Edit Contact')}</h1>
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
                                value={first_name}
                                className="form-control"
                                type="text"
                                onChange={(e) => setFname(e.target.value)}
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
                                value={last_name}
                                className="form-control"
                                type="text"
                                onChange={(e) => setLname(e.target.value)}
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
                                value={email}
                                className="form-control"
                                type="text"
                                onChange={(e) => setEmail(e.target.value)}
                            />
                            {renderErrorFor('email.0')}
                        </div>
                    </Form.Group>
                    <Form.Group className="mb-3 mb-md-4 d-flex">
                        <Form.Label htmlFor="country">{t('Phone Number')}</Form.Label>
                        <div className="form-control d-flex flex-fill input-holder reduce-gap flex-column">
                            <PhoneInput
                                value={number}
                                placeholder={t("Enter phone number")}
                                onChange={number => setNumber(number)}
                            />
                            {/* {renderErrorFor('country_code')} */}
                            <div className="mb-1">
                                {renderErrorFor('number.0')}
                            </div>
                        </div>
                    </Form.Group>
                    <Form.Group className="mb-3 mb-md-4 d-flex">
                        <Form.Label htmlFor="For">
                            {t('For')} <b className="req-sign">*</b>
                        </Form.Label>
                        <div className="d-flex flex-row align-items-center justify-content-start flex-fill input-holder sms-email-wrap">
                            <label> {t('SMS')} </label>
                            <input
                                name="for_sms"
                                className="form-checkbox"
                                type="checkbox"
                                disabled={disabledForsms}
                                checked={for_sms == 1 ? true : false}
                                onClick={(e) => handleForsms(e)}
                            />
                            <label > {t('Email')} </label>
                            <input
                                name="for_email"
                                className="form-checkbox"
                                type="checkbox"
                                disabled={disabledForemail}
                                checked={for_email == 1 ? true : false}
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
                                checked={double_opt_in}
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
                                    value={options.find(o => o.value == selectedOption)}
                                    classNamePrefix="react-select"
                                    placeholder={t("Select Mailing List")}
                                />
                            </div>
                        </div>
                    </Form.Group>
                    <div className="btns-holder right-btns d-flex flex-row-reverse pt-5">
                        {/* Save button will be shonw in Edit Mode
						<Link to="/my-mailing-list" className="btn btn-primary ms-3 mb-3"><span>Save</span></Link> */}
                        <Link
                            // to="/contacts"
                            onClick={() => goBack()}
                            className="btn btn-secondary ms-3 ml-2 mb-3"
                        >
                            <span>{t('Back')}</span>
                        </Link>
                        <button
                            type="submit"
                            className="btn btn-primary ms-3 mb-3"
                        >
                            <span>{t('Save')}</span>
                        </button>
                    </div>
                </Form>
            </Container>
        </React.Fragment>
    );
}

export default withTranslation()(EditContact);
