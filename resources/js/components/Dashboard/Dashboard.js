import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Modal, Button } from 'react-bootstrap';
import { Link } from "react-router-dom";
import NoCampaign from '../../assets/images/no-campaign-found.svg';
import Select from 'react-select';
import Spinner from "../includes/spinner/Spinner";
import { LineChart, PieChart, BarChart, Legend, Bar, Pie, Line, Cell, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';
import './Dashboard.css';
import GetUserPackage from '../Auth/GetUserPackage';
import { withTranslation } from 'react-i18next';
function Dashboard(props) {
	const { t } = props;
	const [loading, setLoading] = useState(false);
	const [contacts, setContacts] = useState("");
	const [templates, setTemplates] = useState("");
	const [lists, setLists] = useState("");
	const [subscribers, setSubscribers] = useState("");
	const [unsubscribers, setUnsubscribers] = useState("");
	const [smsData, setSmsData] = useState([]);
	const [emailData, setEmailData] = useState([]);
	const [splitData, setSplitData] = useState([]);
	const [splitCampaigns, setSplitCampaigns] = useState([]);
	const [selectedSplit, setSelectedSplit] = useState(0);
	const [smsCampaignsData, setSmsCampaignsData] = useState([]);
	const [emailCampaignsData, setEmailCampaignsData] = useState([]);
	const [selectedOption, setSelectedOption] = useState([]);

	const [userPackage, setUserPackage] = useState({});
	const [canSplitTest, setCanSplitTest] = useState(0);

	useEffect(() => {
		if (localStorage.after_login) {
			window.location.href = "/packages/payment-checkout";
			localStorage["after_login"] = false;
		}
		const chartData = () => {
			setLoading(true);
			axios
				.get("/api/get-dashboard-data?lang=" + localStorage.lang)
				.then((response) => {
					setLoading(false);
					const data_received = response.data;

					setContacts(data_received.contacts);
					setLists(data_received.lists);
					setTemplates(data_received.templates);
					setSubscribers(data_received.subscribers);
					setUnsubscribers(data_received.unsubscribers);
					setSmsData(data_received.smsData);
					setEmailData(data_received.emailData);
					setSplitData(data_received.splitData);
					var splitCampaigns = []
					data_received.splitCampaigns.map((campaign, index) => {
						splitCampaigns.push({ value: index, label: campaign })
					});
					setSplitCampaigns(splitCampaigns);
					setSelectedOption(splitCampaigns[0]);
					setSmsCampaignsData(data_received.smsCampaignsData);
					setEmailCampaignsData(data_received.emailCampaignsData);
				})
				.catch((error) => {
					if (error.response.data.errors) {
						setErrors(error.response.data.errors);
					}
					setLoading(false);
				});
		};
		chartData();
	}, [])
	useEffect(() => {
		const load = () => {
			if (userPackage != {}) {
				if (userPackage.features) {
					if (Object.keys(userPackage.features).findIndex(val => val === "12") >= 0) { // split allowed
						// console.log(Object.values(userPackage.features)[Object.keys(userPackage.features).findIndex(val => val === "12")])
						setCanSplitTest(true)
					} else {
						setCanSplitTest(false)
					}
				}
			}
		}
		load();
	}, [userPackage])

	const monthNames = [t("January"), t("February"), t("March"), t("April"), t("May"), t("June"),
	t("July"), t("August"), t("September"), t("October"), t("November"), t("December")
	];
	// console.log(monthNames);

	const d = new Date();
	// console.log(d);
	// console.log(d.getMonth());
	console.log(` ${monthNames[d.getMonth()]}`);

	const colors = ['#24E096', '#0088FE', '#FFBB28', '#ef5100', '#a0a7b7', '#481C3C'];
	const dummyEmailData = [{}]
	dummyEmailData[0][t('name')] = ` ${monthNames[d.getMonth()]}`;
	dummyEmailData[0][t('Open Rate')] = 0;
	dummyEmailData[0][t('Link Clicks')] = 0;
	// "name": monthNames[d.getMonth()],
	// "Open Rate": 0,
	// "Link Clicks": 0

	const dummySmsData = [{}]
	dummySmsData[0][t('name')] = ` ${monthNames[d.getMonth()]}`;
	dummySmsData[0][t('sent')] = 0;
	dummySmsData[0][t('failed')] = 0;
	// {
	// 	"name": monthNames[d.getMonth()],
	// 	"sent": 0,
	// 	"failed": 0
	// }

	const emptyPie = [
		{ name: t('Total Campaigns'), value: 0.01 },
	];

	const dummySplitData = [{}, {}]
	dummySplitData[0][t('name')] = t("Section 1");
	dummySplitData[0][t('Open Rate')] = 0;
	dummySplitData[0][t('Link Clicks')] = 0;
	dummySplitData[1][t('name')] = t("Section 2");
	dummySplitData[1][t('Open Rate')] = 0;
	dummySplitData[1][t('Link Clicks')] = 0;
	// const dummySplitData = [
	// 	{
	// 		"name": t("Section 1"),
	// 		"Open Rate": 0.00,
	// 		"Link Clicks": 0.00
	// 	},
	// 	{
	// 		"name": t("Section 2"),
	// 		"Open Rate": 0.00,
	// 		"Link Clicks": 0.00
	// 	}
	// ]

	const [show, setShow] = useState(false);
	const handleClose = () => {
		setShow(false);
	};
	const handleShow = (e) => {
		setShow(true);
	};

	return (
		<React.Fragment>
			{loading ? <Spinner /> : null}
			<GetUserPackage parentCallback={(data) => { setUserPackage(data); }} />
			{(emailCampaignsData[0] && emailCampaignsData[0].value == 0 && smsCampaignsData[0] && smsCampaignsData[0].value == 0 && splitCampaigns.length == 0) ?
				<div className="no-campaign-img">
					<strong className="logo w-100 d-flex justify-content-center">
						<img className="img-fluid" src={NoCampaign} alt="" />
					</strong>
					<p className="text-uppercase">{t('No campaigns found')}</p>
					<div className="text-center">
						<a
							className="btn btn-primary mt-2 text-uppercase"
							onClick={() => setShow(true)}
						>
							<span>{t('Add Camapaign')}</span>
						</a>
					</div>
				</div>
				: ""}
			<Container fluid className={(emailCampaignsData[0] && emailCampaignsData[0].value == 0 && smsCampaignsData[0] && smsCampaignsData[0].value == 0 && splitCampaigns.length == 0) ? "campaign-overlay-content" : ""}>
				<Row>
					<Col xl="4" xs="12">
						<div className="graph-box bg-white rounded-box-shadow">
							<h3 className="mb-3">{t('Email Campaigns')}</h3>
							<Row>
								<Col xl="7" xs="7" className="p-0">
									<div className="campaign-graph-holder">
										{emailCampaignsData[0] ? emailCampaignsData[0].value == 0 ?
											<ResponsiveContainer width="100%" height={191}>
												<PieChart>
													<Pie dataKey="value" isAnimationActive={false} dataKey="value" nameKey="name" data={emptyPie} fill="#24E096">
														{emptyPie.map((entry, index) => <Cell key={`cell-${index}`} fill={colors[index % colors.length]} />)}
													</Pie>
													<Tooltip />
												</PieChart>
											</ResponsiveContainer>
											:
											<ResponsiveContainer width="100%" height={191}>
												<PieChart>
													<Pie dataKey="value" isAnimationActive={false} dataKey="value" nameKey="name" data={emailCampaignsData.slice(1, emailCampaignsData.length)} fill="#82ca9d">
														{emailCampaignsData.slice(1, emailCampaignsData.length).map((entry, index) => <Cell key={`cell-${index}`} fill={colors[index % colors.length]} />)}
													</Pie>
													<Tooltip />
												</PieChart>
											</ResponsiveContainer>
											: ""}
										<div className='text-center'>
											{t('Total') + ": " + emailCampaignsData.slice(0, 1).map(val => (val.value))}
										</div>
									</div>
								</Col>
								<Col xl="5" xs="5" className="p-0">
									<ul className="list-unstyled snooze-des dashed">
										{emailCampaignsData.slice(1, emailCampaignsData.length).map((data, index) => (<React.Fragment key={index}> <li style={{ color: colors[index % colors.length] }} > <span style={{ color: '#6E7684' }}> {data.name} </span> </li> </React.Fragment>))}
									</ul>
								</Col>
							</Row>
							{/* {emailCampaignsData[0] ? emailCampaignsData[0].value == 0 ?
							<small>0 campaigns found. this is sample chart.</small>
							: "" :""} */}
						</div>
					</Col>

					<Col xl="8" xs="12">
						<div className="graph-box bg-white rounded-box-shadow">
							<h3>{t('Click')} {'&'} {t('Open Rate')}</h3>
							{/* <small className="mb-3" > This year </small> */}
							<div className="campaign-graph-holder">
								<ResponsiveContainer width="100%" height={220}>
									<LineChart width={100} height={100} data={emailData.length ? emailData : dummyEmailData} isAnimationActive={true} >
										<CartesianGrid strokeDasharray="3 3" />
										<XAxis dataKey="name" data />
										<YAxis />
										<fromZero />
										<Tooltip />
										<Line type="monotone" dataKey={t("Open Rate")} stroke="#24E096" />
										<Line type="monotone" dataKey={t("Link Clicks")} stroke="#1D2579" />
									</LineChart>
								</ResponsiveContainer>
							</div>
						</div>
					</Col>
				</Row>
				<Row>
					<Col xl="4" xs="12">
						<div className="graph-box bg-white rounded-box-shadow">
							<h3 className="mb-3">{t('SMS Campaigns')}</h3>
							<Row>
								<Col xl="7" xs="7" className="p-0">
									<div className="campaign-graph-holder">
										{smsCampaignsData[0] ? smsCampaignsData[0].value == 0 ?
											<ResponsiveContainer width="100%" height={191}>
												<PieChart>
													<Pie dataKey="value" isAnimationActive={false} dataKey="value" nameKey="name" data={emptyPie} fill="#24E096">
														{emptyPie.map((entry, index) => <Cell key={`cell-${index}`} fill={colors[index % colors.length]} />)}
													</Pie>
													<Tooltip />
												</PieChart>
											</ResponsiveContainer>
											:
											<ResponsiveContainer width="100%" height={191}>
												<PieChart>
													<Pie dataKey="value" isAnimationActive={false} dataKey="value" nameKey="name" data={smsCampaignsData.slice(1, smsCampaignsData.length)} fill="#82ca9d">
														{smsCampaignsData.slice(1, smsCampaignsData.length).map((entry, index) => <Cell key={`cell-${index}`} fill={colors[index % colors.length]} />)}
													</Pie>
													<Tooltip />
												</PieChart>
											</ResponsiveContainer>
											: ""}
										<div className='text-center'>
											{t('Total') + ": " + smsCampaignsData.slice(0, 1).map(val => (val.value))}
										</div>
									</div>
								</Col>
								<Col xl="5" xs="5" className="p-0">
									<ul className="list-unstyled snooze-des dashed">
										{smsCampaignsData.slice(1, smsCampaignsData.length).map((data, index) => (<React.Fragment key={index}> <li style={{ color: colors[index % colors.length] }} > <span style={{ color: '#6E7684' }}> {data.name} </span> </li> </React.Fragment>))}
									</ul>
								</Col>
							</Row>
						</div>
					</Col>
					<Col xl="8" xs="12">
						<div className="graph-box bg-white rounded-box-shadow">
							<h3>{t('Sending Rate')}</h3>
							{/* <small className="mb-3" > This year </small> */}
							<div className="campaign-graph-holder">
								<ResponsiveContainer width="100%" height={220}>
									<LineChart width={100} height={100} data={smsData.length ? smsData : dummySmsData} isAnimationActive={true} >
										<CartesianGrid strokeDasharray="3 3" />
										<XAxis dataKey="name" />
										<YAxis />
										<fromZero />
										<Tooltip />
										<Line type="monotone" dataKey={t("sent")} stroke="#24E096" />
										<Line type="monotone" dataKey={t("failed")} stroke="#1D2579" />
									</LineChart>
								</ResponsiveContainer>
							</div>
						</div>
					</Col>
				</Row>
				<Row>
					{canSplitTest ?
						<Col xxl="8" xl="6" xs="12">
							<div className="graph-box bg-white rounded-box-shadow count-box">
								<h3 className="mb-4">{t('Split')} {t('Click')} {'&'} {t('Open Rate')}</h3>
								{splitCampaigns.length ?
									<div className="subscriber-select w-100">
										<Select className="pb-3"
											onChange={(e) => { setSelectedSplit(e.value); setSelectedOption(e) }}
											options={splitCampaigns}
											value={selectedOption}
											classNamePrefix="react-select"
											placeholder={t("select_split_campaign")}
										/>
									</div>
									: ""}
								<ResponsiveContainer width="100%" height={splitCampaigns.length ? 235 : 280}>
									<BarChart width={730} height={220} data={splitData.length ? splitData[selectedSplit] : dummySplitData} barGap="40%" barSize="60%">
										<CartesianGrid strokeDasharray="3 3" />
										<XAxis dataKey="name" />
										<YAxis />
										<Tooltip />
										<Legend />
										<Bar dataKey={t("Open Rate")} fill="#1D2579" />
										<Bar dataKey={t("Link Clicks")} fill="#24E096" />
									</BarChart>
								</ResponsiveContainer>
							</div>
						</Col>
						: ""
					}
					<Col xxl={canSplitTest ? "4" : "12"} xl={canSplitTest ? "6" : "12"} xs="12">
						<div className="graph-box bg-white rounded-box-shadow count-box">
							<div className="stats-blocks d-flex">
								<div className="stat-block d-flex flex-column align-items-center text-center">
									<strong className="stat-value mb-2">{contacts}</strong>
									<span className="stat-name text-uppercase">{t('Total Contacts')}</span>
								</div>
								<div className="stat-block d-flex flex-column align-items-center text-center">
									<strong className="stat-value mb-2">{lists}</strong>
									<span className="stat-name text-uppercase">{t('Mailing Lists')}</span>
								</div>
							</div>
							<div className="stats-blocks d-flex">
								<div className="stat-block d-flex flex-column align-items-center text-center">
									<strong className="stat-value mb-2">{subscribers}</strong>
									<span className="stat-name text-uppercase">{t('Total Subscribers')}</span>
								</div>
								<div className="stat-block d-flex flex-column align-items-center text-center">
									<strong className="stat-value mb-2">{unsubscribers}</strong>
									<span className="stat-name text-uppercase">{t('Total Unsubscribers')}</span>
								</div>
							</div>
						</div>
					</Col>

				</Row>
			</Container>

			{/* Camapign Modal */}
			<Modal
				show={show}
				onHide={handleClose}
				className="em-modal bulk-dlt-modal nocampaign-modal"
				centered
			>
				<Modal.Body >
					<div className="multi-button d-flex align-items-center ">
						{contacts == 0 ? <Link to="/contacts">{t('add contact')}</Link> : null}
						{templates == 0 ? <Link to="/template/list">{t('add templates')}</Link> : null}
						{lists == 0 ? <Link to="/mailing-lists/create">{t('add mailing list')}</Link> : null}
						<Link to="/email-campaign/create">{t('add email campaign')}</Link>
						<Link to="/sms-campaign/create">{t('add sms campaign')}</Link>
						{canSplitTest ? <Link to="/split-testing/create">{t('Send Split Testing Email campaign')}</Link> : ""}
					</div>
				</Modal.Body>
			</Modal>
		</React.Fragment>
	);
}

export default withTranslation()(Dashboard);