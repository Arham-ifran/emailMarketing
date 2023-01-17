import React, { useState, useEffect, useRef } from "react";
import { Link, useHistory } from "react-router-dom";
import Select from "react-select";
import { Container, Form } from "react-bootstrap";
import Spinner from "../includes/spinner/Spinner";
import { faSync } from "@fortawesome/free-solid-svg-icons";
import { faEye } from "@fortawesome/free-solid-svg-icons";
import { faCopy } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faDownload } from "@fortawesome/free-solid-svg-icons";
import "./AllApis.css";
import AddUrlsInput from "./AddUrlsInput";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { withTranslation } from "react-i18next";
import Tooltip from "react-bootstrap/Tooltip";
import OverlayTrigger from "react-bootstrap/OverlayTrigger";
import Swal from 'sweetalert2';

function AllApis(props) {

	const { t } = props;
	const notify = () => toast.success(t("Text copied to clipboard"));
	const history = useHistory();
	const childRef = useRef();
	const [loading, setLoading] = useState(false);
	const [name, setName] = useState("");
	const [email, setEmail] = useState("");
	const [endpoint_urls, setEndpoint_urls] = useState([]);
	const [secret_key, setSecret_key] = useState("");
	const [api_token, setApi_token] = useState("");
	const [hidden_token, setHidden_token] = useState("");
	const [status, setStatus] = useState(2);
	const [refresh, setRefresh] = useState(false);
	const [errors, setErrors] = useState([]);

	useEffect(() => {
		const getApiData = () => {
			setLoading(true);
			axios
				.get("/api/get-apidata?lang=" + localStorage.lang)
				.then((response) => {
					setLoading(false);
					// console.log(response.data.data);
					const data_received = response.data.data;
					setName(data_received.name);
					setEmail(data_received.email);
					setApi_token(data_received.api_token);
					var l = data_received.api_token.length;

					setEndpoint_urls(data_received.endpoint_urls);
					// console.log(data_received.endpoint_urls);
					setSecret_key(data_received.secret_key);
					setStatus(data_received.api_status);
				})
				.catch((error) => {
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
					setLoading(false);
				});
		};
		getApiData();
	}, [refresh]);

	const handleSubmit = (event) => {
		event.preventDefault();
		setErrors([])

		var unsub = document.getElementById('add_urls').value;

		if (!unsub) {
			setLoading(true);

			const data = {
				endpoint_urls: endpoint_urls,
				api_status: status,
			};
			axios
				.post("/api/update-apidata?lang=" + localStorage.lang, data)
				.then((res) => {
					setLoading(false);
					setRefresh(!refresh);
					Swal.fire({
						title: t('Success'),
						text: t('Your Api has been updated successfully!'),
						icon: 'success',
						showCancelButton: false,
						confirmButtonText: 'OK',
					})
				})
				.catch((error) => {
					setLoading(false);
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
				});
		}
		else {
			setErrors({
				clear_input: [t('type_or_paste_url')],
			});
		}
	};

	const refreshKey = () => {
		setLoading(true);
		axios
			.get("/api/refresh-api-key?lang=" + localStorage.lang)
			.then((res) => {
				setLoading(false);
				setRefresh(!refresh);
				Swal.fire({
					title: t("Success"),
					text: t("Your Api Key has been Refreshed!"),
					icon: "success",
					showCancelButton: false,
					confirmButtonText: "OK",
				});
			})
			.catch((error) => {
				setLoading(false);
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
			});
	};

	const refreshToken = () => {
		setLoading(true);
		axios
			.get("/api/refresh-api-token?lang=" + localStorage.lang)
			.then((res) => {
				setLoading(false);
				setRefresh(!refresh);
				Swal.fire({
					title: t("Success"),
					text: t("Your Api Token has been Refreshed!"),
					icon: "success",
					showCancelButton: false,
					confirmButtonText: "OK",
				});
			})
			.catch((error) => {
				setLoading(false);
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
			});
	};

	function showSecret() {
		var x = document.getElementById("secret");
		x.type = "text";
	}
	function hideSecret() {
		var x = document.getElementById("secret");
		x.type = "password";
	}
	function copySecret() {
		navigator.clipboard.writeText(secret_key);
		notify();
	}
	function showToken() {
		var x = document.getElementById("token");
		x.type = "text";
	}
	function hideToken() {
		var x = document.getElementById("token");
		x.type = "password";
	}
	function copyToken() {
		navigator.clipboard.writeText(api_token);
		notify();
	}

	const downloadDocument = () => {
		setLoading(true);
		axios
			.get("/api/download-api-doc?lang=" + localStorage.lang, {
				responseType: "blob",
			})
			.then((res) => {
				setLoading(false);
				var fileName = "api-documentation.pdf";
				var a = document.createElement("a");
				var json = res.data,
					blob = new Blob([json], { type: "octet/stream" }),
					url = window.URL.createObjectURL(blob);
				a.href = url;
				a.download = fileName;
				a.click();
				window.URL.revokeObjectURL(url);
			})
			.catch((error) => {
				setLoading(false);
			});
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

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<ToastContainer />
			<Container fluid>
				<div className="mt-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>{t("APIs")}</h1>
					</div>

					<div className="add-contact">
						<Link
							onClick={downloadDocument}
							className="btn btn-secondary"
						>
							<span>
								{t("Download Documentation")}{" "}
								<FontAwesomeIcon
									icon={faDownload}
									className="ms-2"
								/>
							</span>
						</Link>
					</div>
				</div>

				<Form
					className="create-form-holder rounded-box-shadow bg-white"
					onSubmit={handleSubmit}
				>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label htmlFor="contact-name">
							{t("Name")}
						</Form.Label>
						<div className="flex-fill input-holder">
							{/* <input id="contact-name" value= className="form-control" type="text" /> */}
							<p>{name}</p>
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label htmlFor="email">{t("Email")}</Form.Label>
						<div className="flex-fill input-holder">
							{/* <input id="email" value={email} className="form-control" type="text" /> */}
							<p>{email}</p>
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label htmlFor="country">
							{t("Callback Urls")}
						</Form.Label>
						<div className="flex-fill input-holder">
							<div className="form-group w-100">
								<AddUrlsInput
									ref={childRef}
									parentUrls={endpoint_urls}
									changeEndpointUrls={(val) =>
										setEndpoint_urls(val)
									}
									clearErrors={() => setErrors({})}
								/>
							</div>
							{renderErrorFor('clear_input')}
							{/* <input id="url" onChange={(e) => setEndpoint_urls(e.target.value)} value={endpoint_urls} className="form-control" type="text" /> */}
						</div>
					</Form.Group>
					<Form.Group className="mb-3 mb-md-4 d-flex">
						<Form.Label htmlFor="country">
							{t("Secret Key")}
						</Form.Label>
						<div className="flex-fill input-holder apis-icon-btn">
							<input
								id="secret"
								value={secret_key}
								disabled
								className="form-control text-break"
								type="password"
							/>
							<button type="button" onClick={refreshKey} title="refresh">

								<FontAwesomeIcon
									icon={faSync}
									className="ms-2"
								/>
							</button>
							<button type="button" title="show"
								onMouseDown={showSecret}
								onMouseUp={hideSecret}
							>

								<FontAwesomeIcon
									icon={faEye}
									className="ms-2"
								/>
							</button>
							<button type="button" onClick={copySecret} title="copy">

								<FontAwesomeIcon
									icon={faCopy}
									className="ms-2"
								/>
							</button>
						</div>
					</Form.Group>
					<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label className="mb-2 mb-md-0" htmlFor="token">
							{t("Api Token")}
						</Form.Label>
						<div className="flex-fill input-holder  apis-icon-btn">
							<input
								id="token"
								value={api_token}
								rows="2"
								disabled
								className="form-control"
								type="password"
							/>
							<div className="w-100 d-flex align-items-right">
								<button type="button" onClick={refreshToken} title="refresh">

									<FontAwesomeIcon
										icon={faSync}
										className="ms-2"
									/>
								</button>
								<button type="button" title="show"
									onMouseDown={showToken}
									onMouseUp={hideToken}
								>

									<FontAwesomeIcon
										icon={faEye}
										className="ms-2"
									/>
								</button>
								<button type="button" onClick={copyToken} title="copy">

									<FontAwesomeIcon
										icon={faCopy}
										className="ms-2"
									/>
								</button>
							</div>
							{/* <textarea id="ssd" type="password" value={api_token} disabled rows="2" className="form-control" />
							<span onClick={refreshToken}> <FontAwesomeIcon icon={faSync} className="ms-2"/></span> */}
						</div>
					</Form.Group>
					<Form.Group className="align-items-center mb-2 mb-md-4 d-flex flex-column flex-md-row">
						<Form.Label
							className="mb-2 mb-md-0"
							htmlFor="reply-to-address"
						>
							{t("Status")}
						</Form.Label>
						<div className="flex-fill  input-holder radio-btns-holder d-flex">
							<div className="radio-holder mr-2">
								<label className="custom-radio">
									{t("Active")}
									<input
										type="radio"
										name="status"
										value="1"
										onChange={() => setStatus(1)}
										checked={status == 1 ? true : false}
									/>
									<span className="checkmark"></span>
								</label>
							</div>
							<div className="radio-holder mr-2">
								<label className="custom-radio">
									{t("Disabled")}
									<input
										type="radio"
										name="status"
										value="2"
										onChange={() => setStatus(2)}
										checked={status == 2 ? true : false}
									/>
									<span className="checkmark"></span>
								</label>
							</div>
						</div>
					</Form.Group>
					<div className="btns-holder right-btns d-flex flex-row-reverse">
						<button
							className="btn btn-primary ms-3 mb-3"
							type="submit"
						>
							<span>{t("Update")}</span>
						</button>
						<button
							className="btn btn-secondary ms-3 mr-2 mb-3"
							onClick={() => history.goBack()}
						>
							<span>{t("Back")}</span>
						</button>
					</div>
					<div
						class="toast align-items-center text-white bg-primary border-0"
						role="alert"
						aria-live="assertive"
						aria-atomic="true"
					>
						<div class="d-flex">
							<div class="toast-body">
								{t("Hello, world! This is a toast message.")}
							</div>
							<button
								type="button"
								class="btn-close btn-close-white me-2 m-auto"
								data-bs-dismiss="toast"
								aria-label="Close"
							></button>
						</div>
					</div>
				</Form>
			</Container>
		</React.Fragment>
	);
}

export default withTranslation()(AllApis);
