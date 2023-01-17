import React, { useEffect, useState, useRef, useCallback } from 'react';
import { useLocation, Link, useHistory } from 'react-router-dom';
import { Container, Form, Button } from 'react-bootstrap';
import Spinner from '../includes/spinner/Spinner';
import Swal from 'sweetalert2';
import { withTranslation } from 'react-i18next';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faInfoCircle } from "@fortawesome/free-solid-svg-icons";
function AddSmsTemplate(props) {
	const { t } = props;
	const history = useHistory();
	const [loading, setLoading] = useState('');
	const [errors, setErrors] = useState([]);
	const [templateMessage, setTemplateMessage] = useState([]);
	const [templateName, setTemplateName] = useState([]);
	const [templateId, setTemplateId] = useState('');

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

	const getTemplate = (id) => {
		setLoading(true);
		axios.get('/api/get-sms-template/' + id + '?lang=' + localStorage.lang)
			.then((res) => {
				setLoading(false);
				setTemplateName(res.data.data.name);
				setTemplateMessage(res.data.data.message);
			})
			.catch((error) => {
				setLoading(false);
			});
	}

	const [SmsCamp, setSmsCamp] = useState("");
	const goBack = (back = false) => {
		if (SmsCamp) {
			window.location.href = "/sms-campaign/" + SmsCamp + "/edit";
		} else {
			let params = new URLSearchParams(location.search);
			if (params.get('page')) {
				window.location.href = "/template/list?page2=" + params.get('page');
			}
			else {
				window.location.href = "/template/list";
			}
		}
	}

	useEffect(() => {
		let parseUriSegment = window.location.pathname.split("/");
		let id = '';
		if (parseUriSegment.indexOf('sms-template') && parseUriSegment.indexOf('edit') != -1) {
			id = parseUriSegment[2];
			setTemplateId(id);
			getTemplate(id);
		}
		let params = new URLSearchParams(location.search);
		if (params.get('sms')) {
			setSmsCamp(params.get('sms'));
		}
	}, []);

	const handleSubmit = (event) => {
		event.preventDefault();
		setLoading(true);
		let parseUriSegment = window.location.pathname.split("/");
		let id = '';
		if (parseUriSegment.indexOf('sms-template') && parseUriSegment.indexOf('edit') != -1) {
			id = parseUriSegment[2];
		}
		setErrors([])

		axios.post('/api/add-sms-templates?lang=' + localStorage.lang, {
			name: templateName,
			message: templateMessage,
			id: id
		})
			.then(res => {
				setLoading(false);
				if (res.data.status) {
					{
						const Message = id ? t('Your template has been updated successfully!') : t('Your template has been created successfully!');
						Swal.fire({
							title: t('Success'),
							text: Message,
							icon: 'success',
							showCancelButton: false,
							confirmButtonText: t('OK'),
							//cancelButtonText: 'No, keep it'
						}).then((result) => {
							goBack()
						});
					}
				}
			})
			.catch(error => {
				setLoading(false);
				if (error.response) {
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
				}
			});
		setLoading(false);
	}

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>{templateId ? t('edit_template') : t('Create Template')}</h1>
					</div>
				</div>

				<div className="rounded-box-shadow bg-white export-html-box">

					<div className="info">
						<div className="alert-info p-2" role="alert">
							<p>{t('On sending campaign, following keywords with double curly brackets')} e.g <strong> {"{{" + t('keyword') + "}}"} </strong> {t('will be replaced by their values')}:</p>
							<li><strong>{t('name')}</strong> : {t("Contacts full name")}  </li>
						</div>
					</div>

					<Form
						className="create-form-holder rounded-box-shadow bg-white"
						onSubmit={handleSubmit}
					>
						<Form.Group className="mb-3 mb-md-4 d-flex">
							<Form.Label htmlFor="first-name">{t('Template Name')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<input
									name="name"
									className="form-control"
									type="text"
									value={templateName}
									onChange={(e) => setTemplateName(e.target.value)}
								/>
								{renderErrorFor('name')}
							</div>
						</Form.Group>
						<Form.Group className="mb-3 mb-md-4 d-flex">
							<Form.Label className="mb-2 mb-md-0" htmlFor="sms-text">{t('Message')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<textarea id="name" rows="5" cols="5" maxLength='250' value={templateMessage} onChange={(e) => setTemplateMessage(e.target.value)} className="form-control" />
								<small> {250 - templateMessage.length} {t('characters_remaining')} </small>
								<p>
									<FontAwesomeIcon icon={faInfoCircle}></FontAwesomeIcon>
									{" "}
									{t('limit_is_250_characters_including_spaces')}
								</p>
								{renderErrorFor('message')}
							</div>
						</Form.Group>
						<div className="btns-holder right-btns d-flex flex-row-reverse pt-5  btn-in-bg">
							<button
								type="submit"
								className="btn btn-primary ms-3 ml-2 mb-3"
							>
								<span>{templateId ? t('Update') : t('Create')}</span>
							</button>
							<Link
								onClick={() => goBack(true)}
								className="btn btn-secondary ms-3 mb-3"
							>
								<span>{t('Back')}</span>
							</Link>
						</div>
					</Form>
				</div>
				<div id="capture"></div>
			</Container>
		</React.Fragment>
	);
}
export default withTranslation()(AddSmsTemplate);