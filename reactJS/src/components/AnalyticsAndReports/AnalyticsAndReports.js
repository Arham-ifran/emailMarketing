import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { Container, Row, Col, Tabs, Tab } from 'react-bootstrap';

import './AnalyticsAndReports.css';
function AnalyticsAndReports(props) {
	return (
		<React.Fragment>
			<Container fluid>
				<Tabs defaultActiveKey="email-campaigns" id="main-tabs" className="em-tabs mb-3">
					<Tab eventKey="email-campaigns" title="Email Campaigns">
						<Row>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Campaign Sent</span>
									<span class="value">900</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Click Rate</span>
									<span class="value">800</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Open Rate</span>
									<span class="value">500</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Subscriber</span>
									<span class="value">600</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Unsubscriber</span>
									<span class="value">50</span>
								</div>
							</Col>
						</Row>
						<Tabs defaultActiveKey="open-by-time" id="email-campaign-sub-tabs" className="em-tabs mb-3">
							<Tab eventKey="open-by-time" title="Open By Time">
								<p>Email Campaigns - Open By Time</p>
							</Tab>
							<Tab eventKey="open-by-location" title="Open by Location">
								<p>Email Campaigns - Open By Location</p>
							</Tab>
						</Tabs>
					</Tab>
					<Tab eventKey="sms-campaigns" title="SMS Campaigns">
						<Tabs defaultActiveKey="open-by-time" id="sms-campaign-sub-tabs" className="em-tabs mb-3">
							<Tab eventKey="open-by-time" title="Open By Time">
								<p>SMS Campaigns - Open By Time</p>
							</Tab>
							<Tab eventKey="open-by-location" title="Open by Location">
								<p>SMS Campaigns - Open By Location</p>
							</Tab>
						</Tabs>
					</Tab>
					<Tab eventKey="split-testing" title="Split Testing">
						<Row>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Email Sent</span>
									<span class="value">900</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Click Rate</span>
									<span class="value">800</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Open Rate</span>
									<span class="value">500</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Subscriber</span>
									<span class="value">600</span>
								</div>
							</Col>
							<Col xxl="3" xl="4" lg="6" md="4" sm="6" xs="12">
								<div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
									<span class="title">Total Unsubscriber</span>
									<span class="value">50</span>
								</div>
							</Col>
						</Row>
						<Tabs defaultActiveKey="open-by-time" id="split-testing-sub-tabs" className="em-tabs mb-3">
							<Tab eventKey="open-by-time" title="Open By Time">
								<p>Split Testing - Open By Time</p>
							</Tab>
							<Tab eventKey="open-by-location" title="Open by Location">
								<p>Split Testing - Open By Location</p>
							</Tab>
						</Tabs>
					</Tab>
					<Tab eventKey="subscribers-lists" title="Subscribers Lists">
						<p>Subscribers List</p>
					</Tab>
				</Tabs>
			</Container>
		</React.Fragment>
	);
}

export default AnalyticsAndReports;