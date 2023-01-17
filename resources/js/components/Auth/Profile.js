import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { Container, Form, Button } from 'react-bootstrap';
import { faEye } from '@fortawesome/free-solid-svg-icons'
import { faEyeSlash } from '@fortawesome/free-solid-svg-icons'
import SiteLogo from '../../assets/images/main-logo.svg';
import Spinner from '../includes/spinner/Spinner';
import Swal from 'sweetalert2';
import ProfilePic from '../../assets/images/user.png';
import Select from 'react-select';
import { withTranslation } from 'react-i18next';

var countries = [];
var timezones = [];
function Profile(props) {
	const { t } = props;
	// const countries = [{value:0, label:'sdfg'}];
	var zero = 0;
	const [name, setName] = useState('');
	const [email, setEmail] = useState('');
	const [street, setStreet] = useState('');
	const [city, setCity] = useState('');
	const [country, setCountry] = useState('');
	const [timezone, setTimezone] = useState('');
	const [postcode, setPostcode] = useState(zero);
	const [profileImagePath, setProfileImagePath] = useState('');
	const [profileImage, setProfileImage] = useState('');
	const [oldPassword, setOldPassword] = useState('');
	const [password, setPassword] = useState('');
	const [passwordConfirmation, setPasswordConfirmation] = useState('');
	const [chooseFileName, setChooseFileName] = useState();
	const [showPassword, setShowPassword] = useState(false);
	const [showVerifyPassword, setShowVerifyPassword] = useState(false);
	const [showNewPassword, setShowNewPassword] = useState(false);
	const [errors, setErrors] = useState([]);
	const [loading, setLoading] = useState(false);
	const [disabled, setDisabled] = useState(false);


	useEffect(async () => {

		await axios.get('/api/get-countries?lang=' + localStorage.lang)
			.then(async (response) => {
				let data = response.data.countries;
				await data.map(country => (
					countries.push({ value: country.id, code: country.code, label: country.name })
				))
				setLoading(false);
			})
			.catch(error => {
				setLoading(false);
			})
		setLoading(true);
		await axios.get('/api/timezones?lang=' + localStorage.lang)
			.then(async (response) => {
				let data = response.data.data;
				await data.map(zone => (
					timezones.push({ value: zone.name, label: zone.name })
				))
				setLoading(false);
			})
			.catch(error => {
				setLoading(false);
			})

		setLoading(true);
		await axios.get('/api/auth/profile?lang=' + localStorage.lang)
			.then(async (response) => {
				let data = response.data.data;

				setName(data.name ? data.name : '');
				setEmail(data.email);
				setStreet(data.street ? data.street : '');
				setCity(data.city ? data.city : '');
				setPostcode(isNaN(data.zip_code) ? 0 : data.zip_code);
				setProfileImagePath(data.profile_image_path);

				setCountry(data.country_id)
				setTimezone(data.timezone)
				setLoading(false);

			})
			.catch(error => {
				setLoading(false);
			})

		setLoading(false);
	}, []);


	const handleFieldChange = (event) => {
		if (event.target.type === 'file') {
			const file = event.target.files[0];
			if (file) {
				const reader = new window.FileReader();
				reader.readAsDataURL(file);
				reader.onloadend = () => {
					setProfileImagePath(reader.result);
					setProfileImage(file);
					setChooseFileName(file.name);
				};
			}
		}
	}

	const handleFormSubmit = (event) => {
		event.preventDefault()

		setLoading(true);
		setDisabled(true);
		setErrors([])

		let formData = new FormData();
		if (profileImage) {
			formData.append("profile_image", profileImage);
		}

		if (name == "" && country == "") {
			setErrors({
				name: [name == "" ? t('required') : ''],
				country_id: [country == "" ? t('required') : ''],
			});
			setLoading(false);
			setDisabled(false);
			return;
		}

		formData.append("name", name);
		formData.append("email", email);
		formData.append("street", street);
		formData.append("city", city);
		formData.append("zip_code", postcode);
		formData.append("country_id", country);
		formData.append("timezone", timezone);
		formData.append("old_password", oldPassword);
		formData.append("password", password);
		formData.append("password_confirmation", passwordConfirmation);

		const headers = {
			"content-type": "multipart/form-data"
		}

		axios.post('/api/auth/update-profile?lang=' + localStorage.lang, formData, { "headers": headers })
			.then(res => {

				setDisabled(false);
				setLoading(false);

				if (res.response) {
					if (res.response.data.errors) {
						setErrors(res.response.data.errors);
					}
				} else {
					if (res.data.status) {
						setProfileImagePath(res.data.data.profile_image_path);
						localStorage["user_name"] = res.data.data.name;
						localStorage["profile_image_path"] = res.data.data.profile_image_path;

						// Swal.fire({
						// 	//title: 'Are you sure?',
						// 	text: res.data.message,
						// 	icon: 'success',
						// 	showCancelButton: false,
						// 	confirmButtonText: t('OK'),
						// 	//cancelButtonText: 'No, keep it'
						// }).then((result) => {
						window.location.replace('/dashboard');
						// })
					}
					else {
					}
				}
			})
			.catch(error => {
				setDisabled(false);
				setLoading(false);
				if (error.response.data.errors) {
					setErrors(error.response.data.errors);
				}
			})

	}


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

	const handleCountryChange = (selectedOption) => {
		// console.log(selectedOption);
		// console.log(selectedOption.code);
		setCountry(selectedOption.value);
		localStorage["country"] = selectedOption.code;
	}

	const handleTimezoneChange = (selectedOption) => {
		setTimezone(selectedOption.value);
		localStorage["timezone"] = selectedOption.value;
	}

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>{t('Update Profile')}</h1>
					</div>
				</div>
				<Form autoComplete="off" className="profile-pg create-form-holder profile-form rounded-box-shadow bg-white d-flex flex-row align-items-center" onSubmit={handleFormSubmit}>
					<div className="mobile-style profile-image-wrap">
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
							<Form.Label className="mb-2 text-center w-100" htmlFor="profile_image">{t('Upload Image')}</Form.Label>
							<div className="upload-img-design">
								<img src={profileImagePath ? profileImagePath : ProfilePic} className="img-responsive text-center profile-img" alt="" width="50%" />
							</div>
							<label htmlFor="file" className="btn btn-primary "><span>{t('Choose File')}</span></label>
							<span className="text-center overflow-scroll"> {chooseFileName}</span>
							<input style={{ visibility: "hidden", height: 0 }} id="file" type="file" className="form-control" onChange={handleFieldChange} accept=".jpg, .jpeg, .png, .svg" />
							{
								renderErrorFor('profile_image')
							}
						</Form.Group>
					</div>
					<div className="desktop-style profile-image-wrap">
						<Form.Group className="mb-2 mb-md-4 d-flex flex-column">
							<Form.Label className="mb-2 text-center w-100" htmlFor="profile_image">{t('Upload Image')}</Form.Label>
							<div className="upload-img-design">
								<img src={profileImagePath ? profileImagePath : ProfilePic} className="img-responsive text-center profile-img" alt="" width="50%" />
							</div>
							<label htmlFor="file" className="btn btn-primary "><span>{t('Choose File')}</span></label>
							<span className="text-center overflow-scroll"> {chooseFileName}</span>
							<input style={{ visibility: "hidden", height: 0 }} id="file" type="file" className="form-control" onChange={handleFieldChange} accept=".jpg, .jpeg, .png, .svg" />
							{
								renderErrorFor('profile_image')
							}
						</Form.Group>
					</div>
					<div className="profile-content pt-3">

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label htmlFor="email">{t('Email')}</Form.Label>
							<div className="flex-fill input-holder">
								{/* <p>{email}</p> */}
								<input type="text" disabled class="form-control " name="email" placeholder={t('Email Address')} value={email}></input>
							</div>
						</Form.Group>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="name">{t('Full Name')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<input id="name" className="form-control" type="text" onChange={(e) => setName(e.target.value)} value={name} />
								{
									renderErrorFor('name')
								}
							</div>
						</Form.Group>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="country_id">{t('Country')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<div className="subscriber-select w-100">
									<Select
										onChange={(e) => handleCountryChange(e)}
										options={countries}
										classNamePrefix="react-select"
										value={countries.find(o => o.value == parseInt(country))}
										placeholder={t('Select Country')}
										autoComplete="off"
									/>
									{renderErrorFor('country_id')}
								</div>
							</div>
						</Form.Group>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="country_id">{t('Timezone')} <b className="req-sign">*</b></Form.Label>
							<div className="flex-fill input-holder">
								<div className="subscriber-select w-100">
									<Select
										onChange={(e) => handleTimezoneChange(e)}
										options={timezones}
										classNamePrefix="react-select"
										value={timezones.find(o => o.value == timezone)}
										placeholder={t('select_timezone')}
										autoComplete="off"
									/>
									{
										renderErrorFor('timezone')
									}
								</div>
							</div>
						</Form.Group>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="city">{t('City')}</Form.Label>
							<div className="flex-fill input-holder">
								<input id="city" className="form-control" type="text" onChange={(e) => setCity(e.target.value)} value={city} placeholder="e.g. Braunschweig" />
								{
									renderErrorFor('city')
								}
							</div>
						</Form.Group>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="street">{t('Street')}</Form.Label>
							<div className="flex-fill input-holder">
								<input id="street" className="form-control" type="text" onChange={(e) => setStreet(e.target.value)} value={street} placeholder="e.g. Erftstraße 15" />
								{
									renderErrorFor('street')
								}
							</div>
						</Form.Group>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row">
							<Form.Label className="mb-2 mb-md-0" htmlFor="postcode">{t('Zip Code')}</Form.Label>
							<div className="flex-fill input-holder">
								<input id="postcode" name="zip_code" className="form-control" type="number" onChange={(e) => setPostcode(e.target.value)} value={postcode} placeholder="e.g. 12345" />
								{
									renderErrorFor('zip_code')
								}
							</div>
						</Form.Group>


						{/* for autofill */}
						{/* <div style={{ maxHeight: "0px" }} className="hidden">
							<input id="email" name='email' style={{ maxHeight: "0px", opacity: '0' }} type="email" />
							<input id="pass" name='passwort' style={{ maxHeight: "0px", opacity: '0' }} type="password" />
						</div> */}

						<h5 className="my-4">{t('Change password')}:<u></u> </h5>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row position-relative password-field-wrap">
							<Form.Label className="mb-2 mb-md-0" htmlFor="name">{t('Old Password')}</Form.Label>
							<span className="eye-view-icon">
								<FontAwesomeIcon icon={faEye} className={showPassword ? "no-view-icon" : "view-icon"} onClick={() => setShowPassword(true)} />
								<FontAwesomeIcon icon={faEyeSlash} className={showPassword ? "view-icon" : "no-view-icon"} onClick={() => setShowPassword(false)} />
							</span>
							<div className="flex-fill input-holder">
								<input id="name" name="pass" className="form-control" type={showPassword ? "text" : "password"} onChange={(e) => setOldPassword(e.target.value)} value={oldPassword} />
								{renderErrorFor('old_password_incorrect')}
								{renderErrorFor('old_password')}
							</div>
						</Form.Group>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row position-relative password-field-wrap">
							<Form.Label className="mb-2 mb-md-0" htmlFor="name">{t('New Password')}</Form.Label>
							<span className="eye-view-icon">
								<FontAwesomeIcon icon={faEye} className={showNewPassword ? "no-view-icon" : "view-icon"} onClick={() => setShowNewPassword(true)} />
								<FontAwesomeIcon icon={faEyeSlash} className={showNewPassword ? "view-icon" : "no-view-icon"} onClick={() => setShowNewPassword(false)} />
							</span>
							<div className="flex-fill input-holder">
								<input id="name" className="form-control" type={showNewPassword ? "text" : "password"} onChange={(e) => setPassword(e.target.value)} value={password} />
								{
									renderErrorFor('password')
								}
							</div>
						</Form.Group>

						<Form.Group className="mb-2 mb-md-4 d-flex flex-column flex-md-row position-relative password-field-wrap">
							<Form.Label className="mb-2 mb-md-0" htmlFor="name">{t('Confirm Password')}</Form.Label>
							<span className="eye-view-icon">
								<FontAwesomeIcon icon={faEye} className={showVerifyPassword ? "no-view-icon" : "view-icon"} onClick={() => setShowVerifyPassword(true)} />
								<FontAwesomeIcon icon={faEyeSlash} className={showVerifyPassword ? "view-icon" : "no-view-icon"} onClick={() => setShowVerifyPassword(false)} />
							</span>
							<div className="flex-fill input-holder">
								<input id="name" className="form-control" type={showVerifyPassword ? "text" : "password"} onChange={(e) => setPasswordConfirmation(e.target.value)} value={passwordConfirmation} />
								{
									renderErrorFor('password_confirmation')
								}
							</div>
						</Form.Group>

						<div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-2">
							<Button type="submit" disabled={disabled} className="btn btn-primary ms-3 mb-3">
								<span>{t('Update')}</span>
							</Button>
						</div>
					</div>
				</Form>
			</Container>
		</React.Fragment >
	)





}

//import React, { Component } from 'react'
//import AuthHeader from "../sections/AuthHeader"
//import swal from 'sweetalert'
//import Select from 'react-select'
//import Spinner from '../spinner/Spinner'


export default withTranslation()(Profile)