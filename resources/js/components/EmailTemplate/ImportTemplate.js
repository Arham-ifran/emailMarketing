import React, { useEffect, useState, useRef, useCallback } from 'react';
import { useLocation, Link, useHistory } from 'react-router-dom';
import { Container, Form, Button, Row, Col } from 'react-bootstrap';
import EmailEditor from 'react-email-editor';
import Spinner from '../includes/spinner/Spinner';
import Swal from 'sweetalert2';
import html2canvas from 'html2canvas';
// import { CKEditor } from '@ckeditor/ckeditor5-react';
// import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
// import ReactSummernote from "react-summernote";
// import "react-summernote/dist/react-summernote.css"; // import styles
import 'react-trumbowyg/dist/trumbowyg.min.css'
import Trumbowyg from 'react-trumbowyg'

const queryString = require('query-string');
import { withTranslation } from 'react-i18next';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faInfoCircle } from '@fortawesome/free-solid-svg-icons';
function ImportTemplate(props) {
	const { t } = props;
	const history = useHistory();
	const [loading, setLoading] = useState('');
	const [errors, setErrors] = useState([]);
	const [templateHtml, setTemplateHtml] = useState(`<p>${t("HTML code Preview")}</p>`);
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

	function isValidHTML(html) {
		const parser = new DOMParser();
		const doc = parser.parseFromString(html, 'text/xml');
		if (doc.documentElement.querySelector('parsererror')) {
			return false;
		} else {
			return true;
		}
	}

	const getTemplate = (id) => {
		setLoading(true);
		axios.get(`/api/campaign-template/${id}/edit?lang=` + localStorage.lang, {
			params: {
				_id: id
			},
			responseType: 'json'
		})
			.then((res) => {
				if (res.data.data.type == 1) {
					window.location.replace(`/email-template/${id}/edit`);
				}
				setTemplateHtml(res.data.data.html_content);
				setTemplateName(res.data.data.name);
				setLoading(false);
			})
			.catch((error) => {
				setLoading(false);
			});
	}

	const goBack = (back = false) => {
		var url = new URL(window.location.href);
		const camp_id = (url.searchParams.get("id"));
		const camp = (url.searchParams.get("campaign"));
		if (camp_id && camp) {
			window.location.replace("/" + camp + "/" + camp_id + "/edit");
		}
		else {
			let params = new URLSearchParams(location.search);
			if (params.get('page')) {
				window.location.href = "/template/list?page1=" + params.get('page');
			}
			else {
				window.location.href = "/template/list";
			}
		}
	}

	useEffect(() => {
		let parseUriSegment = window.location.pathname.split("/");
		let id = '';
		if (parseUriSegment.indexOf('campaign-template') && parseUriSegment.indexOf('edithtml') != -1) {
			id = parseUriSegment[2];
			setTemplateId(id);
			getTemplate(id);
		}
		// ClassicEditor
		// 	.create(document.querySelector('#editor'), {
		// 		removePlugins: ['ImageToolbar', 'ImageCaption', 'ImageStyle'],
		// 		image: {}
		// 	})
		// 	.then(editor => {
		// 		window.editor = editor;
		// 	})
		// 	.catch(error => {
		// 		console.error('There was a problem initializing the editor.', error);
		// 	});

	}, []);

	const handleSubmit = (event) => {
		event.preventDefault();
		const html = document.getElementById('react-trumbowyg').innerHTML;
		setTemplateHtml(html);
		setLoading(true);
		let parseUriSegment = window.location.pathname.split("/");
		let id = '';
		if (parseUriSegment.indexOf('campaign-template') && parseUriSegment.indexOf('edithtml') != -1) {
			id = parseUriSegment[2];
		}
		// const html = templateHtml;
		setErrors([])

		// console.log(html);
		// for image
		if (html) {
			const html2 = "<!DOCTYPE html><html><body style='background-color:#000; padding:0px; margin:0;'>" + html + "</body></html>"
			var bodyHtml = "<div id='mine'>" + /<body.*?>([\s\S]*)<\/body>/.exec(html2)[1] + "</div>";
			var s = bodyHtml;
			var temp = document.createElement('div');
			temp.innerHTML = s;
			var htmlObject = temp.firstChild;
			document.getElementById('capture').appendChild(htmlObject);
			html2canvas(document.getElementById('mine'), { allowTaint: true, useCORS: true }).then(canvas => {
				const image = canvas.toDataURL("image/png");
				// setLoading(false);
				// console.log(image);
				axios.post('/api/campaign-template/create-update?lang=' + localStorage.lang, {
					name: templateName,
					type: 2,
					content: '',
					html_content: html,
					id: id,
					image: image ? image : ''
				})
					.then(res => {
						setLoading(false);
						if (res.response) {
							if (res.response.data.errors) {
								setErrors(res.response.data.errors);
							}
						} else {
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
						}
					})
					.catch(error => {
						setLoading(false);
						if (error.response) {
							if (error.response.data.errors) {
								setErrors(error.response.data.errors);
							}
						}
					})
			});
			document.getElementById('capture').style.display = "none";
		}
		else {
			setErrors({
				html: [html == "" ? t('empty_html') : t('invalid_html')],
			});
			setLoading(false);
		}
	}

	const importHTML = (event) => {
		console.log(event.target.files[0]);
		var f = event.target.files[0];

		if (f) {
			var r = new FileReader();
			r.onload = function (e) {
				var contents = e.target.result;
				setTemplateHtml(contents);
				Swal.fire({
					title: t('Success'),
					text: t('the_imported_file_will_replace_already_added_html_code'),
					icon: 'success',
					showCancelButton: false,
					confirmButtonText: t('OK'),
					//cancelButtonText: 'No, keep it'
				})
			}
			r.readAsText(f);
		} else {
			alert("Failed to load file");
		}
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

				<Row className="temp-file-box">
					<Col xl="6" lg="12" md="12" sm="12">
						<Form.Group className="d-flex align-items-center">
							<Form.Label forhtml="Import Contacts" className="me-2">
								{t('import')}
							</Form.Label>
							<div className="flex-fill input-holder">
								<input
									type="file"
									name="file"
									className="custom-file-input form-control"
									id="customFile"
									onChange={importHTML}
									accept=".html"
								/>
								{renderErrorFor('file')}
							</div>
						</Form.Group>
					</Col>
					<Col xl="6" lg="12" md="12" sm="12">
						<div className='file-info'>
							<p>
								<FontAwesomeIcon icon={faInfoCircle}></FontAwesomeIcon>
								{" "}
								{t('the imported file will replace already added html code')}
							</p>
						</div>
					</Col>
				</Row>

				<div className="rounded-box-shadow bg-white export-html-box">

					<div className="info">
						<div className="alert-info p-2" role="alert">
							<p>{t('On sending campaign, following keywords with double curly brackets')} {t('e.g')} <strong> {"{{" + t('keyword') + "}}"} </strong> {t('will be replaced by their values')}:</p>
							<li><strong>{t('Name')}</strong> : {t("Contacts full name")}  </li>
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
									placeholder={t("Template Name")}
								/>
								{renderErrorFor('name')}
							</div>
						</Form.Group>
						<Form.Group className="mb-3 mb-md-4 d-flex">
							<Form.Label className="mb-2 mb-md-0" htmlFor="sms-text">{t('Design Template')} <b className="req-sign">*</b></Form.Label>
							{/* <div className="flex-fill input-holder">
								<textarea id="name" rows="5" cols="5" value={templateHtml} onChange={(e) => setTemplateHtml(e.target.value)} className="form-control" />
							</div> */}
							{/* editor */}
							{/* <CKEditor
								editor={ClassicEditor}
								data={templateHtml}
								onReady={editor => {
									// You can store the "editor" and use when it is needed.
									console.log('Editor is ready to use!', editor);
								}}
								onChange={(event, editor) => {
									const data = editor.getData();
									console.log({ event, editor, data });
									setTemplateHtml(data)
								}}
							/> */}
							<Trumbowyg
								data={templateHtml}
								id='react-trumbowyg'
								onChange={(e) => { setTemplateHtml(e.target.innerHTML); }}
								placeholder={t("HTML code Preview")}
							/>
							{/* editor end */}
							{renderErrorFor('html')}
						</Form.Group>


						<div className="btns-holder right-btns d-flex flex-row-reverse pt-5">
							<button
								type="submit"
								className="btn btn-primary ms-3 ml-2 mb-3"
							>
								<span>{templateId ? t('Update') : t('Create')}</span>
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
				</div>

				<div id="capture" className='p-5'></div>
			</Container>
		</React.Fragment>
	);
}
export default withTranslation()(ImportTemplate);