import React, { useEffect, useState, useRef, useCallback } from 'react';
import { useLocation, Link, useHistory } from 'react-router-dom';
import { Container, Form, Button } from 'react-bootstrap';
import EmailEditor from 'react-email-editor';
import Spinner from '../includes/spinner/Spinner';
import Swal from 'sweetalert2';
import html2canvas from 'html2canvas';
import { withTranslation } from 'react-i18next';
const queryString = require('query-string');
// import Swal from 'sweetalert2';
// import { daysOfWeek, daysOfMonth, MonthsOfYear } from '../../constants';

const export_html_template = '';
function CreateEmailTemplate(props) {
	const { t } = props;
	const emailEditorRef = useRef(null);
	const [errors, setErrors] = useState([]);
	const history = useHistory();

	const [templateName, setTemplateName] = useState('');
	const [templateHtml, setTemplateHtml] = useState('');
	const [templateVal, setTemplateVal] = useState('');
	const [currentStep, setCurrentStep] = useState(1);
	const [loading, setLoading] = useState('');
	const [templateId, setTemplateId] = useState('');

	var saveData = (function () {
		var a = document.createElement("a");
		return function (data2, fileName) {
			var json = data2,
				blob = new Blob([json], { type: "octet/stream" }),
				url = window.URL.createObjectURL(blob);
			a.href = url;
			a.download = fileName;
			a.click();
			window.URL.revokeObjectURL(url);
		};
	}());

	const exportHtml = () => {
		if (emailEditorRef.current != null) {
			emailEditorRef.current.editor.exportHtml((data) => {
				const { design, html } = data;
				var data2 = html;
				var fileName = "design.html";
				saveData(data2, fileName);
			});
		}
	};

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
				window.location.href = "/contacts?page=" + params.get('page');
			}
			else {
				window.location.href = "/contacts";
			}
			window.location.href = "/template/list";
		}
	}

	const saveDesign = () => {
		var url = new URL(window.location.href);
		const camp_id = (url.searchParams.get("id"));
		const camp = (url.searchParams.get("campaign"));

		if (emailEditorRef.current != null) {
			emailEditorRef.current.editor.exportHtml((data) => {
				let parseUriSegment = window.location.pathname.split("/");
				let id = '';
				if (parseUriSegment.indexOf('campaign-template') && parseUriSegment.indexOf('edit') != -1) {
					id = parseUriSegment[2];
				}
				const { design, html } = data;
				setErrors([])

				// for image
				var bodyHtml = "<div id='mine'>" + /<body.*?>([\s\S]*)<\/body>/.exec(html)[1] + "</div>";
				var s = bodyHtml;
				var temp = document.createElement('div');
				temp.innerHTML = s;
				var htmlObject = temp.firstChild;
				document.getElementById('capture').appendChild(htmlObject);
				html2canvas(document.getElementById('mine'), { allowTaint: false, useCORS: true }).then(canvas => {
					const image = canvas.toDataURL("image/png");
					// console.log(image);
					setLoading(true);
					axios.post('/api/campaign-template/create-update?lang=' + localStorage.lang, {
						name: templateName,
						type: 1,
						content: design,
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

										const Message = id ? `Your template has been updated successfully!` : `Your template has been created successfully!`;
										Swal.fire({
											title: t('Success'),
											text: Message,
											icon: 'success',
											showCancelButton: false,
											confirmButtonText: t('OK'),
											//cancelButtonText: 'No, keep it'
										}).then((result) => {
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
				// end for image
			});
		}

		if (emailEditorRef.current != null) {
			emailEditorRef.current.saveDesign((design) => {
			});
		}
	};

	const onLoad = () => {
		const parsed = queryString.parse(window.location.search, {});

		let parseUriSegment = window.location.pathname.split("/");
		if (parseUriSegment.indexOf('campaign-template') && parseUriSegment.indexOf('edit') != -1) {

			setLoading(true);
			axios.get(`/api/campaign-template/${parseUriSegment[2]}/edit?lang=` + localStorage.lang, {
				params: {
					_id: parseUriSegment[2]
				},
				responseType: 'json'
			})
				.then((res) => {
					if (res.data.data.type == 2) {
						window.location.replace(`/email-template/${parseUriSegment[2]}/edithtml`);
					}
					setTemplateVal(res.data.data.content);
					setTemplateHtml(res.data.data.html_content);
					setTemplateName(res.data.data.name);
					setLoading(false);
				})
				.catch((error) => {
					setLoading(false);
				});
		}



		if (emailEditorRef.current != null) {
			emailEditorRef.current.editor.registerCallback('image', function (file, done) {
				var data = new FormData();
				data.append('file', file.attachments[0])

				fetch('/api/upload-template-image', {
					method: 'POST',
					headers: {
						'Accept': 'application/json'
					},
					body: data
				}).then(response => {
					// Make sure the response was valid
					if (response.status >= 200 && response.status < 300) {
						return response
					} else {
						var error = new Error(response.statusText)
						error.response = response
						throw error
					}
				}).then(response => {
					return response.json()
				}).then(data => {
					// Pass the URL back to Unlayer to mark this upload as completed
					done({ progress: 100, url: data.image_url })
				})
			});
		}

	};

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


	useEffect(() => {
		if (templateVal && emailEditorRef.current != null) {
			let parsedData = JSON.parse(templateVal);
			emailEditorRef.current.editor.loadDesign(parsedData);
		}
	}, [templateVal]);


	useEffect(() => {
		if (emailEditorRef.current != null) {
			let parseUriSegment = window.location.pathname.split("/");
			if (parseUriSegment.indexOf('campaign-template') && parseUriSegment.indexOf('edit') != -1) {
				setTemplateId(parseUriSegment[2]);
				//getCampaignTemplate(parseUriSegment[2]);
			}
			//}, [options]);
		}
	}, [emailEditorRef.current]);

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
					{/* <Form.Group className={`mb-3 mb-md-4 ${currentStep == 2 ? ' d-none' : ''}`}> */}
					<Form.Group className={`mb-3 mb-md-4`}>
						<Form.Label className="mb-lg-0 mb-2" htmlFor="template-name">
							{t('Template Name')} <b className="req-sign">*</b>
						</Form.Label>
						<div className="flex-fill input-holder">
							<input id="template-name" className="form-control" type="text" onChange={(e) => setTemplateName(e.target.value)} value={templateName} placeholder={t("Template Name")} />
							{renderErrorFor('name')}
						</div>
					</Form.Group>

					<div className=''>
						{/* <div className={` ${currentStep == 1 ? ' d-none' : ''}`}> */}
						{/* <div>
							<button onClick={exportHtml} className="btn btn-primary"><span>Export HTML</span></button>
						</div> */}
						<div className="info mb-3">
							<div className="alert-info p-2" role="alert">
								<p>{t('On sending campaign, following keywords with double curly brackets')} {t('e.g')} <strong> {"{{" + t('keyword') + "}}"} </strong> {t('will be replaced by their values')}:</p>
								<li><strong>{t('Name')}</strong> : {t("Contacts full name")}  </li>
							</div>
						</div>
						<div id="template-viewer">
							<EmailEditor
								ref={emailEditorRef}
								onLoad={onLoad}
								//onLoad={loadDesign}
								displayMode='email'
								className="my-iframe"
								appearance={
									{
										theme: 'light',
										panels: {
											tools: {
												dock: 'right'
											}
										}
									}
								}
								tools={
									{
										image: {
											properties: {
												src: {
													value: {
														url: 'https://via.placeholder.com/500x100?text=IMAGE',
														width: 500,
														height: 100
													}
												}
											}
										}
									}
								}
								options={
									{
										features: {
											userUploads: true
										}
									}
								}
							/>
						</div>
					</div>
					<div className="btns-holder right-btns d-flex flex-row-reverse pt-5 flex-gap">
						<Link to={void (0)} onClick={saveDesign} className="btn btn-primary ml-2 mb-3"><span>{templateId ? t('Update') : t('Create')}</span></Link>
						<Link
							onClick={() => goBack(true)}
							className="btn btn-secondary ms-3 mb-3"
						>
							<span>{t('Back')}</span>
						</Link>
					</div>
				</div>
				<div id="capture">
				</div>
			</Container>
		</React.Fragment >
	);
}
export default withTranslation()(CreateEmailTemplate);